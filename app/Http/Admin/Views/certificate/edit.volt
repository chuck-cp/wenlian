{% extends 'templates/main.volt' %}

{% block content %}

    {% set block1_style = cert.item_type == 1 ? 'display:block;' : 'display:none;' %}
    {% set block4_style = cert.item_type == 4 ? 'display:block;' : 'display:none;' %}
    {% set block6_style = cert.item_type == 6 ? 'display:block;' : 'display:none;' %}

    <form class="layui-form kg-form" method="POST" action="{{ url({'for':'admin.cert.update','id':cert.id}) }}">
        <fieldset class="layui-elem-field layui-field-title">
            <legend>编辑证书</legend>
        </fieldset>
        <div class="layui-form-item">
            <label class="layui-form-label">证书名称</label>
            <div class="layui-input-block">
                <input class="layui-input" type="text" name="name" value="{{ cert.name }}" lay-verify="required">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">证书类型</label>
            <div class="layui-input-block">
                {% for value,title in item_types %}
                    {% set checked = value == cert.item_type ? 'checked="checked"' : '' %}
                    <input type="radio" name="item_type" value="{{ value }}" title="{{ title }}" {{ checked }} lay-filter="item_type">
                {% endfor %}
            </div>
        </div>
        <div id="block-1" class="block" style="{{ block1_style }}">
            <div class="layui-form-item">
                <label class="layui-form-label">课程选择</label>
                <div class="layui-input-block">
                    <div id="xm-course-id"></div>
                </div>
            </div>
        </div>
        <div id="block-4" class="block" style="{{ block4_style }}">
            <div class="layui-form-item">
                <label class="layui-form-label">考试选择</label>
                <div class="layui-input-block">
                    <div id="xm-paper-id"></div>
                </div>
            </div>
        </div>
        <div id="block-6" class="block" style="{{ block6_style }}">
            <div class="layui-form-item">
                <label class="layui-form-label">专题选择</label>
                <div class="layui-input-block">
                    <div id="xm-topic-id"></div>
                </div>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">发布</label>
            <div class="layui-input-block">
                <input type="radio" name="published" value="1" title="是" {% if cert.published == 1 %}checked="checked"{% endif %}>
                <input type="radio" name="published" value="0" title="否" {% if cert.published == 0 %}checked="checked"{% endif %}>
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

        layui.use(['jquery', 'form'], function () {

            var $ = layui.jquery;
            var form = layui.form;

            xmSelect.render({
                el: '#xm-course-id',
                name: 'xm_course_id',
                radio: true,
                filterable: true,
                filterMethod: function (val, item) {
                    return item.name.toLowerCase().indexOf(val.toLowerCase()) !== -1;
                },
                data: {{ xm_courses|json_encode }}
            });

            xmSelect.render({
                el: '#xm-paper-id',
                name: 'xm_paper_id',
                radio: true,
                filterable: true,
                filterMethod: function (val, item) {
                    return item.name.toLowerCase().indexOf(val.toLowerCase()) !== -1;
                },
                data: {{ xm_exam_papers|json_encode }}
            });

            xmSelect.render({
                el: '#xm-topic-id',
                name: 'xm_topic_id',
                radio: true,
                filterable: true,
                filterMethod: function (val, item) {
                    return item.name.toLowerCase().indexOf(val.toLowerCase()) !== -1;
                },
                data: {{ xm_topics|json_encode }}
            });

            form.on('radio(item_type)', function (data) {
                $('.block').hide();
                $('#block-' + data.value).show();
            });

        });

    </script>

{% endblock %}
