layui.use(['jquery', 'layer'], function () {

    var $ = layui.jquery;
    var layer = layui.layer;

    $('body').on('click', '.report', function () {
        var id = $(this).data('id');
        var url = '/exam/question/' + id + '/report';
        layer.open({
            type: 2,
            title: '试题挑错',
            content: [url, 'no'],
            area: ['720px', '250px'],
        });
    });

});