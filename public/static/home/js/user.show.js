layui.use(['jquery', 'helper'], function () {

    var $ = layui.jquery;
    var helper = layui.helper;

    if ($('#tab-study-courses').length > 0) {
        var $tabStudyCourses = $('#tab-study-courses');
        helper.ajaxLoadHtml($tabStudyCourses.data('url'), $tabStudyCourses.attr('id'));
    }

    if ($('#tab-study-articles').length > 0) {
        var $tabStudyArticles = $('#tab-study-articles');
        helper.ajaxLoadHtml($tabStudyArticles.data('url'), $tabStudyArticles.attr('id'));
    }

    if ($('#tab-study-papers').length > 0) {
        var $tabStudyPapers = $('#tab-study-papers');
        helper.ajaxLoadHtml($tabStudyPapers.data('url'), $tabStudyPapers.attr('id'));
    }

    if ($('#tab-questions').length > 0) {
        var $tabQuestions = $('#tab-questions');
        helper.ajaxLoadHtml($tabQuestions.data('url'), $tabQuestions.attr('id'));
    }

    if ($('#tab-answers').length > 0) {
        var $tabAnswers = $('#tab-answers');
        helper.ajaxLoadHtml($tabAnswers.data('url'), $tabAnswers.attr('id'));
    }

});