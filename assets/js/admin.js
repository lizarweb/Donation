(function ($) {
    "use strict";
    $(document).ready(function () {

        // InitDataTables.
        function initDataTables(tableId, data) {
            $(tableId).DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                paging: true,
                // pageLength: 25,
                ajax: {
                    url: ajaxurl,
                    type: 'POST',
                    data: data,
                },
            });
        }
        // Fetch Orders
        initDataTables($('#orders_table'), { action: 'dnm-fetch-orders' });


        // Function to handle form submission
        function handleFormSubmit(formId) {
            const form = $(formId);
            form.on('submit', function (e) {
                e.preventDefault();
                const formData = new FormData(this);
                $.ajax({
                    url: form.attr('action'),
                    type: form.attr('method'),
                    data: formData,
                    processData: false,  // tell jQuery not to process the data
                    contentType: false,  // tell jQuery not to set contentType
                    success: function (response) {
                        form.parent().find('.alert').remove();
                        if (response.success) {
                            $('input').removeClass('is-invalid is-valid');
                            $('.invalid-feedback').remove();
                            form.parent().prepend(`<div class="alert alert-success">${response.data.message}</div>`);
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
                                form.parent().prepend(`<div class="alert alert-danger">${response.data.message}</div>`);
                            }
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.error(`Request failed: ${textStatus}`);
                    }
                });
            });
        }

        // Usage
        handleFormSubmit('#dnm-save-order-form');
        handleFormSubmit('#dnm-save-settings-form');
        handleFormSubmit('#dnm-save-payment-settings-form');

        // Delete Order
        $(document).on('click', '.delete-order', function () {
            var orderId = $(this).data('id');
            var nonce = $(this).data('nonce');
            if (confirm('Are you sure you want to delete this order?')) {
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'dnm-delete-order',
                        order_id: orderId,
                        nonce: nonce
                    },
                    success: function (response) {
                        if (response.success) {
                            $('#orders_table').DataTable().ajax.reload();
                        }
                    },
                    error: function (response) {
                        console.log(response);
                    }
                });
            }
        });

        // Copy shortcode to clipboard
        function copyToClipboard(text) {
            var $textarea = $("<textarea>");
            $textarea.text(text);
            $("body").append($textarea);
            $textarea.select();
            try {
                var successful = document.execCommand("copy");
                var msg = successful ? 'successful' : 'unsuccessful';
                console.log('Copying text command was ' + msg);
            } catch (err) {
                console.error('Oops, unable to copy', err);
            }
            $textarea.remove();
        }

        $("#donation_shortcode").on("click", function () {
            var shortCode = $(this).text();
            copyToClipboard(shortCode);
        });
    });
})(jQuery);
