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
                pageLength: 25,
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
            var form = $(formId);
            form.on('submit', function (e) {
                e.preventDefault();
                $.ajax({
                    url: form.attr('action'),
                    type: form.attr('method'),
                    data: form.serialize(),
                    success: function (response) {
                        form.parent().find('.alert').remove();
                        if (response.success) {
                            // If the request is successful, remove any existing alert and show the success message
                            $('input').removeClass('is-invalid is-valid');
                            $('.invalid-feedback').remove();
                            form.parent().prepend('<div class="alert alert-success">' + response.data.message + '</div>');
                        } else {
                            var errors = response.data; // Access the 'data' property of the response
                            if (errors) {
                                $('input').removeClass('is-invalid is-valid');
                                $('.invalid-feedback').remove();
                                $.each(errors, function (key, value) {
                                    $('#' + key).removeClass('is-valid').addClass('is-invalid'); // Add invalid class to the input
                                    var feedbackId = key + 'Feedback';
                                    $('#' + feedbackId).remove(); // Remove old messages
                                    $('#' + key).after('<div id="' + feedbackId + '" class="invalid-feedback">' + value + '</div>'); // Add new messages
                                });
                                // Add an alert at the top of the form with the error message
                                form.parent().prepend('<div class="alert alert-danger">' + response.data.message + '</div>');
                            }
                        }
                    },
                    error: function (response) {
                        console.log(response);
                    }
                });
            });
        }

        // Usage
        handleFormSubmit('#dnm-save-order-form', 'Order saved successfully!', 'Error saving order.');

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
