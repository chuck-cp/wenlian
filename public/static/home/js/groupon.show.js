layui.use(['jquery', 'helper'], function () {

    var $ = layui.jquery;
    var helper = layui.helper;

    $('.btn-direct-order').on('click', function () {
        var location = $(this).data('url');
        helper.checkLogin(function () {
            window.location.href = location;
        });
    });

    $('.btn-leader-order').on('click', function () {
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

    $('body').on('click', '.btn-team-order', function () {
        var url = $(this).data('url');
        var teamId = $(this).data('id');
        helper.checkLogin(function () {
            $.ajax({
                type: 'POST',
                url: url,
                data: {team_id: teamId},
                success: function (res) {
                    window.location.href = res.location;
                }
            });
        });
    });

    var $grouponList = $('#groupon-list');
    var $teamList = $('#team-list');

    if ($grouponList.length > 0) {
        helper.ajaxLoadHtml($grouponList.data('url'), $grouponList.attr('id'));
    }

    if ($teamList.length > 0) {
        helper.ajaxLoadHtml($teamList.data('url'), $teamList.attr('id'));
    }

});