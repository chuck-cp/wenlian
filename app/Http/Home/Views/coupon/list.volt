{% extends 'templates/main.volt' %}

{% block content %}

    <div class="layui-breadcrumb breadcrumb">
        <a href="/">首页</a>
        <a><cite>领券中心</cite></a>
    </div>

    <div id="coupon-list" data-url="{{ url({'for':'home.coupon.pager'}) }}"></div>

{% endblock %}

{% block include_js %}

    {{ js_include('home/js/coupon.list.js') }}

{% endblock %}