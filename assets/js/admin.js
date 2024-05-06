jQuery(document).ready(function($) {
    // Your code goes here.
    // You can use $ as your jQuery object.

    // DataTables
    $('#orders').DataTable({
        responsive: true,
        pageLength: 25,
    });

    function copyToClipboard(text) {
        var $textarea = $('<textarea>');
        $textarea.text(text);
        $('body').append($textarea);
        $textarea.select();
        document.execCommand("copy");
        $textarea.remove();
    }
    
    $('#donation_shortcode').on('click', function() {
        var shortcode = $(this).text();
        copyToClipboard(shortcode);
    });
});