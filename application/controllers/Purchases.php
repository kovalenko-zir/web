<?php
class Purchases extends CI_Controller
{
    public function buy($type)
    {
        $clear_data = $this->validation->received_data_checkup(array('data' => json_encode($_GET)));
        $this->validation->status_token_device_comparing($clear_data['user_id'], $clear_data['access_token'], $clear_data['device_id']);

        $data['payment_cards'] = $this->buy_model->get_users_cards($clear_data['user_id']);
        $data['user'] = $this->buy_model->user_info($clear_data['user_id']);
        $data['item'] = $item_info = $this->buy_model->item_info($clear_data['item_id']);
        $data['purchased'] = $purchased = $this->buy_model->is_this_item_purchased($clear_data['item_id'], $clear_data['user_id']);
        $data['clear_data'] = $clear_data;
        $data['page_name'] = $type;
        $data['country_list'] = $this->buy_model->country_list();

        $this->load->view('mobile_view/templates/header', $data);
        $this->load->view('mobile_view/art_info');
        $this->load->view('mobile_view/contacts_block');

        if ($type === 'physical') {
            $this->load->view('mobile_view/shipping_block');
            $this->load->view('mobile_view/payment_block');
        } else if ($type === 'digital' && $item_info['digitalPrice'] !== 'FREE' && !($purchased)) {
            $this->load->view('mobile_view/payment_block');
        }
        $this->load->view('mobile_view/templates/footer');
    }

    public function add_credit_card()
    {
        $clear_data = $this->validation->received_data_checkup($_POST);
        $this->validation->status_token_device_comparing($clear_data['id'], $clear_data['access_token'], $clear_data['device_id']);
        $card_token = $this->stripe_payment->card_token_creation($clear_data);
        $customer_id = $this->buy_model->check_if_user_has_stripes_customer_id($clear_data['id']);
        if ($customer_id) {
            $db_array = $this->stripe_payment->add_new_card_to_existing_stripe_customer($customer_id, $card_token);
        } else {
            $db_array = $this->stripe_payment->new_stripe_customer_creating($clear_data['id'], $card_token);
        }
        $db_array['userId'] = $clear_data['id'];
        $result = $this->buy_model->add_stripe_card($db_array);
        echo json_encode($result);
    }

    public function delete_credit_card()
    {
        $clear_data = $this->validation->received_data_checkup($_POST);
        $this->validation->status_token_device_comparing($clear_data['id'], $clear_data['access_token'], $clear_data['device_id']);
        $customer_id = $this->buy_model->check_if_user_has_stripes_customer_id($clear_data['id']);
        $card_id = $this->buy_model->retrieve_card_stripe_identifier($clear_data['id'], $clear_data['fourNum']);
        $this->stripe_payment->remove_card_from_stripe_vault($customer_id, $card_id);
        $this->buy_model->remove_card_from_db($card_id);
        echo 'deleted';
    }

    public function direct_payment()
    {
        $clear_data = $this->validation->received_data_checkup($_POST);
        $this->validation->status_token_device_comparing($clear_data['user_id'], $clear_data['acces_token'], $clear_data['device_id']);
        $order=$this->stripe_payment->create_new_order($clear_data);
        $payment = $this->stripe_payment->pay_order($order);
        $this->add_purchase_to_database($clear_data,$payment->id);
        echo 'completed';
    }

    private function add_purchase_to_database ($orderArray,$transactionId){
        $key = $this->general->unique_pic_key();
        $data_to_db = array('transaction_id' => $transactionId,
            'status' => 'PAID',
            'pic_code' => $key,
            'order_date' => date('d.m.Y'),
            'order_email' => $orderArray['order_email'],
            'order_zip_code' => $orderArray['order_zip_code'],
            'order_country' => $orderArray['order_country'],
            'order_city' => $orderArray['order_city'],
            'order_adress' => $orderArray['order_adress'],
            'order_appartment' => $orderArray['order_appartment'],
            'order_cost' => $orderArray['order_cost'],
            'user_id' => $orderArray['user_id'],
            'item_id' => $orderArray['item_id'],
            'order_type' => $orderArray['order_type'],
        );
        $user = $this->buy_model->user_info($data_to_db['user_id']);
        $id = $this->buy_model->add_purchase($data_to_db);
        $this->email->send_message_to_user('order', $key, $user['email'], $data_to_db['item_id'], $id, $user['user_name'], $user['user_surname']);
    }

