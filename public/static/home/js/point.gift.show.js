layui.use(['jquery', 'form', 'layer'], function () {

    var $ = layui.jquery;
    var form = layui.form;
    var layer = layui.layer;

    form.on('submit(redeem)', function (data) {
        var submit = $(this);
        layer.confirm('兑换不支持退换，确定要兑换吗？', function () {
            submit.attr('disabled', 'disabled').addClass('layui-btn-disabled');
            $.ajax({
                type: 'POST',
                url: data.form.action,
                data: data.field,
                success: function (res) {
                    layer.msg(res.msg, {icon: 1});
                    setTimeout(function () {
                        window.location.href = res.location;
                    }, 3000);
                },
                error: function () {
                    submit.removeAttr('disabled').removeClass('layui-btn-disabled');
                }
            });
        });
        return false;
    });

});