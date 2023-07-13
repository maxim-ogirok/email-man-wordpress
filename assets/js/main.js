jQuery(document).ready(function ($) {
    // init select2 for settings page
    $("#email-man-settings-form .select2").select2({
        tags: true
    });

    // Set content to popup on email-man page
    $('body').on('click', '#emailman-readmore', function (e) {
        e.preventDefault();

        var item_id = $(this).data('id');
        $(document).find('#modal-window').html('<div class="content">' + email_man_data[item_id].body + '</div>');
    })
});