layui.use(['jquery', 'helper'], function () {

    var $ = layui.jquery;
    var helper = layui.helper;

    var $articleList = $('#article-list');

    if ($articleList.length > 0) {
        helper.ajaxLoadHtml($articleList.data('url'), $articleList.attr('id'));
    }

});