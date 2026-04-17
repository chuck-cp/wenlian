{% extends 'templates/main.volt' %}

{% block content %}

    <form class="layui-form kg-form" method="GET" action="{{ url({'for':'admin.cert.users','id':cert.id}) }}">
        <fieldset class="layui-elem-field layui-field-title">
            <legend>搜索用户</legend>
        </fieldset>
        <div class="layui-form-item">
            <label class="layui-form-label">证书编号</label>
            <div class="layui-input-block">
                <input class="layui-input" type="text" name="sn" placeholder="证书编号精确匹配">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">用户账号</label>
            <div class="layui-input-block">
                <input class="layui-input" type="text" name="user_id" placeholder="用户编号 / 手机号码 / 邮箱地址">
            </div>
        </div>
        <div class="layui-form-item" id="time-range">
            <label class="layui-form-label">授予时间</label>
            <div class="layui-input-inline">
                <input class="layui-input" id="start-time" type="text" name="create_time[]" autocomplete="off">
            </div>
            <div class="layui-form-mid">-</div>
            <div class="layui-input-inline">
                <input class="layui-input" id="end-time" type="text" name="create_time[]" autocomplete="off">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label"></label>
            <div class="layui-input-block">
                <button class="layui-btn" lay-submit="true">提交</button>
                <button type="button" class="kg-back layui-btn layui-btn-primary">返回</button>
            </div>
        </div>
    </form>

{% endblock %}

{% block inline_js %}

    <script>

        layui.use(['laydate'], function () {

            var laydate = layui.laydate;

            laydate.render({
                elem: '#time-range',
                type: 'datetime',
                range: ['#start-time', '#end-time'],
            });

        });

    </script>

{% endblock %}