<?php

class MY_Email extends CI_Email
{
    protected $email_message;

    protected $user_email;

    protected $email_subject;

    protected $message_templates;

//  MAIN MESSAGE TEMPLATES - Each method is responsable for different template of messages.
//  For adding new template you need to add new method (for mockup use existed methods)
//  and add new "switch-case" point into main method that will lead to newly created template.

    private function send_email_with_picture_path($code, $email, $item_id, $id, $name, $surname)
    {

        $this->user_email = $email;

//        $this->email_subject = 'ORDER #' . $id;

        $this->email_subject = 'ZIR Artwork Purchase Confirmation';

        $this->email_message = '<div style="width:100%; height: 60px; background-color: rgba(255,255,255,.4); color:black;
        font-size: 48px; font-weight: bold; padding-top: 4px; text-align: center;">ZIR</div>
        <h4 style="width:80%; margin-left: 10%; color:black; font-size: 14px; font-weight: lighter; padding-top: 8px; margin-top: 8px; text-align: center;">ZIR Artwork Purchase #' . $id . ' Confirmation</h4>
        <br style="width:80%; margin-left: 10%; color:black; font-size: 14px; font-weight: lighter; padding-top: 8px; margin-top: 8px; text-align: justify;">Hello ' . $name . ' ' . $surname . '. Your order is completed.
          For downloading a digital copy of your purchase click on the following button.<br>
          Please use this manual and attached file to create amazing DIY AR Art objects:<br>
          http://getzir.com/manual<br>
          Kind regards, Dmitry. <br>
          Co-Founder of ZIR</h4>                 
          <a href="https://backend.getzir.com/download_digital_copy/' . $code . '/' . $item_id . '" style="text-decoration: none;"><div style="width:60%; margin-left: 20%;  background-color: #4731CA; color:white;
        font-size: 14px; font-weight: 200; padding: 10px; margin-top: 30px; border-radius: 20px; margin-bottom: 12px; text-align: center;">Download</div>';
    }

    private function send_email_for_free_digital_copy($code, $email, $item_id)
    {

        $this->user_email = $email;

        $this->email_subject = 'ZIR - Purchase';

        $this->email_message = '<div style="width:100%; height: 60px; background-color: rgba(255,255,255,.4); color:black;
        font-size: 48px; font-weight: bold; padding-top: 4px; text-align: center;">ZIR</div>
        <h4 style="width:80%; margin-left: 10%; color:black; font-size: 14px; font-weight: lighter; padding-top: 8px; margin-top: 8px; text-align: center;">Download free item</h4>
        <h4 style="width:80%; margin-left: 10%; color:black; font-size: 14px; font-weight: lighter; padding-top: 8px; margin-top: 8px; text-align: justify;">You get this free digital copy of item. 
        You can download it by pressing button below.<br>
          Please use this manual and attached file to create amazing DIY AR Art objects:<br>
          http://getzir.com/manual<br>
          Kind regards, Dmitry. <br>
          Co-Founder of ZIR</h4>                 
         <a href="https://backend.getzir.com/download_digital_copy/' . $code . '/' . $item_id . '" style="text-decoration: none;"><div style="width:60%; margin-left: 20%;  background-color: #4731CA; color:white;
        font-size: 14px; font-weight: 200; padding: 10px; margin-top: 30px; border-radius: 20px; margin-bottom: 12px; text-align: center;">Download</div>   </a> ';
    }

    private function resend_email_picture_path($code, $email, $item_id)
    {

        $this->user_email = $email;

        $this->email_subject = 'ZIR - Purchase';

        $this->email_message = '<div style="width:100%; height: 60px; background-color: rgba(255,255,255,.4); color:black;
        font-size: 48px; font-weight: bold; padding-top: 4px; text-align: center;">ZIR</div>
        <h4 style="width:80%; margin-left: 10%; color:black; font-size: 14px; font-weight: lighter; padding-top: 8px; margin-top: 8px; text-align: center;">Purchased item</h4>
        <h4 style="width:80%; margin-left: 10%; color:black; font-size: 14px; font-weight: lighter; padding-top: 8px; margin-top: 8px; text-align: justify;">You requested your purchased digital item. 
        You can download it by pressing button below.<br>
          Please use this manual and attached file to create amazing DIY AR Art objects:<br>
          http://getzir.com/manual<br>
          Kind regards, Dmitry. <br>
          Co-Founder of ZIR</h4>                 
         <a href="https://backend.getzir.com/download_digital_copy/' . $code . '/' . $item_id . '" style="text-decoration: none;"><div style="width:60%; margin-left: 20%;  background-color: #4731CA; color:white;
        font-size: 14px; font-weight: 200; padding: 10px; margin-top: 30px; border-radius: 20px; margin-bottom: 12px; text-align: center;">Confirm</div>   </a> ';
    }

