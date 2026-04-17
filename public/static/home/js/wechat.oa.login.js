layui.use(['jquery'], function () {

    var $ = layui.jquery;
    var interval = null;
    var ticket = null;
    var returnUrl = $('input[name=return_url]').val();

    showQrCode();

    function showQrCode() {
        $.get('/wechat/oa/login/qrcode', function (res) {
            ticket = res.qrcode.ticket;
            var html = '<img alt="关注微信公众号" src="' + res.qrcode.url + '">';
            $('.wechat-scan-box > .qrcode').html(html);
            interval = setInterval(function () {
                queryStatus();
            }, 1500);
        });
    }

    function queryStatus() {
        $.get('/wechat/oa/login/status', {
            ticket: ticket
        }, function (res) {
            if (res.data.action === 'login') {
                $.post('/wechat/oa/auth/login', {
                    ticket: ticket,
                    return_url: returnUrl
                }, function (res) {
                    window.location.href = res.location;
                });
            } else if (res.data.action === 'bind') {
                window.location.href = '/wechat/oa/bind?ticket=' + ticket + '&return_url=' + encodeURIComponent(returnUrl);
            }
        });
    }

});
