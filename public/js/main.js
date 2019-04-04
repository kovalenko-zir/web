window.onload = function () {
    $Height = window.innerHeight;
    $Width = window.innerWidth;

    $congrats_img = $('#congrats_image_row img').outerHeight();
    $congrats_header = $('.congrats_header').outerHeight();
    $congrats_text = 110;

    $margin_congrats = ($Height - $congrats_img - $congrats_header - $congrats_text) / 4;
    $('#congrats_image_row').css('margin-top', $margin_congrats);
    $('#congrats_image_row').css('margin-bottom', $margin_congrats);

    $loader_height = $('.lds-dual-ring').outerHeight();
    $loader_width = $('.lds-dual-ring').outerWidth();

    $loader_margin_top = ($Height - $loader_height) / 2;
    $loader_margin_left = ($Width - $loader_width) / 2;

    $('.lds-dual-ring').css('margin-top', $loader_margin_top);
    $('.lds-dual-ring').css('margin-left', $loader_margin_left);

    $img_height = $('.img_holder img').outerHeight();
    $art_name_height = $('.art_name').outerHeight();
    $artist_name_height = $('.artist_name').outerHeight();
    $art_cost_height = $('.art_cost').outerHeight();

    $margin = $img_height - $art_name_height - $artist_name_height - $art_cost_height;

    $('.art_cost').css('margin-top', $margin);

    $min_height = $Height - $('.img_holder img').outerHeight() - $('#checkout_button_block').outerHeight() - 55;
    $('#min_height_block').css('min-height', $min_height);

    checkoutDataValueCounter()
};


//MASKS

$.mask.definitions['~'] = '[01]';
$.mask.definitions['&'] = '[/]';
$.mask.definitions['@'] = '[0-9]';
$('#card_number').mask('@@@@ @@@@ @@@@ @@@@', {placeholder: " "});
$('#expiration_date').mask('~9/99', {placeholder: " "});


//CHECKOUT CLASS

function correctValueCounter() {
    $arr = $validator.CorrectValueCounter;
    for ($key in $arr) {
        if ($arr[$key] === '') {
            return false;
        }
    }
    return true;
}

function checkoutDataValueCounter() {
    $arr = $checkout.СheckoutData
    if ($('#type').val() === 'digital') {
        if ($arr['order_email'] === '' || $arr['fourNum'] === '') {
            return false;
        }
    }
    else {
        for ($key in $arr) {
            if ($arr[$key] === '') {
                return false;
            }
        }
    }
    $('#checkout_button').removeClass('not_active')
    $checkout.Status = 1;
    return true;
}

function CardNumberCheckUp(value) {
    if ($validator.Identificator === 'card_number' && value !== '') {
        var card_number = value.replace(/\s/g, "");
        var arr = [],
            card_number = card_number.toString();
        for (var i = 0; i < card_number.length; i++) {
            if (i % 2 === 0) {
                var m = parseInt(card_number[i]) * 2;
                if (m > 9) {
                    arr.push(m - 9);
                } else {
                    arr.push(m);
                }
            } else {
                var n = parseInt(card_number[i]);
                arr.push(n)
            }
        }
        var summ = arr.reduce(function (a, b) {
            return a + b;
        });
        return Boolean(!(summ % 10));
    }
    else {
        return true
    }
}

function add_purchase() {
    $('#wrapper_loader').css('display', 'block');
    $data = JSON.stringify($checkout.СheckoutData);
    $.ajax({
        url: '../direct',
        type: "POST",
        data: {
            data: $data
        },
        success: function (data) {
            $d = data;
            if (data === 'completed') {
                $('#wrapper_loader').css('display', 'none');
                $checkout.ShowCongratsScreen = 'Order completed. Confirmation with the </br> download link was sent to your email.'
            }
        }
    })
}

$checkout = new Checkout();
$validator = new Validator();

// INPUT VALIDATION BLOCK

$('#contact_info_block').click(function () {
        $block = $validator.OpenBlock = 'wrapper_ci';
        $validator.SetActionButton = 'add_ci_button';
        $validator.ValuesQuantity = $('#wrapper_ci input').length
        $validator.correctValueCounter = 'wrapper_ci';
    }
);

$('#shipping_info_block').click(function () {
        $validator.OpenBlock = $block = 'wrapper_si';
        $validator.SetActionButton = 'add_ship_button';
        $validator.ValuesQuantity = $('#wrapper_si input').length
        $validator.correctValueCounter = 'wrapper_si';
    }
);

$('#user_add_payment_method').click(function () {
        $validator.OpenBlock = $block = 'wrapper_pi';
        $validator.SetActionButton = 'add_method_button';
        $validator.ValuesQuantity = $('#wrapper_pi input').length;
        $validator.correctValueCounter = 'wrapper_pi';
    }
);

$('div').on('click touch', '#cancel_block', function () {
    $validator.CloseBlock = $validator.Opened_block;
    $validator.cleaner();
});

// PAYMENT METHOD

$('body').on('click touch', '.payment_method', function () {
    $('.payment_method').removeClass('chosen_method');
    $(this).addClass('chosen_method');
    $checkout.СheckoutData['fourNum'] = $('.chosen_method .fourNum').text();
    checkoutDataValueCounter()
});

//INPUTS CHECKOUT

