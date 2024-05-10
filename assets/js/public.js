(function ($) {
    "use strict";
    $(document).ready(function () {
        $('#dnm-amount').change(function() {
            var customAmountInput = $('#customAmount');
            if ($(this).val() === 'custom') {
                customAmountInput.show();
                customAmountInput.prop('required', true);
            } else {
                customAmountInput.hide();
                customAmountInput.prop('required', false);
            }
        });
    });
})(jQuery);