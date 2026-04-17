layui.use(['jquery', 'helper'], function () {

    var $ = layui.jquery;
    var helper = layui.helper;

    var myShare = {
        title: $('input[name="share.title"]').val(),
        pic: $('input[name="share.pic"]').val(),
        url: $('input[name="share.url"]').val(),
        qrcode: $('input[name="share.qrcode"]').val()
    };

    $('.icon-wechat').on('click', function () {
        helper.checkLogin(function () {
            helper.wechatShare(myShare.qrcode);
        });
    });

    $('.icon-qq').on('click', function () {
        helper.checkLogin(function () {
            var title = '好物分享：' + myShare.title + '，快来和我一起学习吧！';
            helper.qqShare(title, myShare.url, myShare.pic);
        });
    });

    $('.icon-qzone').on('click', function () {
        helper.checkLogin(function () {
            var title = '好物分享：' + myShare.title + '，快来和我一起学习吧！';
            helper.qzoneShare(title, myShare.url, myShare.pic);
        });
    });

    $('.icon-weibo').on('click', function () {
        helper.checkLogin(function () {
            var title = '好物分享：' + myShare.title + '，快来和我一起学习吧！';
            helper.weiboShare(title, myShare.url, myShare.pic);
        });
    });

    $('.icon-douban').on('click', function () {
        helper.checkLogin(function () {
            var title = '好物分享：' + myShare.title + '，快来和我一起学习吧！';
            helper.doubanShare(title, myShare.url, myShare.pic);
        });
    });

});