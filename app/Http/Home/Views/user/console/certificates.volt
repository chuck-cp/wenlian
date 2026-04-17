{% extends 'templates/main.volt' %}

{% block content %}

    <div class="layout-main">
        <div class="my-sidebar">{{ partial('user/console/menu') }}</div>
        <div class="my-content">
            <div class="wrap">
                <div class="my-nav">
                    <span class="title">我的证书</span>
                </div>
                {% if pager.total_pages > 0 %}
                    <table class="layui-table" lay-skin="line" lay-size="lg">
                        <colgroup>
                            <col>
                            <col>
                            <col>
                            <col width="15%">
                        </colgroup>
                        <thead>
                        <tr>
                            <th>名称</th>
                            <th>编号</th>
                            <th>时间</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        {% for item in pager.items %}
                            {% set cert_item_info = item.cert.item_info %}
                            {% set cert_item_type = item.cert.item_type %}
                            {% set cert_url = url({'for':'home.cert','sn':item.sn}) %}
                            <tr>
                                <td>
                                    {% if cert_item_type == 1 %}
                                        {% set course = cert_item_info.course %}
                                        {% set url = url({'for':'home.course.show','id':course.id}) %}
                                        <p><a href="{{ url }}" title="课程：{{ course.title }}" target="_blank">{{ item.cert.name }}</a></p>
                                    {% elseif cert_item_type == 4 %}
                                        {% set exam_paper = cert_item_info.exam_paper %}
                                        {% set url = url({'for':'home.exam_paper.show','id':exam_paper.id}) %}
                                        <p><a href="{{ url }}" title="考试：{{ exam_paper.title }}" target="_blank">{{ item.cert.name }}</a></p>
                                    {% elseif cert_item_type == 6 %}
                                        {% set topic = cert_item_info.topic %}
                                        {% set url = url({'for':'home.topic.show','id':topic.id}) %}
                                        <p><a href="{{ url }}" title="专题：{{ topic.title }}" target="_blank">{{ item.cert.name }}</a></p>
                                    {% endif %}
                                </td>
                                <td>{{ item.sn }}</td>
                                <td>{{ item.create_time|time_ago }}</td>
                                <td class="center">
                                    <a href="{{ cert_url }}" class="layui-btn layui-btn-sm">详情</a>
                                </td>
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