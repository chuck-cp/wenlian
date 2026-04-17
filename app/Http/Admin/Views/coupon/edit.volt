{% extends 'templates/main.volt' %}

{% block content %}

    {% set coupon.start_time = coupon.start_time > 0 ? date('Y-m-d H:i:s',coupon.start_time) : '' %}
    {% set coupon.end_time = coupon.end_time > 0 ? date('Y-m-d H:i:s',coupon.end_time) : '' %}

    {% set block1_style = coupon.item_type == 1 ? 'display:block;' : 'display:none;' %}
    {% set block2_style = coupon.item_type == 2 ? 'display:block;' : 'display:none;' %}
    {% set block3_style = coupon.item_type == 3 ? 'display:block;' : 'display:none;' %}
    {% set block4_style = coupon.item_type == 4 ? 'display:block;' : 'display:none;' %}
    {% set block5_style = coupon.item_type == 5 ? 'display:block;' : 'display:none;' %}

    <form class="layui-form kg-form" method="POST" action="{{ url({'for':'admin.coupon.update','id':coupon.id}) }}">
        <fieldset class="layui-elem-field layui-field-title">
            <legend>编辑优惠券</legend>
        </fieldset>
        <div class="layui-form-item">
            <label class="layui-form-label">名称</label>
            <div class="layui-input-block">
                <input class="layui-input" type="text" name="name" value="{{ coupon.name }}" lay-verify="required">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">类型</label>
            <div class="layui-input-block">
                {% for value,title in types %}
                    {% set checked = coupon.type == value ? 'checked="checked"' : '' %}
                    <input type="radio" name="type" value="{{ value }}" title="{{ title }}" {{ checked }} disabled="disabled" lay-filter="type">
                {% endfor %}
            </div>
        </div>
        {% if coupon.type == 1 %}
            <div id="type-block-1" class="type-block">
                <div class="layui-form-item">
                    <label class="layui-form-label">抵扣额度</label>
                    <div class="layui-input-inline">
                        <input class="layui-input" type="text" name="attrs[deduct_amount]" value="{{ coupon.attrs['deduct_amount'] }}" lay-verify="number">
                    </div>
                    <div class="layui-form-mid gray">元</div>
                </div>
            </div>
        {% endif %}
        {% if coupon.type == 2 %}
            <div id="type-block-2" class="type-block">
                <div class="layui-form-item">
                    <label class="layui-form-label">抵扣比例</label>
                    <div class="layui-input-inline">
                        <select name="discount_rate" lay-filter="required">
                            <option value="">请选择</option>
                            {% for value in discount_rates %}
                                {% set selected = value == coupon.attrs['discount_rate'] ? 'selected="selected"' : '' %}
                                <option value="{{ value }}" {{ selected }}>{{ value }}%</option>
                            {% endfor %}
                        </select>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">最多抵扣</label>
                    <div class="layui-input-inline">
                        <input class="layui-input" type="text" name="attrs[max_deduct_amount]" placeholder="不限制请设为0" value="{{ coupon.attrs['max_deduct_amount'] }}" lay-verify="number">
                    </div>
                    <div class="layui-form-mid gray">元</div>
                </div>
            </div>
        {% endif %}
        <div class="layui-form-item">
            <label class="layui-form-label">使用门槛</label>
            <div class="layui-input-inline">
                <input class="layui-input" type="text" name="consume_limit" placeholder="无限制请设为0" value="{{ coupon.consume_limit }}" lay-verify="number">
            </div>
            <div class="layui-form-mid gray">元</div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">发行数量</label>
            <div class="layui-input-block">
                <input class="layui-input" type="text" name="total_usage" value="{{ coupon.total_usage }}" lay-verify="number">
                <div class="kg-field-tips">优惠券可供使用的最大次数</div>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">用户限量</label>
            <div class="layui-input-block">
                <input class="layui-input" type="text" name="user_usage" value="{{ coupon.user_usage }}" lay-verify="number">
                <div class="kg-field-tips">用户使用此优惠券的最大次数</div>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">活动商品</label>
            <div class="layui-input-block">
                {% set checked = coupon.item_type == 0 ? 'checked="checked"' : '' %}
                <input type="radio" name="item_type" value="0" title="不限" {{ checked }} lay-filter="item_type">
                {% for value,title in item_types %}
                    {% set checked = value == coupon.item_type ? 'checked="checked"' : '' %}
                    <input type="radio" name="item_type" value="{{ value }}" title="{{ title }}" {{ checked }} lay-filter="item_type">
                {% endfor %}
            </div>
        </div>
        <div id="item-block-1" class="item-block" style="{{ block1_style }}">
            <div class="layui-form-item">
                <label class="layui-form-label">课程选择</label>
                <div class="layui-input-block">
                    <div id="xm-course-id"></div>
                </div>
            </div>
        </div>
        <div id="item-block-2" class="item-block" style="{{ block2_style }}">
            <div class="layui-form-item">
                <label class="layui-form-label">套餐选择</label>
                <div class="layui-input-block">
                    <div id="xm-package-id"></div>
                </div>
            </div>
        </div>
        <div id="item-block-3" class="item-block" style="{{ block3_style }}">
            <div class="layui-form-item">
                <label class="layui-form-label">会员选择</label>
                <div class="layui-input-block">
                    <div id="xm-vip-id"></div>
                </div>
            </div>
        </div>
        <div id="item-block-4" class="item-block" style="{{ block4_style }}">
            <div class="layui-form-item">
                <label class="layui-form-label">试卷选择</label>
                <div class="layui-input-block">
                    <div id="xm-paper-id"></div>
                </div>
            </div>
        </div>
        <div id="item-block-5" class="item-block" style="{{ block5_style }}">
            <div class="layui-form-item">
                <label class="layui-form-label">文章选择</label>
                <div class="layui-input-block">
                    <div id="xm-article-id"></div>
                </div>
            </div>
        </div>
        <div class="layui-form-item" id="time-range">
            <label class="layui-form-label">有效期限</label>
            <div class="layui-input-inline">
                <input class="layui-input" id="start-time" type="text" name="start_time" autocomplete="off" value="{{ coupon.start_time }}" lay-verify="required">
            </div>
            <div class="layui-form-mid gray">至</div>
            <div class="layui-input-inline">
                <input class="layui-input" id="end-time" type="text" name="end_time" autocomplete="off" value="{{ coupon.end_time }}" lay-verify="required">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">私有</label>
            <div class="layui-input-block">
                <input type="radio" name="private" value="1" title="是" {% if coupon.private == 1 %}checked="checked"{% endif %}>
                <input type="radio" name="private" value="0" title="否" {% if coupon.private == 0 %}checked="checked"{% endif %}>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">发布</label>
            <div class="layui-input-block">
                <input type="radio" name="published" value="1" title="是" {% if coupon.published == 1 %}checked="checked"{% endif %}>
                <input type="radio" name="published" value="0" title="否" {% if coupon.published == 0 %}checked="checked"{% endif %}>
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

        layui.use(['jquery', 'form', 'laydate'], function () {

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
                radio: true,
                filterMethod: function (val, item) {
                    return item.name.toLowerCase().indexOf(val.toLowerCase()) !== -1;
                },
                data: {{ xm_courses|json_encode }}
            });

            xmSelect.render({
                el: '#xm-package-id',
                name: 'xm_package_id',
                filterable: true,
                radio: true,
                filterMethod: function (val, item) {
                    return item.name.toLowerCase().indexOf(val.toLowerCase()) !== -1;
                },
                data: {{ xm_packages|json_encode }}
            });

            xmSelect.render({
                el: '#xm-paper-id',
                name: 'xm_paper_id',
                filterable: true,
                radio: true,
                filterMethod: function (val, item) {
                    return item.name.toLowerCase().indexOf(val.toLowerCase()) !== -1;
                },
                data: {{ xm_exam_papers|json_encode }}
            });

            xmSelect.render({
                el: '#xm-article-id',
                name: 'xm_article_id',
                filterable: true,
                radio: true,
                filterMethod: function (val, item) {
                    return item.name.toLowerCase().indexOf(val.toLowerCase()) !== -1;
                },
                data: {{ xm_articles|json_encode }}
            });

            xmSelect.render({
                el: '#xm-vip-id',
                name: 'xm_vip_id',
                radio: true,
                data: {{ xm_vips|json_encode }}
            });

            form.on('radio(type)', function (data) {
                $('.type-block').hide();
                $('#type-block-' + data.value).show();
            });

            form.on('radio(item_type)', function (data) {
                $('.item-block').hide();
                $('#item-block-' + data.value).show();
            });

        });

    </script>

{% endblock %}
