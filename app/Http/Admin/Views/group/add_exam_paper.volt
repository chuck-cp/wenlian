{% extends 'templates/main.volt' %}

{% block content %}

    {% set create_url = url({'for':'admin.group.create_exam_paper','id':group.id}) %}

    <fieldset class="layui-elem-field layui-field-title">
        <legend>添加试卷</legend>
    </fieldset>

    <form class="layui-form kg-form" method="POST" action="{{ create_url }}">
        <div class="layui-form-item">
            <label class="layui-form-label">分组名称</label>
            <div class="layui-input-block">
                <div class="layui-form-mid">{{ group.name }}</div>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">试卷选择</label>
            <div class="layui-input-block">
                <div id="xm-paper-ids"></div>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label"></label>
            <div class="layui-input-block">
                <button class="layui-btn" lay-submit="true" lay-filter="go">提交</button>
                <button type="button" class="kg-back layui-btn layui-btn-primary">返回</button>
                <input type="hidden" name="group_id" value="{{ group.id }}">
            </div>
        </div>
    </form>

{% endblock %}

{% block include_js %}

    {{ js_include('lib/xm-select.js') }}

{% endblock %}

{% block inline_js %}

    <script>

        layui.use(['jquery'], function () {

            xmSelect.render({
                el: '#xm-paper-ids',
                name: 'xm_paper_ids',
                layVerify: 'required',
                autoRow: true,
                filterable: true,
                filterMethod: function (val, item) {
                    return item.name.toLowerCase().indexOf(val.toLowerCase()) !== -1;
                },
                data: {{ xm_exam_papers|json_encode }}
            });

        });

    </script>

{% endblock %}
