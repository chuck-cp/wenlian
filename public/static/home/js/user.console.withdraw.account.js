layui.use(['jquery', 'form'], function () {

    var $ = layui.jquery;
    var form = layui.form;

    form.on('select(channel)', function (data) {
        var $block = $('#channel-block');
        var $label = $('#channel-label');
        if (data.value !== '') {
            $block.show();
        }
        if (data.value === '1') {
            $label.text('支付宝帐号');
        } else if (data.value === '2') {
            $label.text('微信号');
        }
    });

});