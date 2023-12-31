; (function ($) {

    // console.log('Admin js');

    $('.npp-username-input').on('change', function () {
        let username = $(this).val();

        let url = new URL(window.location.href);
        url.searchParams.set("username", username);
        window.location.href = url.href;
    });
})(jQuery);
