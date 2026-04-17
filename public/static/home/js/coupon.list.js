layui.use(['jquery', 'layer', 'helper'], function () {

    var $ = layui.jquery;
    var layer = layui.layer;
    var helper = layui.helper;

    $('.package-link').on('click', function () {
        var url = $(this).data('url');
        layer.open({
            type: 2,
            title: '套餐课程',
            content: url,
            area: ['800px', '280px']
        });
    });

    $('body').on('click', '.btn-claim', function () {
        var $this = $(this);
        helper.checkLogin(function () {
            $.ajax({
                type: 'POST',
                url: $this.data('url'),
                success: function () {
                    $this.text('已经领取').removeClass('btn-claim').addClass('layui-btn-disabled');
                }
            });
        });
    });

    var $couponList = $('#coupon-list');

    if ($couponList.length > 0) {
        helper.ajaxLoadHtml($couponList.data('url'), $couponList.attr('id'));
    }

});
