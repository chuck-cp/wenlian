{% extends 'templates/main.volt' %}

{% block content %}

    {{ partial('macros/user') }}

    {% set onlines_url = url({'for':'admin.user.onlines','id':user.id}) %}
    {% set study_articles_url = url({'for':'admin.user.study_articles','id':user.id}) %}
    {% set study_courses_url = url({'for':'admin.user.study_courses','id':user.id}) %}
    {% set study_papers_url = url({'for':'admin.user.study_exam_papers','id':user.id}) %}
    {% set teams_url = url({'for':'admin.user.teams','id':user.id}) %}
    {% set orders_url = url({'for':'admin.user.orders','id':user.id}) %}
    {% set cash_history_url = url({'for':'admin.user.cash_history','id':user.id}) %}

    <fieldset class="layui-elem-field layui-field-title">
        <legend>用户详情</legend>
    </fieldset>

    <div class="layui-tabs">
        <ul class="layui-tabs-header">
            <li class="layui-this">基本信息</li>
            <li>在学课程</li>
            <li>在学专栏</li>
            <li>在学试卷</li>
            <!-- <li>分销团队</li>
            <li>订单明细</li>
            <li>收支明细</li> -->
            <li>在线记录</li>
        </ul>
        <div class="layui-tabs-body">
            <div class="layui-tabs-item layui-show">
                {{ partial('user/show_basic') }}
            </div>
            <div class="layui-tabs-item">
                <div id="study-course-list" data-url="{{ study_courses_url }}"></div>
            </div>
            <div class="layui-tabs-item">
                <div id="study-article-list" data-url="{{ study_articles_url }}"></div>
            </div>
            <div class="layui-tabs-item">
                <div id="study-paper-list" data-url="{{ study_papers_url }}"></div>
            </div>
            <!-- <div class="layui-tabs-item">
                <div id="team-list" data-url="{{ teams_url }}"></div>
            </div>
            <div class="layui-tabs-item">
                <div id="order-list" data-url="{{ orders_url }}"></div>
            </div>
            <div class="layui-tabs-item">
                <div id="cash-history" data-url="{{ cash_history_url }}"></div>
            </div> -->
            <div class="layui-tabs-item">
                <div id="online-list" data-url="{{ onlines_url }}"></div>
            </div>
        </div>
    </div>

{% endblock %}

{% block inline_js %}

    <script>

        layui.use(['jquery', 'helper'], function () {

            var $ = layui.jquery;
            var helper = layui.helper;

            var $articleList = $('#study-article-list');
            helper.ajaxLoadHtml($articleList.data('url'), $articleList.attr('id'));

            var $courseList = $('#study-course-list');
            helper.ajaxLoadHtml($courseList.data('url'), $courseList.attr('id'));

            var $paperList = $('#study-paper-list');
            helper.ajaxLoadHtml($paperList.data('url'), $paperList.attr('id'));

            var $orderList = $('#order-list');
            helper.ajaxLoadHtml($orderList.data('url'), $orderList.attr('id'));

            var $teamList = $('#team-list');
            helper.ajaxLoadHtml($teamList.data('url'), $teamList.attr('id'));

            var $cashHistory = $('#cash-history');
            helper.ajaxLoadHtml($cashHistory.data('url'), $cashHistory.attr('id'));

            var $onlineList = $('#online-list');
            helper.ajaxLoadHtml($onlineList.data('url'), $onlineList.attr('id'));

        });

    </script>

{% endblock %}
