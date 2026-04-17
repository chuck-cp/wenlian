<!DOCTYPE html>
<html lang="zh-CN-Hans">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="renderer" content="webkit">
    <meta name="csrf-token" content="{{ csrfToken.getToken() }}">
    <title>收藏练习</title>
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
            <span class="title">收藏练习</span>
        </div>
        <div class="right">
            收藏数量：<span class="count" id="total-count">0</span>
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
        <div class="exam-sort-box wrap" id="exam-sort-box"></div>
    </div>
</div>

{{ partial('partials/js_vars') }}
{{ js_include('lib/layui/layui.js') }}
{{ js_include('home/js/common.js') }}
{{ js_include('home/js/exam.favorite.explore.js') }}
{{ js_include('home/js/exam.question.report.js') }}

{% if site_info.copy_enabled == 0 %}
    {{ js_include('home/js/copy.block.js') }}
    {{ js_include('home/js/copy.watermark.js') }}
{% endif %}

</body>
</html>