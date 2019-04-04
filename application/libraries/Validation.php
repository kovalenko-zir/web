<?php

class Validation
{
    protected $CI;

    protected $validation_patterns;

    protected $error_array;

    protected $result_array;

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->error_array = array('data' => "", 'error' => "");
        $this->result_array = array('data' => "", 'error' => "");

//      CONSTRUCT ARRAY WITH REGEXP PATTERNS FOR CHECKING INPUT POST ARRAY VALUES FOR VALIDITY
//      FOR ADDING SOME ADDITIONAL CHECK UP JUST ADD NEW REGEXP TO CONCRETE VALUE ARRAY

        $this->validation_patterns = array(
            'name' => array(array('/[a-zA-Z]/'), array(2, 20)),
            'surname' => array(array('/[a-zA-Z]/'), array(2, 30)),
            'email' => array(array('/^[a-zA-Z0-9_\-\.]+@[a-z]+\.[a-z]/'), array(2, 50)),
            'password' => array(array('/[a-zA-Z0-9-_.]/'), array(2, 1000)),
            'country' => array(array('/[a-zA-Z]/'), array(2, 50)),
            'city' => array(array('/[a-zA-Z]/'), array(2, 50)),
            'zip_code' => array(array('/[0-9]/'), array(4, 6)),
            'appartment' => array(array('/[a-zA-Z0-9]/'), array(2, 200)),
            'adress' => array(array('/[a-zA-Z0-9]/'), array(2, 200)));

    }

//  VALUES VALIDATION

//  SPECIAL CHECK UP METHODS

//  METHOD WHICH REMOVE EMPTY VALUES

    public function empty_value_remover($array)
    {
        foreach ($array as $key => $object) {
            if ($object === '') {
                unset($array[$key]);
            }
        }
        if (count($array) > 0) {
            return $array;
        } else {
            $this->error_array['data'] = '';
            $this->error_array['error'] = 'There is no data to change';

            echo json_encode($this->error_array);
            exit();
        }
    }

//  METHOD THAT CHECKS UP IF EMAIL EXIST

    public function email_existence_check_up($array)
    {
        $email_existence_check_up = $this->CI->api_model->email_existence_check_up($array['email']);
        if ($email_existence_check_up) {
            return $array;
        } else {
            foreach ($array as $key => $val) {
                $this->error_array['error'][$key] = '';
            }
            $this->error_array['error'] = 'This email is already registered';
            echo json_encode($this->error_array);
            exit();
        };
    }

//  Application $_POST method and authorization check-up

    public function received_data_checkup($some_array)
    {
        if ($some_array) {
            $raw_data = $some_array['data'];
            $raw_data = json_decode($raw_data, true);
            $app_id = $raw_data['app_id'];
            $auth_id = $this->CI->general->app_auth();

            if ($app_id === $auth_id) {


                $clear_data = array();
                foreach ($raw_data as $key => $values) {
                    $raw_value = $this->CI->security->xss_clean($values);
                    $raw_value = $this->CI->security->sanitize_filename($raw_value);
                    $raw_value = $this->CI->security->entity_decode($raw_value);
                    $clear_data[$key] = $raw_value;
                }

                return $clear_data;


            } else {
                $this->error_array['error'] = 'Application authorization error';
                echo json_encode($this->error_array);
                exit;
            }


        } else {
            show_404();
            exit;
        }


    }

//  EMPTY CHECKING - This method check if $_POST array consist empty values

    public function is_post_array_values_are_empty($array)
    {

        $empty_counter = 0;
        $empty_error = array();
        foreach ($array as $key => $value) {
            if ($value === '') {
                $this->error_array['error'] = $key . ' field is empty';
                $empty_counter++;
                break;
            }
        }

        if ($empty_counter === 0) {
            return $array;
        } else {
            $this->error_array['error'] = $empty_error;
            echo json_encode($this->error_array);
            exit();
        }

    }

//  THIS METHOD RETURN ARRAY WITH VALID VALUES

    public function value_pattern_conformity($array)
    {

        $arr = $array;
        $error_counter = 0;
        foreach ($arr as $key => $value) {
            if (array_key_exists($key, $this->validation_patterns)) {
                $patterns_that_will_be_checked = $this->validation_patterns[$key];
                for ($i = 0; $i < count($patterns_that_will_be_checked[0][0]); $i++) {
                    if (!preg_match($patterns_that_will_be_checked[0][$i], $value) || strlen($value) < $patterns_that_will_be_checked[1][0] || strlen($value) > $patterns_that_will_be_checked[1][1]) {
                        $this->error_array['error'] = 'Incorrect value in ' . $key . ' field';
                        $error_counter++;
                        break;
                    } else {
                        $this->result_array['data'][$key] = $value;
                        $this->error_array['error'][$key] = '';
                    }
                }
            } else {
                $this->result_array['data'][$key] = $value;
                $this->error_array['error'][$key] = '';
            }
            if ($error_counter > 0) {
                break;
            }
        }


        if ($error_counter === 0) {
            return $this->result_array['data'];
        } else {
            echo json_encode($this->error_array);
            exit();
        }


    }

//  SESSION PARAMETERS COMPARING

    public function status_token_device_comparing($user_id, $token, $device_id)
    {
        $authorization_fields = $this->CI->api_model->status_token_device_list($user_id);
        if ($authorization_fields['log_status'] !== 'logged_in' || $authorization_fields['session_acces_token'] !== $token ||
            $authorization_fields['device_id'] !== $device_id) {
            $this->error_array['error'] = 'Access denied';
            echo json_encode($this->error_array);
            exit();
        }


    }

    public function device_id_update ($user_id, $device_id)
    {
        $authorization_fields = $this->CI->api_model->status_token_device_list($user_id);
        if ($authorization_fields['device_id'] !== $device_id) {
            $this->CI->api_model->update_device_id($user_id,$device_id);
        }
    }


}