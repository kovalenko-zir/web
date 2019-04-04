<?php

class Api_model extends CI_Model
{

    protected $user_dataset;

    protected $content_dataset;

    public function content_string()
    {
        $this->db->where('item_id', '1');
        $query = $this->db->get('content');
        $result = $query->result_array();
        return $result[0];
    }

//  DATA SET THAT CONTAINS ONLY OPEN USER DATA

    public function user_dataset()
    {
        $this->user_dataset = $this->db->select('id,user_name,user_surname,email,zip_code,country,city,adress,appartment,
        fb_id,google_id');

    }

    public function content_dataset()
    {
        $this->content_dataset = $this->db->select('item_id,artName,artInfo,digitalInfo,physicalInfo,img_preview,marker_path,video_path,artistName,artistInfo,physicalPrice,digitalPrice,');
    }

    //   METHOD WHICH CHECK FOR UPDATES

    public function update_checker($id, $type)
    {
        $result = $this->db->select('Checker')->where('UserID', $id)->get('Update_checker')->result_array();
        if (!empty($result)) {
            if ($result[0]['Checker'] == '1' && $type === 'user') {
                $this->user_dataset();
                $result = $this->db->where('id', $id)->get('users')->result_array();
                return $result[0];
            } else if ($result[0]['Checker'] == '1' && $type === 'card') {
                return $this->db->select('fourNum,type')->where('userId', $id)->get('stripe_payment_cards')->result_array();
            } else {
                return 'false';
            }
        } else {
            return 'false';
        }
    }

    public function check_done($id)
    {
        $this->db->where('UserID', $id)->update('Update_checker', array('Checker' => 0));
    }

//   METHOD THAT RETRIEVES APPLICATION IDENTIFICATOR,
//   WHICH WILL BE COMPARING WITH KEY THAT WILL BE RECEIVED FROM REQUESTS

    public function auth_key()
    {
        $this->db->select('application_identifier');
        $this->db->where('id', '1');
        $query = $this->db->get('administrator');
        $result = $query->result_array();
        return $result[0]['application_identifier'];
    }

//  METHOD THAT USES FOR CHECKING IF ACCESS TOKEN EXIST.
//  IF EXIST RETURN ARRAY ABOUT LOGGED IN USER
//  IF DOESN'T EXIST RETURN EMPTY ARRAY

    public function is_session_exist($access_token)
    {
        $this->user_dataset();
        $this->db->where('session_acces_token', $access_token);
        $result = $this->db->get('users')->result_array();
        return $result;

    }

//  METHOD THAT USES FOR CHECKING IF EMAIL EXIST IN DB.
//  IF EXIST RETURN FALSE BOOLEAN
//  IF DOESN'T EXIST RETURN TRUE BOOLEAN

    public function email_existence_check_up($email)
    {
        $result = $this->db->select('id')->where('email', $email)->get('users')->result_array();

        if (empty($result)) {
            return true;
        } else {
            return false;
        }

    }

//  METHOD WHICH USES FOR ADDING USER BY EMAIL AND PASSWORD.
//  RETURN ARRAY WITH NEWLY REGISTERED USER
//  IF DOESN'T EXIST RETURN TRUE BOOLEAN

    public function add_new_registered_user($data)
    {
        $this->db->insert('users', $data);
        $insert_id = $this->db->insert_id();
        $this->user_dataset();
        $this->db->where('id', $insert_id);
        $result = $this->db->get('users')->result_array();
        $result = $result[0];
        return $result;
    }

//  METHOD WHICH USES FOR CHECKING VALIDATION CODE WHICH WAS SENT TO USER EMAIL.
//  IF EXIST USER RETURN FALSE BOOLEAN
//  IF DOESN'T EXIST RETURN TRUE BOOLEAN

    public function validation_code_list($code)
    {
        $this->db->select('id')->where('validation_status', $code);
        $result = $this->db->get('users')->result_array();
        if (empty($result)) {
            return true;
        } else {
            return false;
        }

    }

//  METHOD WHICH USES FOR CHECKING VALIDATION CODE WHICH WAS SENT TO USER EMAIL.
//  IF EXIST USER VALIDATION STATUS CHANGES TO 'VALIDATED' AND RETURN TRUE BOOLEAN
//  IF DOESN'T EXIST RETURN FALSE BOOLEAN

    public function validation_account_by_email_code($code)
    {
        $this->db->select('id')->where('validation_status', $code);
        $result = $this->db->get('users')->result_array();
        if (empty($result)) {
            return false;
        } else {
            $this->db->where('id', $result[0]['id'])->update('users', array('validation_status' => 'validated'));
            return true;
        }
    }

//  METHOD CHECKS CURRENT VALIDATION STATUS OF REGISTERED USER
//  IF VALIDATED RETURN TRUE BOOLEAN
//  IF DOESN'T RETURN FALSE BOOLEAN

    public function email_current_validation_status($email)
    {
        $this->db->select('validation_status')->where('email', $email);
        $result = $this->db->get('users')->result_array();
        if ($result[0]['validation_status'] === 'validated') {
            return true;
        } else {
            return false;
        }

    }

//  METHOD CHECKS CURRENT VALIDATION STATUS OF REGISTERED USER
//  IF VALIDATED RETURN TRUE BOOLEAN
//  IF DOESN'T RETURN FALSE BOOLEAN

    public function authorization($email, $password)
    {
        $result = $this->db->select('password')->where('email', $email)->get('users')->result_array();
        if ($result[0]['password'] === $password) {
            $this->db->where('email', $email)->update('users', array('log_status' => 'logged_in'));
            $this->user_dataset();
            $result = $this->db->where('email', $email)->get('users')->result_array();
            return $result[0];
        } else {
            return false;
        }
    }

//  METHOD WHICH USES FOR CHECKING EXISTENCE OF SOCIAL NETWORK IDENTIFICATOR IN DB.
//  IF EXIST RETURNS ASSOCIATED WITH THIS IDENTIFICATOR USER INFORMATION.
//  IF DOESN'T EXIST RETURN FALSE BOOLEAN

    public function social_network_id_existence($id, $soc_network)
    {
        if ($soc_network === 'fb') {
            $column = 'fb_id';
        } else {
            $column = 'google_id';
        }
        $result = $this->db->select('id')->where($column, $id)->get('users')->result_array();

        if (empty($result)) {
            return false;
        } else {
            $this->db->where($column, $id)->update('users', array('log_status' => 'logged_in'));
            $this->user_dataset();
            $result = $this->db->where($column, $id)->get('users')->result_array();
            $result = $result[0];
            return $result;
        }
    }

//  METHOD WHICH USES FOR ADDING SOCIAL NETWORK IDENTIFICATOR IN DB.
//  RETURNS ASSOCIATED WITH THIS NEWLY ADDED IDENTIFICATOR USER INFORMATION.

    public function add_social_network_id($id, $email, $soc_network)
    {
        if ($soc_network === 'fb') {
            $column = 'fb_id';
        } else {
            $column = 'google_id';

        }
        $data = array($column => $id,
            'log_status' => 'logged_in',
            'validation_status' => 'validated');
        $this->db->where('email', $email)->update('users', $data);

        $this->user_dataset();
        $result = $this->db->where($column, $id)->get('users')->result_array();
        return $result[0];
    }

//  METHOD WHICH LOGGING OUT LOGGED USER

    public function user_logout($id)
    {
        $data = array('session_acces_token' => '',
            'device_id' => '',
            'log_status' => 'logged_out');
        $this->db->where('id', $id)->update('users', $data);
    }

//  SESSION BLOCK

//  METHOD WHICH RETRIEVES INFO ABOUT SESSION OF CURRENT USER.
//  RETURNS ASSOCIATED WITH THIS NEWLY ADDED IDENTIFICATOR USER INFORMATION.

    public function status_token_device_list($id)
    {
        $this->db->select('session_acces_token,device_id,log_status')->where('id', $id);
        $result = $this->db->get('users')->result_array();
        return $result[0];
    }

//  METHOD WHICH ADD INFO ABOUT SESSION OF CURRENT USER.
//  RETURNS ASSOCIATED WITH THIS NEWLY ADDED IDENTIFICATOR USER INFORMATION.

    public function add_access_token_and_device_id($user_id, $acces_token, $device_id)
    {
        $this->db->where('id', $user_id)->update('users', array('session_acces_token' => $acces_token, 'device_id' => $device_id));
    }

    public function update_device_id($user_id, $device_id)
    {
        $this->db->where('id', $user_id)->update('users', array('device_id' => $device_id));
    }

//CONTENT BLOCK

//  METHOD WHICH RETRIEVES CURRENT CONTENT VERSION

    public function get_content_version()
    {
        $result= $this->db->where('id', '1')->get('db_version')->result_array();
        return $result[0]['content_version_number'];
    }

//  METHOD WHICH RETRIEVES APPLE IDENTIFIERS FOR CONTENT

    public function get_apple_identifiers()
    {
        return $this->db->select('item_id,apple_id')->get('apple_id')->result_array();
    }

//  METHOD WHICH RETRIEVES ALL CONTENT FROM DB

    public function get_all_content()
    {
        $this->content_dataset();
        return $this->db->order_by('item_id', 'DESC')->get('content')->result_array();
    }

//  METHOD WHICH RETRIEVES ALL PURCHASED BY LOGGED USER CONTENT FROM DB
//  RETURNS LIST OF PURCHASED ITEMS IDENTIFICATOR

    public function user_purchased_items($user_id)
    {
        $this->db->select('item_id')->where('user_id', $user_id)->where('status', 'PAID');
        $result = $this->db->get('purchases')->result_array();
        if (empty($result)) {
            return false;
        } else {
            $purchased_items_list = array();
            foreach ($result as $object) {
                array_push($purchased_items_list, $object['item_id']);
            }
            return $purchased_items_list;
        }
    }

//  METHOD WHICH RETRIEVES ALL LOGGED USERS PAYMENT CARDS
//  RETURNS LIST OF ALL LOGGED USERS PAYMENT CARDS

//    public function get_users_cards($id)
//    {
//        $this->db->select('fourNum,type')->where('userId', $id);
//        $result = $this->db->get('payment_cards')->result_array();
//
//        return $result;
//    }

    public function get_users_cards($id)
    {
        $this->db->select('fourNum,type')->where('userId', $id);
        $result = $this->db->get('stripe_payment_cards')->result_array();

        return $result;
    }


//  METHOD WHICH UPDATE USER PERSONAL INFORMATION
//  RETURNS UPDATED USER PERSONAL INFORMATION

    public function change_user_info($id, $data)
    {

        $this->db->where('id', $id)->update('users', $data);
        $this->user_dataset;
        $this->db->where('id', $id);
        $result = $this->db->get('users')->result_array();
        $result = $result[0];
        return $result;
    }

//  METHOD WHICH COMPARING OLD PASSWORD WITH PASS IN DB
//  RETURNS TRUE IF EQUAL AND FALSE IF DON'T


    public function compare_with_old_password($id, $old_pass)
    {
        $this->db->select('password')->where('id', $id);
        $result = $this->db->get('users')->result_array();
        $result = $result[0]['password'];

        if ($old_pass === $result) {
            return true;
        } else {
            return false;
        }
    }

//  METHOD WHICH UPDATE PASSWORD IN DB
//  RETURNS TRUE IF EQUAL AND FALSE IF DON'T

    public function change_user_password($type, $identificator, $pass)
    {
        if ($type === 'email') {
            $this->db->where('email', $identificator);
        } else if ($type === 'id') {
            $this->db->where('id', $identificator);

        }
        $data = array('password' => $pass);
        $this->db->update('users', $data);
    }

//  METHOD WHICH FULLY DELETE USER FROM DB AND APP

    public function final_delete_user($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('users');

    }
}