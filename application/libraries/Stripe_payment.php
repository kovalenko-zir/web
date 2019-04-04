<?php

require 'vendor/stripe/stripe-php/init.php';

use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Token;
use Stripe\Charge;

class Stripe_payment
{
    protected $CI;

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->stripe_public_api_key = 'user_pubic_key';
        $this->stripe_secret_api_key = 'user_secret_key';
        $this->stripe_main = new Stripe;
        $this->stripe_main->setApiKey($this->stripe_public_api_key);
    }

    public $stripe_public_api_key;

    public $stripe_secret_api_key;

    public $stripe_main;

    public function pay_order($orderArray)
    {
        $this->stripe_main->setApiKey($this->stripe_secret_api_key);
        $order = new Charge;
        $order = $order->create($orderArray);
        return $order;
    }

    public function create_new_order($array)
    {
        $itemInfo = $this->CI->buy_model->retrieve_concrete_item_info($array['item_id']);
        $order = array();
        $order['shipping'] = $this->set_shipping_information_for_order($array);
        $order['receipt_email'] = $array['order_email'];
        $order['currency'] = 'usd';
        $order['amount'] = $array['order_cost'] * 100;
        $order['description']= $itemInfo['artName'];
        $order['source']= $this->CI->buy_model->retrieve_card_stripe_identifier($array['user_id'], $array['fourNum']);
        $order['customer']= $this->CI->buy_model->check_if_user_has_stripes_customer_id($array['user_id']);
        return $order;
    }

    public function set_shipping_information_for_order($array)
    {
        $shippingInfo = array();
        $countryCode = $this->CI->buy_model->country_code($array['order_country']);
        $shippingInfo['name'] = $array['first_name'] . ' ' . $array['last_name'];
        $shippingInfo['address'] = array(
            "country" => $countryCode,
            "city" => $array['order_city'],
            "line1" => $array['order_adress'],
            "line2" => $array['order_appartment'],
            "postal_code" => $array['order_zip_code']);
        return $shippingInfo;
    }

    public function received_credit_card_info_previous_processing($array)
    {
        $name = $array['cardholder_name'];
        $number = str_replace(' ', '', $array['card_number']);
        $month = substr($array['expiration_date'], 0, 2);
        $year = '20' . substr($array['expiration_date'], 2, 3);
        if (preg_match('/(0\d{1})/', $month) === 1) {
            $month = substr($month, 1, 1);
        }
        $cvv = $array['cvv'];
        $card = array(
            "name" => $name,
            "number" => $number,
            "exp_month" => $month,
            "exp_year" => $year,
            "cvc" => $cvv);
        $card = array('card' => $card);
        return $card;
    }

    public function retrieve_all_needles_for_database_card_information($cardObject)
    {
        $result_array = array(
            'stripeCardId' => $cardObject->id,
            'fourNum' => $cardObject->last4,
            'type' => strtolower($cardObject->brand));
        return $result_array;
    }

    public function remove_card_from_stripe_vault($customerId, $cardId)
    {
        $this->stripe_main->setApiKey($this->stripe_secret_api_key);
        $customer = new Customer;
        $customer = $customer->retrieve($customerId);
        $customer->sources->retrieve($cardId)->delete();
    }

    public function add_new_card_to_existing_stripe_customer($customerId, $token)
    {
        $this->stripe_main->setApiKey($this->stripe_secret_api_key);
        $customer = new Customer;
        $customer = $customer->retrieve($customerId);
        $cardObject = $customer->sources->create(array('source' => $token));
        return $this->retrieve_all_needles_for_database_card_information($cardObject);
    }

    public function new_stripe_customer_creating($userId, $token)
    {
        $this->stripe_main->setApiKey($this->stripe_secret_api_key);
        $customer = new Customer;
        $result = $customer->create(array('source' => $token));
        $customerId = $result->id;
        $cardObject = $result->sources->data[0];
        $db_array = array('customerId' => $customerId, 'userId' => $userId);
        $this->CI->buy_model->add_customer_id_to_database($db_array);
        return $this->retrieve_all_needles_for_database_card_information($cardObject);
    }

    public function card_token_creation($array)
    {
        $array = $this->received_credit_card_info_previous_processing($array);
        $token = new Token;
        $card_token = $token->create($array);
        return $card_token->id;
    }}