{% extends 'templates/main.volt' %}

{% block content %}

    <div class="layui-breadcrumb breadcrumb">
        <a href="/">首页</a>
        <a><cite>拼团</cite></a>
    </div>

    <div id="groupon-list" data-url="{{ url({'for':'home.groupon.pager'}) }}"></div>

{% endblock %}

{% block include_js %}

    {{ js_include('home/js/groupon.list.js') }}

{% endblock %}