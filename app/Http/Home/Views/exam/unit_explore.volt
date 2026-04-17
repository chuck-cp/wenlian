<!DOCTYPE html>
<html lang="zh-CN-Hans">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="renderer" content="webkit">
    <meta name="csrf-token" content="{{ csrfToken.getToken() }}">
    <title>{{ paper.title }} - 同步练习</title>
    {% if site_info.favicon %}
        {{ icon_link(site_info.favicon,false) }}
    {% else %}
        {{ icon_link('favicon.ico') }}
    {% endif %}
    {{ css_link('lib/layui/css/layui.css') }}
    {{ css_link('home/css/content.css') }}
    {{ css_link('home/css/common.css') }}
</head>
<body class="main watermark">

<div class="exam-header wrap">
    <div class="exam-nav">
        <div class="left">
            <span class="title">{{ paper.title }}</span>
        </div>
        <div class="right unit">
            <button class="layui-btn layui-btn-normal" id="btn-unit-fresh">未做题</button>
            <button class="layui-btn layui-btn-primary" id="btn-unit-history">已做题</button>
        </div>
    </div>
</div>

<div class="exam-main">
    <div class="exam-filter wrap">
        <div class="filter-group">
            <div class="title">题型</div>
            <div class="content">
                {% for value,name in model_types %}
                    <a class="model-filter" href="javascript:" data-model="{{ value }}">{{ name }}</a>
                {% endfor %}
            </div>
        </div>
    </div>
    <div class="exam-content">
        <div class="exam-question-box" id="exam-question-box"></div>
        <div class="loading" id="question-loading" style="display:none;">
            <i class="layui-icon layui-icon-loading layui-anim layui-anim-rotate layui-anim-loop"></i>
        </div>
    </div>
    <div class="exam-sidebar">
        <div class="exam-sort-box wrap" id="fresh-sort-box" style="display:none;"></div>
        <div class="exam-sort-box wrap" id="history-sort-box" style="display:none;"></div>
    </div>
</div>

<div class="layui-hide">
    <input type="hidden" name="paper_user.id" value="{{ paper_user.id }}">
    <input type="hidden" name="paper.id" value="{{ paper.id }}">
</div>

{{ partial('partials/js_vars') }}
{{ js_include('lib/layui/layui.js') }}
{{ js_include('home/js/common.js') }}
{{ js_include('home/js/exam.unit.explore.js') }}
{{ js_include('home/js/exam.question.report.js') }}

{% if site_info.copy_enabled == 0 %}
    {{ js_include('home/js/copy.block.js') }}
    {{ js_include('home/js/copy.watermark.js') }}
{% endif %}

</body>
</html>
