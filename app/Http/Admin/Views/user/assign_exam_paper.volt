{% extends 'templates/main.volt' %}

{% block content %}

    {% set action_url = url({'for':'admin.user.assign_exam_paper','id':user.id}) %}

    <form class="layui-form kg-form" method="POST" action="{{ action_url }}">
        <fieldset class="layui-elem-field layui-field-title">
            <legend>赠送试卷</legend>
        </fieldset>
        <div class="layui-form-item">
            <label class="layui-form-label">赠送对象</label>
            <div class="layui-input-block">
                <div class="layui-form-mid">{{ user.name }}（{{ user.id }}）</div>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">选择试卷</label>
            <div class="layui-input-block">
                <div id="xm-paper-ids"></div>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">过期时间</label>
            <div class="layui-input-block">
                <input class="layui-input" type="text" name="expiry_time" autocomplete="off" lay-verify="required">
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

{% block include_js %}

    {{ js_include('lib/xm-select.js') }}

{% endblock %}

{% block inline_js %}

    <script>

        layui.use(['laydate'], function () {

            var laydate = layui.laydate;

            xmSelect.render({
                el: '#xm-paper-ids',
                name: 'xm_paper_ids',
                layVerify: 'required',
                filterable: true,
                filterMethod: function (val, item) {
                    return item.name.toLowerCase().indexOf(val.toLowerCase()) !== -1;
                },
                data: {{ xm_exam_papers|json_encode }}
            });

            laydate.render({
                elem: 'input[name=expiry_time]',
                type: 'datetime'
            });

        });

    </script>

{% endblock %}
