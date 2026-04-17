layui.use(['jquery', 'helper'], function () {

    var $ = layui.jquery;
    var helper = layui.helper;

    setInterval(function () {
        window.location.reload();
    }, 60000);

    $('.order').on('click', function () {
        var url = $(this).data('url');
        helper.checkLogin(function () {
            $.ajax({
                type: 'POST',
                url: url,
                success: function (res) {
                    window.location.href = res.location;
                }
            });
        });
    });

});