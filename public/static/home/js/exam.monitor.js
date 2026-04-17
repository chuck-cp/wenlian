layui.use(['jquery', 'layer'], function () {

    var $ = layui.jquery;
    var layer = layui.layer;

    var showProtectMask = function () {
        var content = '<div class="layui-font-red layui-padding-4">请专心答题啦，不要三心二意！</div>';
        layer.open({
            id: 'anti-switch-mask',
            type: 1,
            title: '系统提示',
            area: ['400px', '160px'],
            shade: 0.95,
            content: content,
        });
    }

    $(document).on('visibilitychange', function () {
        if (document.hidden) {
            showProtectMask();
        }
    });

    $(window).on('blur', function () {
        showProtectMask();
    });

});
