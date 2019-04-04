<?php

class General
{
    protected $CI;

    protected $user_table_db_field_names;

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->user_table_db_field_names = array(
            'fb' => array('first_name' => 'user_name',
                'last_name' => 'user_surname',
                'email' => 'email',
                'id' => 'fb_id'),
            'google' => array('givenName' => 'user_name',
                'familyName' => 'user_surname',
                'value' => 'email',
                'id' => 'google_id'),
            'email' => array('name' => 'user_name',
                'surname' => 'user_surname',
                'email' => 'email',
                'password' => 'password',
                'country' => 'country',
                'zip_code' => 'zip_code',
                'adress' => 'adress',
                'city' => 'city',
                'appartment' => 'appartment'));


    }

//    Get application authorization key

    public function app_auth()
    {
        return $this->CI->api_model->auth_key();
    }

//    Get unique pic access-code string

    public function unique_pic_key()
    {

        $rand_str = random_str(24);
        $key_existence = $this->CI->buy_model->is_pic_key_exist($rand_str);
        if (!($key_existence)) {
            $generated_string = $rand_str;
        } else {
            $generated_string = $this->unique_pic_key();
        };
        return $generated_string;

    }


//    Remove unnecessary fields in array when add info to database

    public function remove_unnecessary_fields($array)
    {
        unset($array['app_id']);
        unset($array['device_id']);
        unset($array['session_acces_token']);
        unset($array['acces_token']);
        unset($array['access_token']);
        unset($array['session_acces_token']);
        unset($array['fourNum']);

        return $array;
    }

    public function turn_array_to_db_array($array, $selector)
    {
        $db_array = array();
        foreach ($array as $key => $val) {
            $db_array[$this->user_table_db_field_names[$selector][$key]] = $val;
        }
        return $db_array;
    }

}