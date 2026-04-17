layui.use(['jquery', 'form', 'layer'], function () {

    var $ = layui.jquery;
    var form = layui.form;
    var layer = layui.layer;

    var target = $('input[name="target"]').val();
    var count = $('input[name="count"]').val();

    if (target === 'export' && count === '0') {
        layer.msg('没有检索到相关结果');
    }

    if (target === 'export') {
        form.on('submit(search)', function () {
            var submit = $(this);
            var orgText = $(this).text();
            submit.text('处理中···').addClass('layui-btn-disabled');
            setTimeout(function () {
                submit.text(orgText).removeClass('layui-btn-disabled');
            }, 5000);
            return true;
        });
    }

});