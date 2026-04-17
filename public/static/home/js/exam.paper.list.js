layui.use(['jquery', 'helper'], function () {

    var $ = layui.jquery;
    var helper = layui.helper;

    var $paperList = $('#paper-list');

    helper.ajaxLoadHtml($paperList.data('url'), $paperList.attr('id'));

});