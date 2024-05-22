(function ($) {
    "use strict";
    $(document).ready(function () {
        $('input[name="type"]').change(function () {
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


    function handleFormSubmit(formId) {
        const form = $(formId);
        form.on('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            $.ajax({
                url: dnmData.ajax_url,
                type: form.attr('method'),
                data: formData,
                processData: false,  // tell jQuery not to process the data
                contentType: false,  // tell jQuery not to set contentType
                success: function (response) {
                    console.log(response);
                    // Remove all alerts before starting
                    // $('.alert').remove();
                    // if (response.success === false) {
                    //     // Assuming the error message is in response.data
                    //     // and the keys in response.data correspond to the ids of the input fields
                    //     for (let field in response.data) {
                    //         $(`#${field}`).before(`<div style="color: red;" class="alert alert-danger">${response.data[field]}</div>`);
                    //     }
                    // } else {
                    //     window.location.href = response;
                    // }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.error(`Request failed: ${textStatus}`);
                }
            });
        });
    }

    handleFormSubmit('#dnm-save-custom-registration-form');
    handleFormSubmit('#dnm-save-fixed-registration-form');
    handleFormSubmit('#dnm-save-membership-registration-form');

})(jQuery);