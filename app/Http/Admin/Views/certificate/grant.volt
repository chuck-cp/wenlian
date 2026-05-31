{% extends 'templates/main.volt' %}

{% block content %}

    {% set grant_url = url({'for':'admin.cert.grant','id':cert.id}) %}
    {% set user_select_url = url({'for':'admin.user.search'},{'target':'select'}) %}

    <form class="layui-form kg-form" method="POST" action="{{ grant_url }}">
        <fieldset class="layui-elem-field layui-field-title">
            <legend>手动发放证书</legend>
        </fieldset>
        <div class="layui-form-item">
            <label class="layui-form-label">当前证书</label>
            <div class="layui-input-block">
                <div class="layui-form-mid">{{ cert.name }}（{{ cert.id }}）</div>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">选择用户</label>
            <div class="layui-input-block">
                <div class="layui-input-inline" style="width:60%;">
                    <input class="layui-input" type="text" name="selected_user" placeholder="请选择用户" readonly lay-verify="required">
                </div>
                <div class="layui-input-inline">
                    <button class="layui-btn layui-btn-primary" type="button" id="select-user" data-url="{{ user_select_url }}">选择用户</button>
                </div>
                <input type="hidden" name="user_id">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label"></label>
            <div class="layui-input-block">
                <div class="layui-word-aux">提交后会将当前证书授予所选用户。</div>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label"></label>
            <div class="layui-input-block">
                <button class="layui-btn" lay-submit="true" lay-filter="go">提交</button>
                <button type="button" class="kg-back layui-btn layui-btn-primary">返回</button>
            </div>
        </div>
    </form>

{% endblock %}

{% block inline_js %}

    <script>

        layui.use(['jquery', 'layer'], function () {

            var $ = layui.jquery;
            var layer = layui.layer;

            $('#select-user').on('click', function () {
                layer.open({
                    type: 2,
                    title: '选择用户',
                    resize: false,
                    area: ['88%', '88%'],
                    content: [$(this).data('url')]
                });
            });

            window.selectCertGrantUser = function (user) {
                $('input[name=user_id]').val(user.id);
                $('input[name=selected_user]').val(user.name + '（' + user.id + '）');
            };

        });

    </script>

{% endblock %}
