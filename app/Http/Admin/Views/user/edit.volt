{% extends 'templates/main.volt' %}

{% block content %}

    {% set profile_display = user.edu_role == 2 ? 'display:block': 'display:none' %}
    {% set edu_role_label_display = user.edu_role == 3 ? 'display:block': 'display:none' %}
    {% set lock_expiry_display = user.locked == 1 ? 'display:block': 'display:none' %}
    {% set vip_expiry_display = user.vip == 1 ? 'display:block': 'display:none' %}
    {% set update_user_url = url({'for':'admin.user.update','id':user.id}) %}
    {% set update_profile_url = url({'for':'admin.user.profile','id':user.id}) %}

    <fieldset class="layui-elem-field layui-field-title">
        <legend>编辑用户</legend>
    </fieldset>

    <div class="layui-tabs">
        <ul class="layui-tabs-header">
            <li class="layui-this">基本信息</li>
            <li>账号信息</li>
        </ul>
        <div class="layui-tabs-body">
            <div class="layui-tabs-item layui-show">
                {{ partial('user/edit_basic') }}
            </div>
            <div class="layui-tabs-item">
                {{ partial('user/edit_account') }}
            </div>
        </div>
    </div>

{% endblock %}

{% block include_js %}

    {{ js_include('admin/js/avatar.upload.js') }}

{% endblock %}

{% block inline_js %}

    <script>

        layui.use(['jquery', 'form', 'layer', 'laydate'], function () {

            var $ = layui.jquery;
            var form = layui.form;
            var layer = layui.layer;
            var laydate = layui.laydate;

            $('#btn-profile').on('click', function () {
                var url = $(this).data('url');
                layer.open({
                    type: 2,
                    title: '个人资料',
                    area: ['800px', '450px'],
                    content: url,
                });
            });

            laydate.render({
                elem: 'input[name=vip_expiry_time]',
                type: 'datetime'
            });

            laydate.render({
                elem: 'input[name=lock_expiry_time]',
                type: 'datetime'
            });

            form.on('radio(edu_role)', function (data) {
                var profileBlock = $('#profile-block');
                var labelBlock = $('#edu-role-label-block');
                if (data.value === '2') {
                    profileBlock.show();
                } else {
                    profileBlock.hide();
                }
                if (data.value === '3') {
                    labelBlock.show();
                } else {
                    labelBlock.hide();
                }
            });

            form.on('radio(vip)', function (data) {
                var block = $('#vip-expiry-block');
                if (data.value === '1') {
                    block.show();
                } else {
                    block.hide();
                }
            });

            form.on('radio(locked)', function (data) {
                var block = $('#lock-expiry-block');
                if (data.value === '1') {
                    block.show();
                } else {
                    block.hide();
                }
            });

        });

    </script>

{% endblock %}
