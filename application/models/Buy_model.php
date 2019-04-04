<?php
class Buy_model extends CI_Model {

//  STRIPE TEST METHODS

    public function check_if_user_has_stripes_customer_id ($id) {
        $customerId=$this->db->select('customerId')->where('userId',$id)->get('stripe_customers_id')->result_array();
        if (!empty($customerId)){
            return $customerId[0]['customerId'];
        } else {
            return false;
        }
    }

//  METHOD WHICH ADD STRIPE CUSTOMER IDENTIFIER TO DATABASE

    public function add_customer_id_to_database ($customerIdentifiers){
        $this->db->insert('stripe_customers_id',$customerIdentifiers);
    }

//  METHOD WHICH ADD STRIPE CUSTOMER CREDIT CARD IDENTIFIER TO DATABASE

    public function add_stripe_card ($cardArray){
        $this->db->insert('stripe_payment_cards',$cardArray);
        $cardId= $this->db->insert_id();
        $cardInfo=$this->db->select('fourNum,type')->where('id',$cardId)->get('stripe_payment_cards')->result_array();
        $this->db->select('ID')->where('UserID',$cardArray['userId']);
        $result = $this->db->get('Update_checker')->result_array();
        if (empty($result)){
            $this->db->insert('Update_checker', array('UserID'=>$cardArray['userId'],'Checker'=>1));
        } else {
            $this->db->where('UserID',$cardArray['userId'])->update('Update_checker', array('UserID'=>$cardArray['userId'],'Checker'=>1));
        }
        return $cardInfo[0];
    }

//  METHOD WHICH RETRIEVE STRIPE CUSTOMER CREDIT CARD IDENTIFIER FROM DATABASE BY USER ID AND FOUR NUM OF CARD

    public function retrieve_card_stripe_identifier ($userId,$fourNumsOfCard) {
        $stripeCardId=$this->db->select('stripeCardId')->where(array('userId'=>$userId,'fourNum'=>$fourNumsOfCard))->get('stripe_payment_cards')->result_array();
        if (!empty($stripeCardId)){
            return $stripeCardId[0]['stripeCardId'];
        } else {
            return false;
        }
    }

//  METHOD WHICH DELETE STRIPE CUSTOMER CREDIT CARD IDENTIFIER FROM DATABASE

    public function remove_card_from_db ($cardId){
        $this->db->where('stripeCardId',$cardId)->delete('stripe_payment_cards');
    }

//  METHOD WHICH RETRIEVE CONCRETE ITEM ARTNAME FROM DATABASE

    public function retrieve_concrete_item_info ($itemId) {
        $itemInfo=$this->db->select('artName')->where('item_id',$itemId)->get('content')->result_array();
        return $itemInfo[0];
    }

//  METHOD WHICH RETRIEVES FULL LIST OF COUNTRIES

    public function country_list (){
        return $this->db->select('UNTERM_English_Short')->get('countries')->result_array();
    }

//  METHOD WHICH RETRIEVES CONCRETE COUNTRY TWO DIGITS CODE

    public function country_code ($country){
        $result = $this->db->select('ISO31661Alpha2')->where('UNTERM_English_Short',$country)->get('countries')->result_array();
        return $result[0]['ISO31661Alpha2'];
    }

//  METHOD WHICH RETRIEVES FULL INFORMATION ABOUT USER FROM DB

    public function user_info ($id){
        $result = $this->db->where('id',$id)->get('users')->result_array();
        return $result[0];
    }

//  METHOD WHICH RETRIEVES FULL INFORMATION ABOUT ITEM FROM DB

    public function item_info ($id){
        $result = $this->db->where('item_id',$id)->get('content')->result_array();
        return $result[0];
    }

//  METHOD WHICH CHECKING IS PIC CODE EXIST
//  IF EXIST RETURN TRUE
//  IF NOT RETURN FALSE

    public function is_pic_key_exist($string)
    {
        $result = $this->db->select('pic_code')->where('pic_code',$string)->get('purchases')->result_array();
        if(empty($result)){
            return false;
        } else {
            return true;
        }
    }

//  METHOD WHICH INSERT UNIQUE PIC CODE TO DB
//  IF EXIST RETURN TRUE
//  IF NOT RETURN FALSE

    public function insert_pic_code($key, $user_id, $item_id)
    {
        $data = array('pic_code' => $key);
        $this->db->where('user_id', $user_id)->where('item_id', $item_id)->update('purchases', $data);
    }

//  METHOD WHICH CHECKING IF ITEM FREE OR NOT
//  IF FREE RETURN TRUE BOOLEAN
//  IF NOT RETURN FALSE BOOLEAN

    public function is_free($item_id)
    {
        $result = $this->db->select('digitalPrice')->where('item_id', $item_id)->get('content')->result_array();
        $result = $result[0]['digitalPrice'];
        if ($result === 'FREE') {
            return true;
        } else {
            return false;
        }
    }

//  METHOD WHICH CHECKING IF ITEM PURCHASED OR NOT
//  IF PURCHASED RETURN TRUE BOOLEAN
//  IF NOT RETURN FALSE BOOLEAN

    public function is_this_item_purchased($item_id,$user_id)
    {
        $result =$this->db->select('status')->where('item_id', $item_id)->where('user_id', $user_id)->get('purchases')->result_array();
        if (empty($result)) {
            return false;
        } else {
            return true;
        }
    }

    public function purchased_item_id($key)
    {
        return $this->db->select('item_id,user_id')->where('pic_code', $key)->get('purchases')->result_array();
    }

    public function user_purchased_items($user_id)
    {
        $result=$this->db->select('item_id,item_name,pic_code')->where('user_id', $user_id)->where('status', 'PAID')->get('purchases')->result_array();
        if (empty($result)) {
            return false;
        } else {
            $resultArray=array();
            foreach ($result as $key=>$value){
                $resultArray[$value['item_name']]=$value['pic_code'] . '/' .$value['item_id'];
            }
            $purchased_items_list = array_unique($resultArray);
            return $purchased_items_list;
        }
    }

//  METHOD WHICH RETRIEVES PATH TO FULLSIZE IMAGE FROM DB

    public function full_image_path($item_id)
    {
        $result = $this->db->select('full_img')->where('item_id', $item_id)->get('content')->result_array();
        return $result[0]['full_img'];
    }

//  METHOD WHICH RETRIEVES ALL LOGGED USERS PAYMENT CARDS
//  RETURNS LIST OF ALL LOGGED USERS PAYMENT CARDS

    public function get_users_cards($id)
    {
        return $this->db->select('fourNum,type')->where('userId', $id)->get('stripe_payment_cards')->result_array();
    }

//  USER PAYMENT INFO BLOCK


   public function update_user_shipping_info($data, $id)
    {
        $this->db->where('id', $id)->update('users', $data);
        $this->db->select('ID')->where('UserID',$id);
        $result = $this->db->get('Update_checker')->result_array();
        if (empty($result)){
            $this->db->insert('Update_checker', array('UserID'=>$id,'Checker'=>1));
        } else {
            $this->db->where('UserID',$id)->update('Update_checker', array('UserID'=>$id,'Checker'=>1));
        }
    }

    public function update_purchase( $id,$data)
    {
        $this->db->where('order_id',$id)->update('purchases', $data);
    }

    public function add_purchase($data)
    {
        $this->db->insert('purchases', $data);
        return $this->db->insert_id();
    }
}