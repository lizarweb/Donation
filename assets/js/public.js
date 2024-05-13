(function ($) {
    "use strict";
  $(document).ready(function () {
    $('input[name="type"]').change(function() {
        var customAmountInput = $('#amount');
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