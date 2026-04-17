{% extends 'templates/main.volt' %}

{% block content %}

    <div class="layui-breadcrumb breadcrumb">
        <a href="/">首页</a>
        <a><cite>分销市场</cite></a>
    </div>

    <div id="dist-list" data-url="{{ url({'for':'home.distribution.pager'}) }}"></div>

{% endblock %}

{% block include_js %}

    {{ js_include('home/js/distribution.list.js') }}

{% endblock %}