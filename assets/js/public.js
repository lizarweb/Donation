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

    function handleFormSubmitRef(formId) {
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

                    if (response.success) {
                        $('input').removeClass('is-invalid is-valid');
                        $('.invalid-feedback').remove();
                        form.parent().prepend(`<div class="alert alert-success">${response.data.message}</div>`);
                        
                        // Reload the page after 3 seconds (3000 milliseconds)
                        setTimeout(function() {
                            window.location.reload();
                        }, 1000);
                    } else {
                        const errors = response.data;
                        if (errors) {
                            $('input').removeClass('is-invalid is-valid');
                            $('.invalid-feedback').remove();
                            $.each(errors, function (key, value) {
                                $(`#${key}`).removeClass('is-valid').addClass('is-invalid');
                                const feedbackId = `${key}Feedback`;
                                $(`#${feedbackId}`).remove();
                                $(`#${key}`).after(`<div id="${feedbackId}" class="invalid-feedback">${value}</div>`);
                            });
                            // form.parent().prepend(`<div class="alert alert-danger">${response.data}</div>`);
                            console.error(response.data);
                        }
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.error(`Request failed: ${textStatus}`);
                }
            });
        });
    }
    handleFormSubmitRef('#dnm-save-fixed-registration-form_ref');

    function membershipFormSubmit(formId) {
        const form = $(formId);
        form.on('submit', function (e) {
            e.preventDefault();
            
            // Get the submit button and its original text
            const submitButton = $(this).find('button[type="submit"]');
            const originalButtonText = submitButton.text();
            
            // Disable the submit button and change its text to 'Loading...'
            submitButton.prop('disabled', true).text('Loading...');
    
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
                        // Enable the submit button and restore its original text
                        submitButton.prop('disabled', false).text(originalButtonText);
                        
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
                    // Enable the submit button and restore its original text
                    submitButton.prop('disabled', false).text(originalButtonText);
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

    function dnmPrint(targetId, title, styleSheets, css = '') {
        var target = $(targetId).html();

        var frame = $('<iframe />');
        frame[0].name = 'frame';
        frame.css({ 'position': 'absolute', 'top': '-1000000px' });

        var that = frame.appendTo('body');
        var frameDoc = frame[0].contentWindow ? frame[0].contentWindow : frame[0].contentDocument.document ? frame[0].contentDocument.document : frame[0].contentDocument;
        frameDoc.document.open();

        // Create a new HTML document.
        frameDoc.document.write('<html><head>' + title);
        frameDoc.document.write('</head><body>');

        // Append the external CSS file.
        styleSheets.forEach(function (styleSheet, index) {
            $(that).contents().find('head').append('<link href="' + styleSheet + '" rel="stylesheet" type="text/css" referrerpolicy="origin" />');
        });

        if (css) {
            frameDoc.document.write('<style>' + css + '</style>');
        }

        // Append the target.
        frameDoc.document.write(target);
        frameDoc.document.write('</body></html>');
        frameDoc.document.close();

        setTimeout(function () {
            window.frames["frame"].focus();
            window.frames["frame"].print();
            frame.remove();
        }, 1000);
    }

    // $(document).on('click', '#dnm-print-invoice', function() {
    //     var targetId = '#printableArea';
    //     var title = $(this).data('title');
    //     if(title) {
    //         title = '<title>' + title  + '</title>';
    //     }
    //     var styleSheets = $(this).data('styles');

    //     dnmPrint(targetId, title, styleSheets);
    // });


})(jQuery);
