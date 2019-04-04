//VALIDATOR CLASS

class Validator {

    constructor() {
        this.Opened_block = '';

        this.Action_button = '';

        this.Values_quantity = 0;

        this.Identificator = '';
        this.InputValue = '';
        this.CorrectValueCounter = {};
        this.counter = 0;

        this.patterns_array = {};
        this.patterns_array['order_email'] = /^([a-z0-9_.-])+@[a-z0-9-]+.([a-z]{2,4}.)?[a-z]{2,4}$/i;
        this.patterns_array['order_zip_code'] = /\d{5}/;
        this.patterns_array['card_number'] = /\d{4}\s\d{4}\s\d{4}\s\d{4}/;
        this.patterns_array['expiration_date'] = /(0[1-9]|1[012])+\/(1[9]|2[0-9])/;
        this.patterns_array['cvv'] = /\d{3}/;
    }

    set identificator(value) {
        this.Identificator = value;
    }

    set inputValue(value) {
        this.InputValue = value;
    }

    set correctValueCounter(value) {
        this.counter = 0;
        this.CorrectValueCounter = {};
        for (this.counter; this.counter < $('#' + value + ' .field').length; this.counter++) {
            this.CorrectValueCounter[$('#' + value + ' .field')[this.counter].id] = $('#' + value + ' .field')[this.counter].value;
        }

    }

    set OpenBlock(value) {
        this.Opened_block = value;
        $('#' + value).find('.row').append('<div id="cancel_block" class="offset-1 col-10 button">Cancel changes</div>');
        $('#' + value).addClass('swipe_in');
        setTimeout(function () {
            $('#' + value).removeClass('swipe_in').css('left', 0);
        }, 100);
    }

    set CloseBlock(value) {

        $('#' + value).addClass('swipe_out');
        setTimeout(function () {
            $('#' + value).removeClass('swipe_out').css('left', '100%');
            $('#cancel_block').detach();
        }, 100);
    }

    set SetActionButton(value) {
        this.Action_button = value;
    }

    get GetActionButton() {
        return this.Action_button
    }

    set ValuesQuantity(value) {
        this.Values_quantity = value;
    }

    get ValuesQuantity() {
        return this.Values_quantity
    }

    cleaner() {
        this.CorrectValueCounter = {};

    }

    InputEmptynessCheck() {
        return (this.InputValue !== '');
    }

    SpacesChecker() {
        return (this.InputValue.replace(/\s/g, "").length > 0);
    }

    PatternCheckup() {
        if (this.patterns_array[this.Identificator] !== undefined) {
            return this.patterns_array[this.Identificator].test(this.InputValue);
        } else {
            return true
        }
    }
}