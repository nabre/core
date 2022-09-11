$(document).ready(function () {

    $('.toggle-password').on('click', function () {
        $(this).find('*').toggle();
        var password = $(this).closest('.input-group').find('input').first();
        var type = password.attr('type') === 'password' ? 'text' : 'password';
        password.attr('type', type);
    });

    /*
        var $sortable = $("tbody[data-orderby]");
        $sortable.sortable({
            items: "tr",
            cursor: 'grabbing',
            handle: '.handle',
            opacity: 0.6,
            update: function () {
                var items = $(this).sortable('toArray', { attribute: 'data-id' });
                var ids = $.grep(items, (item) => item !== '');

                $.post($(this).data('orderby'), {
                    ids
                });
            }
        });*/

});
