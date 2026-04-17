{% extends 'templates/main.volt' %}

{% block content %}

    {% set block1_style = cert.item_type == 1 ? 'display:block;' : 'display:none;' %}
    {% set block4_style = cert.item_type == 4 ? 'display:block;' : 'display:none;' %}
    {% set block6_style = cert.item_type == 6 ? 'display:block;' : 'display:none;' %}

    {% set grant_url = url({'for':'admin.cert.grant','id':cert.id}) %}
    {% set user_select_url = url({'for':'admin.user.search'},{'target':'select'}) %}

    <form class="layui-form kg-form" method="POST" action="{{ grant_url }}">
        <fieldset class="layui-elem-field layui-field-title">
            <legend>发放证书</legend>
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
            <label class="layui-form-label"></label>
            <div class="layui-input-block">
                <div class="layui-word-aux">默认带入当前证书关联内容，也可以切换专题 / 课程 / 考试重新选择后发放对应证书。</div>
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

        layui.use(['jquery', 'form', 'layer'], function () {

            var $ = layui.jquery;
            var form = layui.form;
            var layer = layui.layer;

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
