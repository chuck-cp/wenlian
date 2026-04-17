{% extends 'templates/layer.volt' %}

{% block content %}

    {% set month = request.get('month','string',date('Y-m')) %}

    <form class="layui-form query-form" method="get" action="{{ url({'for':'home.uc.affiliate'}) }}">
        <div class="layui-form-item">
            <label class="layui-form-label">总计金额</label>
            <div class="layui-form-mid">{{ '￥%0.2f'|format(sale_trend.total_amount) }}</div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">所属月份</label>
            <div class="layui-input-inline">
                <input class="layui-input" type="text" name="month" value="{{ month }}">
            </div>
            <div class="layui-input-inline">
                <button class="layui-btn" lay-submit="true">查询</button>
                <input type="hidden" name="action" value="sale_trend">
            </div>
        </div>
    </form>

    <div class="chart" id="chart"></div>

{% endblock %}

{% block inline_css %}
    <style>
        .query-form {
            margin-top: 10px;
            margin-bottom: 20px;
        }

        .chart {
            height: 320px;
        }
    </style>
{% endblock %}

{% block include_js %}

    {{ js_include('lib/echarts/echarts.min.js') }}

{% endblock %}

{% block inline_js %}

    <script>

        var myChart = echarts.init(document.getElementById('chart'));

        var option = {
            legend: {},
            tooltip: {},
            dataset: {
                dimensions: ['day', 'amount'],
                source: {{ sale_trend.items|json_encode }}
            },
            xAxis: {type: 'category'},
            yAxis: {},
            series: [
                {type: 'line'},
            ]
        };

        myChart.setOption(option);

        layui.use(['laydate'], function () {
            var laydate = layui.laydate;
            laydate.render({
                elem: 'input[name=month]',
                type: 'month'
            });
        });

    </script>

{% endblock %}
