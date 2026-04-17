layui.use(['jquery', 'layer', 'upload'], function () {

    var $ = layui.jquery;
    var layer = layui.layer;
    var upload = layui.upload;

    var extsA = ['ppt', 'pptm', 'pptx', 'pot', 'potm', 'potx', 'pps', 'ppsm', 'ppsx', 'dps', 'dpt'];
    var extsB = ['doc', 'docm', 'docx', 'dot', 'dotm', 'dotx', 'wps', 'wpt'];
    var extsC = ['xls', 'xlsb', 'xlsm', 'xlsx', 'xlt', 'xltm', 'xltx', 'et', 'ett', 'csv'];
    var extsD = ['pdf', 'txt'];
    var exts = [...extsA, ...extsB, ...extsC, ...extsD];

    upload.render({
        elem: '#upload-doc',
        url: '/admin/upload/doc/file',
        accept: 'file',
        size: 100000,
        exts: exts.join('|'),
        before: function () {
            layer.load();
        },
        done: function (res, index, upload) {
            $('input[name=upload_path]').val(res.data.path);
            $('input[name=upload_id]').val(res.data.id);
            layer.closeAll('loading');
        },
        error: function (index, upload) {
            layer.msg('上传文件失败', {icon: 2});
        }
    });

});
