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
        initDataTables($('#orders_table'), {action: 'dnm-fetch-orders'});

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
