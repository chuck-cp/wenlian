{% extends 'templates/main.volt' %}

{% block content %}

    {% set closed_tips_display = mobile.status == 'closed' ? 'display:block' : 'display:none' %}
    {% set index_module = mobile.index_module|json_decode(true) %}

    <div class="layui-tabs">
        <ul class="layui-tabs-header">
            <li class="layui-this">基本设置</li>
            <li>首页设置</li>
        </ul>
        <div class="layui-tabs-body">
            <div class="layui-tabs-item layui-show">
                {{ partial('setting/mobile_basic') }}
            </div>
            <div class="layui-tabs-item">
                {{ partial('setting/mobile_index') }}
            </div>
        </div>
    </div>

{% endblock %}

{% block inline_js %}

    <script>

        layui.use(['jquery', 'form'], function () {

            var $ = layui.jquery;
            var form = layui.form;

            form.on('radio(status)', function (data) {
                var block = $('#closed-tips-block');
                if (data.value === 'closed') {
                    block.show();
                } else {
                    block.hide();
                }
            });

        });

    </script>

{% endblock %}
