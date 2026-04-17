{% extends 'templates/main.volt' %}

{% block content %}

    {{ partial('macros/exam_paper_user') }}

    {% set search_url = url({'for':'admin.exam_paper.search_learning','id':paper.id}) %}
    {% set user_id = request.get('user_id','int',0) %}

    {% if user_id == 0 %}
        <div class="kg-nav">
            <div class="kg-nav-left">
                <span class="layui-breadcrumb">
                    <a class="kg-back"><i class="layui-icon layui-icon-return"></i>返回</a>
                    <a><cite>{{ paper.title }}</cite></a>
                    <a><cite>考试记录</cite></a>
                </span>
            </div>
            <div class="kg-nav-right">
                <a class="layui-btn layui-btn-sm" href="{{ search_url }}?target=export">
                    <i class="layui-icon layui-icon-export"></i>导出记录
                </a>
                <a class="layui-btn layui-btn-sm" href="{{ search_url }}">
                    <i class="layui-icon layui-icon-search"></i>搜索记录
                </a>
            </div>
        </div>
    {% endif %}

    {% if pager.total_pages > 0 %}
        <table class="layui-table kg-table">
            <colgroup>
                <col>
                <col>
                <col>
                <col>
                <col>
                <col>
                <col>
                <col>
            </colgroup>
            <thead>
            <tr>
                <th>用户信息</th>
                <th>首次考试</th>
                <th>试卷总分</th>
                <th>考试得分</th>
                <th>考试用时</th>
                <th>考试状态</th>
                <th>考试时间</th>
                <th>操作</th>
            </tr>
            </thead>
            {% for item in pager.items %}
                {% set debut_label = item.debut == 1 ? '是' : '否' %}
                {% set start_time = item.start_time > 0 ? date('Y-m-d H:i:s',item.start_time) : 'N/A' %}
                {% set user_duration = item.user_duration > 0 ? item.user_duration|duration : 'N/A' %}
                {% set mock_pilot_url = url({'for':'home.exam.mock_explore','id':item.id},{'auth_code':item.auth_code}) %}
                {% set mock_grade_url = url({'for':'home.exam.mock_explore','id':item.id},{'manual_grade':1,'auth_code':item.auth_code}) %}
                <tr>
                    <td>{{ item.user.name }}（{{ item.user.id }}）</td>
                    <td>{{ debut_label }}</td>
                    <td>{{ item.paper_score }}</td>
                    <td>{{ item.user_score }}</td>
                    <td>{{ user_duration }}</td>
                    <td>{{ exam_status(item.status) }}</td>
                    <td>{{ start_time }}</td>
                    <td>
                        {% if item.exam_paper.exam_type == 1 %}
                            {% if item.status == 3 and item.debut == 1 %}
                                <a class="layui-btn layui-btn-sm layui-bg-red" href="{{ mock_grade_url }}" target="_blank">阅卷</a>
                            {% else %}
                                <a class="layui-btn layui-btn-sm" href="{{ mock_pilot_url }}" target="_blank">查看</a>
                            {% endif %}
                        {% else %}
                            <a class="layui-btn layui-btn-sm layui-btn-disabled">查看</a>
                        {% endif %}
                    </td>
                </tr>
            {% endfor %}
        </table>
        {{ partial('partials/pager') }}
    {% endif %}

{% endblock %}

{% block inline_js %}

    <script>

        layui.use(['jquery'], function () {

            /**
             * 定时刷新页面，更新阅卷状态
             */
            window.setInterval(function () {
                window.location.reload();
            }, 15000);

        });

    </script>

{% endblock %}
