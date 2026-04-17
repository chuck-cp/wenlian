{% extends 'templates/main.volt' %}

{% block content %}

    <form class="layui-form kg-form" method="POST" action="{{ url({'for':'admin.exam_paper.create'}) }}">
        <fieldset class="layui-elem-field layui-field-title">
            <legend>添加试题</legend>
        </fieldset>
        <div class="layui-form-item">
            <label class="layui-form-label">试卷标题</label>
            <div class="layui-input-block">
                <input class="layui-input" type="text" name="title" lay-verify="required">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">测评方式</label>
            <div class="layui-input-block">
                <input type="radio" name="exam_type" value="1" title="模拟考试" checked="checked" lay-filter="exam_type">
                <input type="radio" name="exam_type" value="2" title="同步练习" lay-filter="exam_type">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label"></label>
            <div class="layui-input-block">
                <div class="layui-form-mid layui-word-aux" id="exam-type-tips"></div>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">组卷方式</label>
            <div class="layui-input-block">
                <input type="radio" name="pack_type" value="1" title="人工组卷" checked="checked" lay-filter="pack_type">
                <input type="radio" name="pack_type" value="2" title="随机组卷" lay-filter="pack_type">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label"></label>
            <div class="layui-input-block">
                <div class="layui-form-mid layui-word-aux" id="pack-type-tips"></div>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label"></label>
            <div class="layui-input-block">
                <button class="layui-btn" lay-submit="true" lay-filter="go">提交</button>
                <button type="button" class="kg-back layui-btn layui-btn-primary">返回</button>
            </div>
        </div>
    </form>

{% endblock %}

{% block inline_js %}

    <script>

        layui.use(['jquery', 'form'], function () {

            var $ = layui.jquery;
            var form = layui.form;

            var examTypeTips = {
                '1': '模拟考试有时间限制，交卷后才能看到答案',
                '2': '同步练习无时间限制，做答后就能看到答案',
            };

            var packTypeTips = {
                '1': '由人工挑选固定试题，内容针对性强，适合作为模拟考试使用',
                '2': '由系统随机挑选试题，内容覆盖面广，适合作为知识练习使用',
            };

            var examTypeTipsBlock = $('#exam-type-tips');
            var packTypeTipsBlock = $('#pack-type-tips');

            form.on('radio(exam_type)', function (data) {
                examTypeTipsBlock.html(examTypeTips[data.value]);
            });

            form.on('radio(pack_type)', function (data) {
                packTypeTipsBlock.html(packTypeTips[data.value]);
            });

            examTypeTipsBlock.html(examTypeTips['1']);
            packTypeTipsBlock.html(packTypeTips['1']);

        });

    </script>

{% endblock %}
