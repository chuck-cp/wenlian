{% extends 'templates/main.volt' %}

{% block content %}

    {{ partial('macros/groupon') }}

    {% set groupon.item_info = objectify(groupon.item_info) %}
    {% set groupon.start_time = groupon.start_time > 0 ? date('Y-m-d H:i:s',groupon.start_time) : '' %}
    {% set groupon.end_time = groupon.end_time > 0 ? date('Y-m-d H:i:s',groupon.end_time) : '' %}

    <form class="layui-form kg-form" method="POST" action="{{ url({'for':'admin.groupon.update','id':groupon.id}) }}">
        <fieldset class="layui-elem-field layui-field-title">
            <legend>编辑商品</legend>
        </fieldset>
        <div class="layui-form-item">
            <label class="layui-form-label">商品信息</label>
            <div class="layui-input-block gray">{{ item_full_info(groupon.item_type,groupon.item_info) }}</div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">成员价格</label>
            <div class="layui-input-inline">
                <input class="layui-input" type="text" name="member_price" value="{{ groupon.member_price }}" lay-verify="number">
            </div>
            <div class="layui-form-mid gray">元</div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">团长价格</label>
            <div class="layui-input-inline">
                <input class="layui-input" type="text" name="leader_price" value="{{ groupon.leader_price }}" lay-verify="number">
            </div>
            <div class="layui-form-mid gray">元</div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">虚拟成团</label>
            <div class="layui-input-block">
                <input type="radio" name="virtual_partner" value="1" title="是" {% if groupon.virtual_partner == 1 %}checked="checked"{% endif %}>
                <input type="radio" name="virtual_partner" value="0" title="否" {% if groupon.virtual_partner == 0 %}checked="checked"{% endif %}>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">成团人数</label>
            <div class="layui-input-inline">
                <select name="partner_limit" lay-verify="required">
                    <option value="">请选择</option>
                    {% for value in 2..20 %}
                        {% set selected = value == groupon.partner_limit ? 'selected="selected"' : '' %}
                        <option value="{{ value }}" {{ selected }}>{{ value }}</option>
                    {% endfor %}
                </select>
            </div>
            <div class="layui-form-mid gray">人</div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">成团期限</label>
            <div class="layui-input-inline">
                <select name="partner_expiry" lay-verify="required">
                    <option value="">请选择</option>
                    {% for value in 1..7 %}
                        {% set selected = value == groupon.partner_expiry ? 'selected="selected"' : '' %}
                        <option value="{{ value }}" {{ selected }}>{{ value }}</option>
                    {% endfor %}
                </select>
            </div>
            <div class="layui-form-mid gray">天</div>
        </div>
        <div class="layui-form-item" id="time-range">
            <label class="layui-form-label">活动时间</label>
            <div class="layui-input-inline">
                <input class="layui-input" id="start-time" type="text" name="start_time" autocomplete="off" value="{{ groupon.start_time }}" lay-verify="required">
            </div>
            <div class="layui-form-mid gray">至</div>
            <div class="layui-input-inline">
                <input class="layui-input" id="end-time" type="text" name="end_time" autocomplete="off" value="{{ groupon.end_time }}" lay-verify="required">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">发布</label>
            <div class="layui-input-block">
                <input type="radio" name="published" value="1" title="是" {% if groupon.published == 1 %}checked="checked"{% endif %}>
                <input type="radio" name="published" value="0" title="否" {% if groupon.published == 0 %}checked="checked"{% endif %}>
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

        layui.use(['laydate'], function () {

            var laydate = layui.laydate;

            laydate.render({
                elem: '#time-range',
                type: 'datetime',
                range: ['#start-time','#end-time'],
            });

        });

    </script>

{% endblock %}
