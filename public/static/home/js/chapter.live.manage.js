layui.use(['jquery', 'layer', 'helper'], function () {

    var $ = layui.jquery;
    var layer = layui.layer;
    var helper = layui.helper;

    var chapterId = $('input[name=chapter_id]').val();

    var $onlineUserList = $('#online-user-list');
    var $blockedUserList = $('#blocked-user-list');

    helper.ajaxLoadHtml($onlineUserList.data('url'), $onlineUserList.attr('id'));
    helper.ajaxLoadHtml($blockedUserList.data('url'), $blockedUserList.attr('id'));

    $('.icon-set').on('click', function () {
        var frameUrl = $(this).data('url');
        layer.open({
            type: 2,
            title: '直播间管理',
            area: ['900px', '540px'],
            content: [frameUrl, 'no'],
        });
    });

    $('body').on('click', '.btn-block', function () {
        var $btn = $(this);
        var url = '/live/' + chapterId + '/user/block';
        var userId = $btn.data('user-id');
        $.ajax({
            type: 'POST',
            url: url,
            data: {user_id: userId, expiry: 1},
            success: function () {
                layer.msg('用户禁言成功', {icon: 1});
                $btn.removeClass('btn-block').addClass('layui-btn-disabled');
            }
        });
    });

    $('body').on('click', '.btn-unblock', function () {
        var $btn = $(this);
        var url = '/live/' + chapterId + '/user/unblock';
        var userId = $btn.data('user-id');
        $.ajax({
            type: 'POST',
            url: url,
            data: {user_id: userId},
            success: function () {
                layer.msg('解除禁言成功', {icon: 1});
                $btn.removeClass('btn-unblock').addClass('layui-btn-disabled');
            }
        });
    });

});
