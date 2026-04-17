{% extends 'templates/main.volt' %}

{% block content %}

    <form class="layui-form kg-form" method="POST" action="{{ url({'for':'admin.distribution.create'}) }}">
        <fieldset class="layui-elem-field layui-field-title">
            <legend>添加商品</legend>
        </fieldset>
        <div class="layui-form-item">
            <label class="layui-form-label">商品类型</label>
            <div class="layui-input-block">
                {% for value,title in item_types %}
                    {% set checked = value == 1 ? 'checked="checked"' : '' %}
                    <input type="radio" name="item_type" value="{{ value }}" title="{{ title }}" {{ checked }} lay-filter="item_type">
                {% endfor %}
            </div>
        </div>
        <div id="item-block-1" class="item-block" style="display:block;">
            <div class="layui-form-item">
                <label class="layui-form-label">课程选择</label>
                <div class="layui-input-block">
                    <div id="xm-course-id"></div>
                </div>
            </div>
        </div>
        <div id="item-block-2" class="item-block" style="display:none;">
            <div class="layui-form-item">
                <label class="layui-form-label">套餐选择</label>
                <div class="layui-input-block">
                    <div id="xm-package-id"></div>
                </div>
            </div>
        </div>
        <div id="item-block-3" class="item-block" style="display:none;">
            <div class="layui-form-item">
                <label class="layui-form-label">会员选择</label>
                <div class="layui-input-block">
                    <div id="xm-vip-id"></div>
                </div>
            </div>
        </div>
        <div id="item-block-4" class="item-block" style="display:none;">
            <div class="layui-form-item">
                <label class="layui-form-label">试卷选择</label>
                <div class="layui-input-block">
                    <div id="xm-paper-id"></div>
                </div>
            </div>
        </div>
        <div id="item-block-5" class="item-block" style="display:none;">
            <div class="layui-form-item">
                <label class="layui-form-label">文章选择</label>
                <div class="layui-input-block">
                    <div id="xm-article-id"></div>
                </div>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">一级佣金比例</label>
            <div class="layui-input-block">
                <select name="v1_com_rate" lay-verify="required">
                    <option value="">请选择</option>
                    {% for value in 1..30 %}
                        <option value="{{ value }}">{{ value }}%</option>
                    {% endfor %}
                </select>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">二级佣金比例</label>
            <div class="layui-input-block">
                <select name="v2_com_rate" lay-verify="required">
                    <option value="">请选择</option>
                    {% for value in 1..20 %}
                        <option value="{{ value }}">{{ value }}%</option>
                    {% endfor %}
                </select>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">三级佣金比例</label>
            <div class="layui-input-block">
                <select name="v3_com_rate" lay-verify="required">
                    <option value="">请选择</option>
                    {% for value in 1..10 %}
                        <option value="{{ value }}">{{ value }}%</option>
                    {% endfor %}
                </select>
            </div>
        </div>
        <div class="layui-form-item" id="time-range">
            <label class="layui-form-label">活动时间</label>
            <div class="layui-input-inline">
                <input id="start-time" class="layui-input" type="text" name="start_time" autocomplete="off" lay-verify="required">
            </div>
            <div class="layui-form-mid"> 至</div>
            <div class="layui-input-inline">
                <input id="end-time" class="layui-input" type="text" name="end_time" autocomplete="off" lay-verify="required">
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
            var laydate = layui.laydate;

            laydate.render({
                elem: '#time-range',
                type: 'datetime',
                range: ['#start-time','#end-time'],
            });

            xmSelect.render({
                el: '#xm-course-id',
                name: 'xm_course_id',
                filterable: true,
                filterMethod: function (val, item) {
                    return item.name.toLowerCase().indexOf(val.toLowerCase()) !== -1;
                },
                data: {{ xm_courses|json_encode }}
            });

            xmSelect.render({
                el: '#xm-package-id',
                name: 'xm_package_id',
                filterable: true,
                filterMethod: function (val, item) {
                    return item.name.toLowerCase().indexOf(val.toLowerCase()) !== -1;
                },
                data: {{ xm_packages|json_encode }}
            });

            xmSelect.render({
                el: '#xm-paper-id',
                name: 'xm_paper_id',
                filterable: true,
                filterMethod: function (val, item) {
                    return item.name.toLowerCase().indexOf(val.toLowerCase()) !== -1;
                },
                data: {{ xm_exam_papers|json_encode }}
            });

            xmSelect.render({
                el: '#xm-article-id',
                name: 'xm_article_id',
                filterable: true,
                filterMethod: function (val, item) {
                    return item.name.toLowerCase().indexOf(val.toLowerCase()) !== -1;
                },
                data: {{ xm_articles|json_encode }}
            });

            xmSelect.render({
                el: '#xm-vip-id',
                name: 'xm_vip_id',
                data: {{ xm_vips|json_encode }}
            });

            form.on('radio(item_type)', function (data) {
                $('.item-block').hide();
                $('#item-block-' + data.value).show();
            });

        });

    </script>

{% endblock %}