$('body').on('blur', '.field', function () {

    $('#masked_input').detach();
    $w = $validator.Opened_block;
    $id = $validator.identificator = $(this).attr('id');
    $val = $validator.inputValue = $(this).val();
    $(this).addClass('checking');
    $emptyness = $validator.InputEmptynessCheck();
    $only_spaces_existence = $validator.SpacesChecker();
    $pattern_equality = $validator.PatternCheckup();
    $card_checkup = CardNumberCheckUp($val.replace(/\s/g, ""));
    if ($emptyness && $only_spaces_existence && $pattern_equality && $card_checkup) {
        $('.' + $id).detach();
        $('.checking').parent().removeClass('error_borders');
        $checkout.СheckoutData[$id] = $val;
        $validator.CorrectValueCounter[$id] = $val;

    } else {
        $msg = 'Incorrect value';
        $checkout.СheckoutData[$id] = '';
        $validator.CorrectValueCounter[$id] = '';
        if (!$emptyness || !$only_spaces_existence) {
            $msg = 'Field is empty';

        }
        $('#' + $id).parent().addClass('error_borders');
        $('.' + $('.checking').attr('id')).detach();
        $wrapper = $('.checking').parent().parent().parent();
        $wrapper.find('.error_description').prepend('<span class="' + $('.checking').attr('id') + '">' + $msg + '</span>').css('visibility', 'visible');
    }
    $(this).removeClass('checking')
});

$('.field').on('change', function () {
    $('#cancel_block').text('Save changes').attr('id', $validator.GetActionButton);
});

$('select').change(function () {
    $(this).removeClass('empty_country');
    $('.remove_this_option').detach();
});

$('.pi_inputs').click(function () {
    if ($(this).val() === '') {
        $(this).after('<div id="masked_input"></div>');
        if ($(this).attr('id') === 'expiration_date') {
            $(this).css('color', 'transparent');
        }
        if ($(this).attr('id') === 'card_number') {
            $('#masked_input').text('1234 1234 1234 1234')
        } else if ($(this).attr('id') === 'expiration_date') {
            $(this).css('color', 'transparent');
            $('#masked_input').text('MM/YY')
        } else if ($(this).attr('id') === 'cvv') {
            $(this).css('color', 'transparent');
            $('#masked_input').text('123')
        }
    }
});

$('.pi_inputs').keydown(function () {
    $('#masked_input').detach();
    $(this).css('color', 'rgba(0, 0, 0, 1)');
});

//CHECKOUT BUTTON CLICK

$('body').on('click touch', '#checkout_button', function () {
    if ($checkout.status === 1 && $('#checkout_button').hasClass('not_active') !== true) {
        $d = add_purchase();
    }
});

// CHANGES SAVING

$('div').on('click touch', '#add_ci_button', function () {
    if (correctValueCounter()) {
        $('.fir_name').html($validator.CorrectValueCounter['first_name']);
        $('.la_name').html($validator.CorrectValueCounter['last_name']);
        $('.email').html($validator.CorrectValueCounter['order_email']);
        checkoutDataValueCounter();
        $validator.CloseBlock = $validator.Opened_block;
    }
});

$('div').on('click touch', '#add_ship_button', function () {
    if (correctValueCounter()) {
        $.ajax({
            url: '../Purchases/user_shipping_info_update',
            type: "POST",
            data: {
                data: JSON.stringify($checkout.СheckoutData)
            },
            success: function (data) {

            }
        });
        $('.zip_code').html($validator.CorrectValueCounter['order_zip_code']);
        $('.country').html($validator.CorrectValueCounter['order_country']);
        $('.city').html($validator.CorrectValueCounter['order_city']);
        $('.address_line_one').html($validator.CorrectValueCounter['order_adress']);
        $('.address_line_two').html($validator.CorrectValueCounter['order_appartment']);
        checkoutDataValueCounter();
        $validator.CloseBlock = $validator.Opened_block;
    }

});

$('#wrapper_pi').on('click touch', '#add_method_button', function () {
    if (correctValueCounter()) {
        $('#wrapper_loader').css('display', 'block');
        $data = {
            'app_id': $('#app_identificator').val(),
            'device_id': $('#device_identificator').val(),
            'access_token': $('#access_token').val(),
            'id': $('#user_identificator').val(),
            'cardholder_name': $validator.CorrectValueCounter['cardholder_name'],
            'cvv': $validator.CorrectValueCounter['cvv'],
            'card_number': $validator.CorrectValueCounter['card_number'],
            'expiration_date': $validator.CorrectValueCounter['expiration_date']
        };
        $data = JSON.stringify($data);
        $.ajax({
            url: '../add_card',
            type: "POST",
            data: {
                data: $data
            },
            success: function (data) {
                $data2 = JSON.parse(data);
                $('#user_add_payment_method').before('<div class="col-12 user_payment_card payment_method"><img class="mastercard_icon" src="/public/images/' + $data2.type + '.png">●●●● <span class="fourNum">' + $data2.fourNum + '</span><img class="checked_icon" src="/public/images/checked.png"></div>');
                $('#wrapper_loader').css('display', 'none');
                $validator.CloseBlock = $validator.Opened_block;

            }
        })
    }

});

// FREE OR PURCHASED PICTURE SENDER

$('body').on('click touch', '#free_sender_button, #purchased_item_button', function () {
    $checkout.ResendOrGetFreeCopy
});