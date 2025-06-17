(function ($) {
    "use strict";
    var card = new Card({
        form: '#payment-form',
        container: '.card-wrapper',
        formSelectors: {
            numberInput: 'input[name="cardNumber"]',
            expiryInput: 'input[name="cardExpiry"]',
            cvcInput: 'input[name="cardCVC"]',
            nameInput: 'input[name="name"]'
        }
    });
})(jQuery);