    private function send_email_validation_code($code, $email)
    {
        $this->user_email = $email;

        $this->email_subject = 'ZIR - Email validation';

        $this->email_message = '<div style="width:100%; height: 60px; background-color: rgba(255,255,255,.4); color:black;
        font-size: 48px; font-weight: bold; padding-top: 4px; text-align: center;">ZIR</div>
        <h4 style="width:80%; margin-left: 10%; color:black; font-size: 14px; font-weight: lighter; padding-top: 8px; margin-top: 8px; text-align: center;">Email confirmation</h4>
        <h4 style="width:80%; margin-left: 10%; color:black; font-size: 14px; font-weight: lighter; padding-top: 8px; margin-top: 8px; text-align: justify;">Please confirm your email by pressing button below.
        You will be continued to registration process.</h4>                 
         <a href="https://backend.getzir.com/api/validation/' . $code . '" style="text-decoration: none;"><div style="width:60%; margin-left: 20%;  background-color: #4731CA; color:white;
        font-size: 14px; font-weight: 200; padding: 10px; margin-top: 30px; border-radius: 20px; margin-bottom: 12px; text-align: center;">Confirm</div>   </a> ';
    }

    private function send_temporary_pass($code, $email)
    {
        $this->user_email = $email;

        $this->email_subject = 'ZIR - Password recovery';

        $this->email_message = '<div style="width:100%; height: 60px; background-color: rgba(255,255,255,.4); color:black;
        font-size: 48px; font-weight: bold; padding-top: 4px; text-align: center;">ZIR</div>
        <h4 style="width:80%; margin-left: 10%; color:black; font-size: 14px; font-weight: lighter; padding-top: 8px; margin-top: 8px; text-align: center;">Password recovery</h4>
        <h4 style="width:80%; margin-left: 10%; color:black; font-size: 14px; font-weight: lighter; padding-top: 8px; margin-top: 8px; text-align: justify;">You requested recovering of your password. We sent you temporary password which you can use to enter your account. 
         In case of security you should delete this messsage and change password on your own after you login in your account .</h4>                 
         <div style="width:60%; margin-left: 20%;  color:black;
        font-size: 14px; font-weight: 200; padding: 10px; margin-top: 30px; margin-bottom: 12px; text-align: center;">' . $code . '</div>';
    }

    private function send_all_items_links($email, $item_id, $name, $surname)
    {

        $this->user_email = $email;

        $this->email_subject = 'Full list of purchased items';

        $this->email_message = '<div style="width:100%; height: 60px; background-color: rgba(255,255,255,.4); color:black;
        font-size: 48px; font-weight: bold; padding-top: 4px; text-align: center;">ZIR</div>
        <h4 style="width:80%; margin-left: 10%; color:black; font-size: 14px; font-weight: lighter; padding-top: 8px; margin-top: 8px; text-align: center;">Full list of purchased items</h4>
        <h4 style="width:80%; margin-left: 10%; color:black; font-size: 14px; font-weight: lighter; padding-top: 8px; margin-top: 8px; text-align: justify;">Hello ' . $name . ' ' . $surname . '. You request to restore all your purchased items.
          List with all your purchased items and related links is posted below.
          For downloading a digital copy of your purchase click on the following buttons.<br>
          Kind regards, Dmitry. <br>
          Co-Founder of ZIR</h4><br>';
        foreach ($item_id as $key=>$value) {

            $this->email_message .= '<h4 style="display:inline-block; margin-left: 5%; width:50%; font-size: 14px; font-weight: 200; padding: 10px; margin-top: 15px; margin-bottom: 12px; text-align: left;">' . $key . ': </h4><a href="https://backend.getzir.com/download_digital_copy/'.$value.'" style="text-decoration: none;"><div style="display:inline-block; width:25%; background-color: #4731CA; color:white; font-size: 12px; font-weight: 200; padding: 10px; margin-top: 15px; border-radius: 20px; margin-bottom: 12px; text-align: center;">Download</div></a>';

        }
    }

//  SEND EMAIL TO USER - Main method which used for sending different emails to customers.
//  This is main point for entering into this subclass.

    public function send_message_to_user($type, $code, $email, $item_id = '', $id = '', $name = '', $surname = '')
    {


        switch ($type) {
            case 'order':
                $this->send_email_with_picture_path($code, $email, $item_id, $id, $name, $surname);
                break;
            case 'free':
                $this->send_email_for_free_digital_copy($code, $email, $item_id);
                break;
            case 'resend':
                $this->resend_email_picture_path($code, $email, $item_id);
                break;
            case 'v_code':
                $this->send_email_validation_code($code, $email);
                break;
            case 'temp_pass':
                $this->send_temporary_pass($code, $email);
                break;
            case 'send_all':
                $this->send_all_items_links($email, $item_id, $name, $surname);
                break;
        }

        $this->from('orders@getzir.com', 'ZIR');
        $this->to($this->user_email);
        $this->subject($this->email_subject);
        $this->message($this->email_message);
        $this->send();
    }

}