class Checkout {

    constructor() {
        this.status = 0;
        this.СheckoutData = {};
        this.СheckoutData['first_name'] = $('#first_name').val();
        this.СheckoutData['last_name'] = $('#last_name').val();
        this.СheckoutData['order_email'] = $('#order_email').val();

        this.СheckoutData['order_zip_code'] = $('#order_zip_code').val();
        this.СheckoutData['order_country'] = $('#order_country').val();
        this.СheckoutData['order_city'] = $('#order_city').val();
        this.СheckoutData['order_adress'] = $('#order_adress').val();
        this.СheckoutData['order_appartment'] = $('#order_appartment').val();

        this.СheckoutData['user_id'] = $('#user_identificator').val();
        this.СheckoutData['item_id'] = $('#item_identificator').val();
        this.СheckoutData['order_cost'] = $('#total_art_cost').val();
        this.СheckoutData['order_type'] = $('#type').val();

        this.СheckoutData['user_id'] = $('#user_identificator').val();
        this.СheckoutData['device_id'] = $('#device_identificator').val();
        this.СheckoutData['app_id'] = $('#app_identificator').val();
        this.СheckoutData['acces_token'] = $('#access_token').val();
        this.СheckoutData['order_id']=null;
        this.СheckoutData['fourNum']='';
    }

    set Status(value) {
        this.status = value;
    }

    set ShowCongratsScreen(value) {
        $('.congrats_text h3').html(value);

        $('#wrapper_congrats').css('visibility','visible');
    }

    get ResendOrGetFreeCopy () {
        $.ajax({
            url: '../purchases/get_purchased_or_free_digital_copy',
            type: "POST",
            data: {
                data: JSON.stringify(this.СheckoutData)
            },
            success: function (data) {
                if (data === 'free') {
                    $checkout.ShowCongratsScreen = 'Download link for this free </br> item was sent to your email.';
                } else if (data === 'resend') {
                    $checkout.ShowCongratsScreen = 'Check your email. Message with </br> download link was sent to your inbox.';
                }
            }
        })

    }
}