    public function get_purchased_or_free_digital_copy()
    {
        $clear_data = $this->validation->received_data_checkup($_POST);
        $this->validation->status_token_device_comparing($clear_data['user_id'], $clear_data['acces_token'], $clear_data['device_id']);
        $purchased = $this->buy_model->is_this_item_purchased($clear_data['item_id'], $clear_data['user_id']);
        $is_free = $this->buy_model->is_free($clear_data['item_id']);
        if ($purchased || $is_free) {
            $key = $this->general->unique_pic_key();
            if ($is_free) {
                $message_type = 'free';
            } else {
                $message_type = 'resend';
                $this->buy_model->insert_pic_code($key, $clear_data['user_id'], $clear_data['item_id']);
            }
            $user_info = $this->buy_model->user_info($clear_data['user_id']);
            $this->email->send_message_to_user($message_type, $key, $user_info['email'], $clear_data['item_id']);
            echo $message_type;
        }
    }

    public function download_digital_copy($key, $item_id)
    {
        $is_free = $this->buy_model->is_free($item_id);
        $item_info = $this->buy_model->purchased_item_id($key);

        $purchased = false;
        if (!(empty($item_info))) {
            $purchased = $this->buy_model->is_this_item_purchased($item_info[0]['item_id'], $item_info[0]['user_id']);
        }
        if ($purchased || $is_free) {
            $full_img_path = $this->buy_model->full_image_path($item_id);
            $slash = strrpos($full_img_path, '/');
            $str_length = strlen($full_img_path);
            $file_name = substr($full_img_path, $slash + 1, $str_length);

            header('Content-Length: ' . filesize($full_img_path));
            header('Content-Disposition: attachment; filename=' . basename($file_name));
            header("Content-Type: application/x-force-download; name=" . $file_name . "");
            header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");// дата в прошлом
            header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
            header("Cache-Control: no-store, no-cache, must-revalidate");// HTTP/1.1
            header("Cache-Control: post-check=0, pre-check=0", false);
            header("Pragma: no-cache");// HTTP/1.0
            readfile($full_img_path);
        }
    }

    public function user_shipping_info_update()
    {
        $clear_data = $this->validation->received_data_checkup($_POST);
        $this->validation->status_token_device_comparing($clear_data['user_id'], $clear_data['acces_token'], $clear_data['device_id']);

        $data = array('zip_code' => $clear_data['order_zip_code'],
            'country' => $clear_data['order_country'],
            'city' => $clear_data['order_city'],
            'adress' => $clear_data['order_adress'],
            'appartment' => $clear_data['order_appartment']);
        $this->buy_model->update_user_shipping_info($data, $clear_data['user_id']);

    }

    public function in_app_purchase_listener (){
        $clear_data = $this->validation->received_data_checkup($_POST);
        $this->validation->status_token_device_comparing($clear_data['user_id'], $clear_data['access_token'], $clear_data['device_id']);
        $clear_data=$this->general->remove_unnecessary_fields($clear_data);
        $key = $this->general->unique_pic_key();
        $user = $this->buy_model->user_info($clear_data['user_id']);
        $item= $this->buy_model->item_info($clear_data['item_id']);
        $data_to_db = array(
            'status' => 'PAID',
            'pic_code' => $key,
            'order_date' => date('d.m.Y'),
            'order_email' => $clear_data['email'],
            'order_zip_code' => $user['zip_code'],
            'order_country' => $user['country'],
            'order_city' => $user['city'],
            'order_adress' => $user['adress'],
            'order_cost' => $item['digitalPrice'],
            'user_id' => $clear_data['user_id'],
            'item_id' => $clear_data['item_id'],
            'item_name' => $item['artName'],
            'order_type' => 'digital',
        );

        $id = $this->buy_model->add_purchase($data_to_db);

        $this->email->send_message_to_user('order', $key, $clear_data['email'], $data_to_db['item_id'], $id, $user['user_name'], $user['user_surname']);

        echo json_encode(array('data'=>'success','error'=>''));
    }

    public function restore_all_items_for_concrete_user ()
    {
        $clear_data = $this->validation->received_data_checkup($_POST);
        $this->validation->status_token_device_comparing($clear_data['id'], $clear_data['access_token'], $clear_data['device_id']);
        $clear_data = $this->general->remove_unnecessary_fields($clear_data);
        $user = $this->buy_model->user_info($clear_data['id']);
        $userPurchasedItems = $this->buy_model->user_purchased_items($clear_data['id']);
        if ($userPurchasedItems) {
            $this->email->send_message_to_user('send_all', '', $user['email'], $userPurchasedItems, '', $user['user_name'], $user['user_surname']);
            echo json_encode(array('data' => 'success', 'error' => ''));
        } else {
            echo json_encode(array('data' => '', 'error' => 'no files to send'));
        }
    }
}