layui.use(['jquery', 'helper'], function () {

    var $ = layui.jquery;
    var helper = layui.helper;

    /**
     * 收藏
     */
    $('.icon-star').on('click', function () {
        var $this = $(this);
        var $parent = $this.parent();
        var $favoriteCount = $parent.next();
        var favoriteCount = $favoriteCount.data('count');
        helper.checkLogin(function () {
            $.ajax({
                type: 'POST',
                url: $parent.data('url'),
                success: function () {
                    if ($this.hasClass('layui-icon-star-fill')) {
                        $this.removeClass('layui-icon-star-fill');
                        $this.addClass('layui-icon-star');
                        $parent.attr('title', '收藏考试');
                        favoriteCount--;
                    } else {
                        $this.removeClass('layui-icon-star');
                        $this.addClass('layui-icon-star-fill');
                        $parent.attr('title', '取消收藏');
                        favoriteCount++;
                    }
                    $favoriteCount.data('count', favoriteCount).text(favoriteCount);
                }
            });
        });
    });

    /**
     * 购买
     */
    $('.btn-buy').on('click', function () {
        var url = $(this).data('url');
        helper.checkLogin(function () {
            window.location.href = url;
        });
    });

    /**
     * 参与
     */
    $('.btn-join').on('click', function () {
        var $btn = $(this);
        helper.checkLogin(function () {
            $btn.attr('disabled', 'disabled').addClass('layui-btn-disabled');
            $.ajax({
                method: 'POST',
                url: $btn.data('url'),
                success: function (res) {
                    window.location.href = res.location;
                },
                error: function () {
                    $btn.removeAttr('disabled').removeClass('layui-btn-disabled');
                }
            });
        });
    });

    var $historyList = $('#history-list');

    if ($historyList.length > 0) {
        helper.ajaxLoadHtml($historyList.data('url'), $historyList.attr('id'));
    }

    var $latestMockUsers = $('#latest-mock-users');

    if ($latestMockUsers.length > 0) {
        helper.ajaxLoadHtml($latestMockUsers.data('url'), $latestMockUsers.attr('id'));
    }

    var $topMockUsers = $('#top-mock-users');

    if ($topMockUsers.length > 0) {
        helper.ajaxLoadHtml($topMockUsers.data('url'), $topMockUsers.attr('id'));
    }

    var $latestUnitUsers = $('#latest-unit-users');

    if ($latestUnitUsers.length > 0) {
        helper.ajaxLoadHtml($latestUnitUsers.data('url'), $latestUnitUsers.attr('id'));
    }

});