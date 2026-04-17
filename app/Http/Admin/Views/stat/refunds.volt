{% extends 'templates/main.volt' %}

{% block content %}

    {% set year = request.get('year','int',date('Y')) %}
    {% set month = request.get('month','int',date('m')) %}

    <div class="kg-nav">
        <div class="kg-nav-left">
            <span class="layui-breadcrumb">
                <a><cite>售后退款统计</cite></a>
            </span>
        </div>
    </div>

    <form class="layui-form kg-search-form" method="GET" action="{{ url({'for':'admin.stat.refunds'}) }}">
        <div class="layui-form-item">
            <label class="layui-form-label">选择年份</label>
            <div class="layui-input-inline">
                <select name="year">
                    {% for value in years %}
                        <option value="{{ value }}" {% if value == year %}selected{% endif %}>{{ value }}年</option>
                    {% endfor %}
                </select>
            </div>
            <label class="layui-form-label">选择月份</label>
            <div class="layui-input-inline">
                <select name="month">
                    {% for value in months %}
                        <option value="{{ value }}" {% if value == month %}selected{% endif %}>{{ value }}月</option>
                    {% endfor %}
                </select>
            </div>
            <div class="layui-input-inline">
                <button class="layui-btn" lay-submit="true" lay-filter="query">查询</button>
            </div>
        </div>
    </form>

    <div class="kg-chart" id="chart"></div>

    <div class="layui-hide">
        <textarea name="data">{{ data|json_encode }}</textarea>
        <input type="text" name="x_axis_name" value="日期">
        <input type="text" name="y_axis_name" value="金额（元）">
    </div>

{% endblock %}

{% block include_js %}

    {{ js_include('lib/echarts/echarts.min.js') }}
    {{ js_include('admin/js/stat.query.js') }}
    {{ js_include('admin/js/stat.chart.js') }}

{% endblock %}
