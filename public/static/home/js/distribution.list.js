layui.use(['jquery', 'layer', 'helper'], function () {

    var $ = layui.jquery;
    var helper = layui.helper;

    $('body').on('click', '.share', function () {
        var url = $(this).data('url');
        helper.checkLogin(function () {
            layer.open({
                type: 2,
                title: '商品分享',
                content: [url, 'no'],
                area: ['640px', '320px']
            });
        });
        return false;
    });

    var $distList = $('#dist-list');

    if ($distList.length > 0) {
        helper.ajaxLoadHtml($distList.data('url'), $distList.attr('id'));
    }

});
