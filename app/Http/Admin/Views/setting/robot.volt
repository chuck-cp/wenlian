{% extends 'templates/main.volt' %}

{% block content %}

    <div class="layui-tabs">
        <ul class="layui-tabs-header">
            <li class="layui-this">企业微信</li>
            <li>阿里钉钉</li>
        </ul>
        <div class="layui-tabs-body">
            <div class="layui-tabs-item layui-show">
                {{ partial('setting/robot_wework') }}
            </div>
            <div class="layui-tabs-item">
                {{ partial('setting/robot_dingtalk') }}
            </div>
        </div>
    </div>

{% endblock %}
