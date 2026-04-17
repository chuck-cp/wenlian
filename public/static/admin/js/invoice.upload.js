layui.use(['jquery', 'layer', 'upload'], function () {

    var $ = layui.jquery;
    var layer = layui.layer;
    var upload = layui.upload;

    upload.render({
        elem: '#change-voucher',
        url: '/admin/upload/invoice/file',
        accept: 'file',
        exts: 'ofd|pdf',
        before: function () {
            layer.load();
        },
        done: function (res, index, upload) {
            $('input[name=voucher]').val(res.data.path);
            layer.closeAll('loading');
        },
        error: function (index, upload) {
            layer.msg('上传文件失败', {icon: 2});
        }
    });

});
