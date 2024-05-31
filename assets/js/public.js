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
                    $('.alert').remove();
                    if (response.success === false) {
                        // Assuming the error message is in response.data
                        // and the keys in response.data correspond to the ids of the input fields
                        for (let field in response.data) {
                            $(`#${field}`).before(`<div style="color: red;" class="alert alert-danger">${response.data[field]}</div>`);
                        }
                    }
                     else {
                        window.location.href = response;
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.error(`Request failed: ${textStatus}`);
                }
            });
        });
    }

    handleFormSubmit('#dnm-save-custom-registration-form');
    handleFormSubmit('#dnm-save-fixed-registration-form');


    function membershipFormSubmit(formId) {
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
                    // Remove all alerts before starting
                    $('.alert').remove();
                    if (response.success === false) {
                        // Assuming the error message is in response.data
                        // and the keys in response.data correspond to the ids of the input fields
                        for (let field in response.data) {
                            $(`#${field}`).before(`<div style="color: red;" class="alert alert-danger">${response.data[field]}</div>`);
                        }
                    } else if (response.message === 'success') {
                        // If the message is 'success', redirect to the URL specified in the 'redirect' field
                        window.location.href = response.redirect;
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.error(`Request failed: ${textStatus}`);
                }
            });
        });
    }

    membershipFormSubmit('#dnm-save-membership-registration-form');

    // when click on the subscription button send ajax request
    $('#subscription-activate-btn').on('click', function (e) {
        e.preventDefault();
        // get values from the form data-order-id
        var orderId = $(this).data('order-id');

        var data = {};
        data['action'] = 'dnm_subscription_form';
        data['order_id'] = orderId;
        // $data = 'action=dnm_subscription_form&order_id=' + orderId;
        $.ajax({
            url: dnmData.ajax_url,
            type: 'POST',
            data: data,
            // processData: false,  // tell jQuery not to process the data
            // contentType: false,  // tell jQuery not to set contentType
            success: function (response) {
                var data = JSON.parse(response);
                console.log(data.code);

                if (data.code === 'SUCCESS') {
                    window.location = data.redirectUrl;

                }

            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error(`Request failed: ${textStatus}`);
            }
        });
    });

    // when click on the subscription button send ajax request
    $('#subscription-verify-btn').on('click', function (e) {
        e.preventDefault();
        // get values from the form data-order-id
        var orderId = $(this).data('order-id');

        var data = {};
        data['action'] = 'dnm_verify_form';
        data['order_id'] = orderId;
        // $data = 'action=dnm_subscription_form&order_id=' + orderId;
        $.ajax({
            url: dnmData.ajax_url,
            type: 'POST',
            data: data,
            // processData: false,  // tell jQuery not to process the data
            // contentType: false,  // tell jQuery not to set contentType
            success: function (response) {
                console.log(response.data.message);
                alert(response.data.message);
                window.location.reload();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error(`Request failed: ${textStatus}`);
            }
        });
    });


})(jQuery);
