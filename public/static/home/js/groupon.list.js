layui.use(['jquery', 'helper'], function () {

    var $ = layui.jquery;
    var helper = layui.helper;

    var $grouponList = $('#groupon-list');

    if ($grouponList.length > 0) {
        helper.ajaxLoadHtml($grouponList.data('url'), $grouponList.attr('id'));
    }

});