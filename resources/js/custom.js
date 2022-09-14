$(document).ready(function () {

    $('.toggle-password').on('click', function () {
        $(this).find('*').toggle();
        var password = $(this).closest('.input-group').find('input').first();
        var type = password.attr('type') === 'password' ? 'text' : 'password';
        password.attr('type', type);
    });

    $('.toggle-content .handle').on('click', function () {
        $(this).closest('.toggle-content').find('> *').toggle();
    });
});
