<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends CI_Controller
{
    protected $response;

    public function view()
    {
        show_404();
    }

    private function validation_code_generator()
    {
        $rand_str = random_str(24);
        $validation_code_list = $this->api_model->validation_code_list($rand_str);
        if ($validation_code_list) {
            $generated_string = $rand_str;
        } else {
            $generated_string = $this->validation_code_generator();
        };
        return $generated_string;
    }

    public function registration_successful($apply = 'success')
    {
        if ($apply === 'failed') {
            $text = "You can't complete registration at ZIR APP. 
                Link expired or invalid.";
            $header = 'Sorry :(';
        } else {
            $text = "You've completed your ZIR account registration. Now you can discover new 
                     AR artworks and access AR Art experiences. 
                     Feel free to email us at team@getzir.com with comments and suggestions, 
                     so that we can improve your experience and deliver the content that most interests you.";
            $header = 'Congratulations!';
        }
        $data['header'] = $header;
        $data['message'] = $text;
        $this->load->view('registration_successful', $data);
    }

    public function login_check()
    {
        $clear_data = $this->validation->received_data_checkup($_POST);
        $user_info = $this->api_model->is_session_exist($clear_data['access_token']);
        if (empty($user_info)) {
            $this->response['data'] = NULL;
            $this->response['error'] = 'There is no such session';
        } else {
            $this->response['data'] = $user_info[0];
            $this->response['error'] = '';
        }
        echo json_encode($this->response);
    }

    public function registration()
    {
        $clear_data = $this->validation->received_data_checkup($_POST);
        $clear_data = $this->validation->is_post_array_values_are_empty($clear_data);
        $clear_data = $this->general->remove_unnecessary_fields($clear_data);
        $clear_data = $this->validation->value_pattern_conformity($clear_data);
        $clear_data = $this->validation->email_existence_check_up($clear_data);
        $db_array = $this->general->turn_array_to_db_array($clear_data, 'email');

        $db_array['password'] = sha1($db_array['password']);
        $db_array['validation_status'] = $this->validation_code_generator();
        $db_array['log_status'] = 'logged_in';
        $this->email->send_message_to_user('v_code', $db_array['validation_status'], $db_array['email']);
        $this->api_model->add_new_registered_user($db_array);

        $this->response['data'] = 'User created. Need validation';
        $this->response['error'] = '';
        echo json_encode($this->response);
    }

    public function validation($code)
    {
        $result = $this->api_model->validation_account_by_email_code($code);
        if ($result) {
            header("location: /success");
        } else {
            header("location: /failed");
        }
    }

    public function login()
    {
        $clear_data = $this->validation->received_data_checkup($_POST);
        $clear_data = $this->validation->is_post_array_values_are_empty($clear_data);
        $clear_data = $this->general->remove_unnecessary_fields($clear_data);
        $clear_data = $this->validation->value_pattern_conformity($clear_data);
        $email_check = $this->api_model->email_existence_check_up($clear_data['email']);

        if ($email_check) {
            $this->response['error'] = 'There is no such registered email';
            $this->response['data'] = NULL;
        } else {
            $email_validation_status = $this->api_model->email_current_validation_status($clear_data['email']);
            if ($email_validation_status) {
                $authorization = $this->api_model->authorization($clear_data['email'], sha1($clear_data['password']));
                if (!($authorization)) {
                    $this->response['error'] = 'Email or password are incorrect';
                    $this->response['data'] = NULL;
                } else {
                    $this->response['data'] = $authorization;
                    $this->response['error'] = '';
                }
            } else {
                $this->response['error'] = 'This email is not validated';
                $this->response['data'] = NULL;
            }
        }
        echo json_encode($this->response);
    }

    public function social_network($soc_network)
    {
        $clear_data = $this->validation->received_data_checkup($_POST);
        $clear_data = $this->general->remove_unnecessary_fields($clear_data);
        $db_array = $this->general->turn_array_to_db_array($clear_data, $soc_network);

        $social_network_id_existence = $this->api_model->social_network_id_existence($clear_data['id'], $soc_network);

        if (!($social_network_id_existence)) {
            $email_exist = $this->api_model->email_existence_check_up($db_array['email']);
            if (!($email_exist)) {
                $data = $this->api_model->add_social_network_id($clear_data['id'], $db_array['email'], $soc_network);
                $this->response['data'] = $data;
                $this->response['error'] = '';
            } else {
                $db_array['log_status'] = 'logged_in';
                $db_array['validation_status'] = 'validated';
                $data = $this->api_model->add_new_registered_user($db_array);
                $this->response['data'] = $data;
                $this->response['error'] = '';
            }
        } else {
            $this->response['data'] = $social_network_id_existence;
            $this->response['error'] = '';
        }
        echo json_encode($this->response);
    }

    public function log_out()
    {
        $clear_data = $this->validation->received_data_checkup($_POST);
        $this->validation->status_token_device_comparing($clear_data['id'], $clear_data['access_token'], $clear_data['device_id']);
        $this->api_model->user_logout($clear_data['id']);
        $this->response['data'] = 'Log out successful';
        $this->response['error'] = '';
        echo json_encode($this->response);
    }

    public function get_access_token_for_user()
    {
        $clear_data = $this->validation->received_data_checkup($_POST);
        $authorization_fields = $this->api_model->status_token_device_list($clear_data['id']);
        if ($authorization_fields['log_status'] === 'logged_in') {
            $access_token = random_str('30') . $clear_data['id'];
            $this->api_model->add_access_token_and_device_id($clear_data['id'], $access_token, $clear_data['device_id']);
            $this->response['data'] = $access_token;
            $this->response['error'] = '';
        } else {
            $this->response['error'] = 'Access denied';
            $this->response['data'] = '';
        }
        echo json_encode($this->response);
    }

    public function content_version_checker () {
        $this->validation->received_data_checkup($_POST);
        $version = $this->api_model->get_content_version();
        $this->response['data'] = $version;
        $this->response['error'] = '';
        echo json_encode($this->response);

    }

    public function apple_identifiers () {
        $this->validation->received_data_checkup($_POST);
        $version = $this->api_model->get_apple_identifiers();
        $this->response['data'] = $version;
        $this->response['error'] = '';
        echo json_encode($this->response);

    }

    public function get_all_content()
    {
        $this->validation->received_data_checkup($_POST);
        $all_content = $this->api_model->get_all_content();
        $this->response['data'] = $all_content;
        $this->response['error'] = '';
        echo json_encode($this->response);
    }

    public function get_all_users_payment_cards()
    {
        $clear_data = $this->validation->received_data_checkup($_POST);
        $this->validation->status_token_device_comparing($clear_data['id'], $clear_data['access_token'], $clear_data['device_id']);
        $payment_cards = $this->api_model->get_users_cards($clear_data['id']);
        $this->response['data'] = $payment_cards;
        $this->response['error'] = '';
        echo json_encode($this->response);
    }

    public function get_concrete_user_purchased_items()
    {
        $clear_data = $this->validation->received_data_checkup($_POST);
        $this->validation->device_id_update($clear_data['id'], $clear_data['device_id']);
        $this->validation->status_token_device_comparing($clear_data['id'], $clear_data['access_token'], $clear_data['device_id']);
        $user_purchased_items = $this->api_model->user_purchased_items($clear_data['id']);
        if (!($user_purchased_items)) {
            $this->response['error'] = '';
            $this->response['data'] = array();
        } else {
            $this->response['data'] = $user_purchased_items;
            $this->response['error'] = '';
        }
        echo json_encode($this->response);
    }

    public function change_user_info()
    {
        $clear_data = $this->validation->received_data_checkup($_POST);
        $this->validation->status_token_device_comparing($clear_data['id'], $clear_data['access_token'], $clear_data['device_id']);
        $clear_data = $this->general->remove_unnecessary_fields($clear_data);
        $clear_data = $this->validation->empty_value_remover($clear_data);
        $clear_data = $this->validation->value_pattern_conformity($clear_data);
        if (array_key_exists('email', $clear_data)) {
            $clear_data = $this->validation->email_existence_check_up($clear_data);
        }
        $updated_data = $this->api_model->change_user_info($clear_data['id'], $clear_data);
        $this->response['error'] = '';
        $this->response['data'] = $updated_data;
        echo json_encode($this->response);
    }

    public function delete_user()
    {
        $clear_data = $this->validation->received_data_checkup($_POST);
        $this->validation->status_token_device_comparing($clear_data['id'], $clear_data['access_token'], $clear_data['device_id']);
        $this->api_model->final_delete_user($clear_data['id']);
        echo 'Deleted';
    }

    public function restore_password()
    {
        $clear_data = $this->validation->received_data_checkup($_POST);
        $repeat_check = $this->api_model->email_existence_check_up($clear_data['email']);

        if (!($repeat_check)) {
            $temporary_pass = random_str('8');
            $hashed_temporary_password = hash('sha256', $temporary_pass);
            $hashed_temporary_password = sha1($hashed_temporary_password);
            $this->api_model->change_user_password('email', $clear_data['email'], $hashed_temporary_password);
            $this->email->send_message_to_user('temp_pass', $temporary_pass, $clear_data['email']);

            $this->response['error'] = '';
            $this->response['data'] = 'Temporary password was sent to your email';
        } else {
            $this->response['error'] = 'There is no such email in database';
            $this->response['data'] = '';
        }
        echo json_encode($this->response);
    }

    public function change_user_password()
    {
        $clear_data = $this->validation->received_data_checkup($_POST);
        $this->validation->status_token_device_comparing($clear_data['id'], $clear_data['access_token'], $clear_data['device_id']);
        $clear_data = $this->general->remove_unnecessary_fields($clear_data);
        $compare_result = $this->api_model->compare_with_old_password($clear_data['id'], sha1($clear_data['old_password']));

        if ($compare_result) {
            $new_pass = sha1($clear_data['new_password']);
            $this->api_model->change_user_password('id', $clear_data['id'], $new_pass);
            $this->response['error'] = '';
            $this->response['data'] = 'Password updated';
        } else {
            $this->response['error'] = 'Passwords missmatched';
            $this->response['data'] = '';
        }
        echo json_encode($this->response);
    }

    public function update_checker_user_info()
    {
        $clear_data = $this->validation->received_data_checkup($_POST);
        $this->validation->status_token_device_comparing($clear_data['id'], $clear_data['access_token'], $clear_data['device_id']);
        $this->response['data'] = $this->api_model->update_checker($clear_data['id'], 'user');
        echo json_encode($this->response);
    }

    public function update_checker_card()
    {
        $clear_data = $this->validation->received_data_checkup($_POST);
        $this->validation->status_token_device_comparing($clear_data['id'], $clear_data['access_token'], $clear_data['device_id']);
        $this->response['data'] = $this->api_model->update_checker($clear_data['id'], 'card');
        echo json_encode($this->response);
    }

    public function check_done()
    {
        $clear_data = $this->validation->received_data_checkup($_POST);
        $this->validation->status_token_device_comparing($clear_data['id'], $clear_data['access_token'], $clear_data['device_id']);
        $this->api_model->check_done($clear_data['id']);
        $this->response['data'] = 'Check done';
        echo json_encode($this->response);
    }
}
