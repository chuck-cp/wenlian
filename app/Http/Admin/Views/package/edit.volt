{% extends 'templates/main.volt' %}

{% block content %}

    {% set action_url = url({'for':'admin.package.update','id':package.id}) %}

    <fieldset class="layui-elem-field layui-field-title">
        <legend>编辑套餐</legend>
    </fieldset>

    <div class="layui-tabs">
        <ul class="layui-tabs-header">
            <li class="layui-this">基本信息</li>
            <li>营销设置</li>
            <li>相关课程</li>
        </ul>
        <div class="layui-tabs-body">
            <div class="layui-tabs-item layui-show">
                {{ partial('package/edit_basic') }}
            </div>
            <div class="layui-tabs-item">
                {{ partial('package/edit_sale') }}
            </div>
            <div class="layui-tabs-item">
                {{ partial('package/edit_course') }}
            </div>
        </div>
    </div>

{% endblock %}

{% block include_js %}

    {{ js_include('lib/xm-select.js') }}
    {{ js_include('admin/js/cover.upload.js') }}

{% endblock %}

{% block inline_js %}

    <script>

        layui.use(['jquery', 'layer'], function () {

            xmSelect.render({
                el: '#xm-course-ids',
                name: 'xm_course_ids',
                max: 15,
                autoRow: true,
                filterable: true,
                filterMethod: function (val, item) {
                    return item.name.toLowerCase().indexOf(val.toLowerCase()) !== -1;
                },
                data: {{ xm_courses|json_encode }}
            });

        });

    </script>

{% endblock %}
