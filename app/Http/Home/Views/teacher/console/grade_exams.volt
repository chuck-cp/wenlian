{% extends 'templates/main.volt' %}

{% block content %}

    <div class="layout-main">
        <div class="my-sidebar">{{ partial('teacher/console/menu') }}</div>
        <div class="my-content">
            <div class="wrap">
                <div class="my-nav">
                    <span class="title">待阅试卷</span>
                </div>
                {% if pager.total_pages > 0 %}
                    <table class="layui-table" lay-skin="line">
                        <colgroup>
                            <col>
                            <col>
                            <col>
                            <col width="12%">
                        </colgroup>
                        <thead>
                        <tr>
                            <th>学员</th>
                            <th>试卷</th>
                            <th>时间</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        {% for item in pager.items %}
                            {% set grade_url = url({'for':'home.exam.mock_explore','id':item.exam_paper_user.id},{'manual_grade':1,'auth_code':item.auth_code}) %}
                            {% set paper_url = url({'for':'home.exam_paper.show','id':item.exam_paper.id}) %}
                            {% set user_url = url({'for':'home.user.show','id':item.user.id}) %}
                            <tr>
                                <td><a href="{{ user_url }}" target="_blank">{{ item.user.name }}</a></td>
                                <td><a href="{{ paper_url }}" target="_blank">{{ item.exam_paper.title }}</a></td>
                                <td>{{ item.exam_paper_user.create_time|time_ago }}</td>
                                <td><a class="layui-btn layui-btn-sm layui-btn-danger" href="{{ grade_url }}" target="_blank">阅卷</a></td>
                            </tr>
                        {% endfor %}
                        </tbody>
                    </table>
                    {{ partial('partials/pager') }}
                {% endif %}
            </div>
        </div>
    </div>

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