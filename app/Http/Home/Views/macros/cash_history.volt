{%- macro event_amount_info(value) %}
    {% if value > 0 %}
        <span class="layui-badge layui-bg-red">+{{ '%0.2f'|format(value) }}</span>
    {% else %}
        <span class="layui-badge layui-bg-green">{{ '%0.2f'|format(value) }}</span>
    {% endif %}
{%- endmacro %}

{%- macro event_type_info(value) %}
    {% if value == 1 %}
        <span class="type">申请提现</span>
    {% elseif value == 2 %}
        <span class="type">商品分销</span>
    {% elseif value == 3 %}
        <span class="type">抽奖奖励</span>
    {% elseif value == 4 %}
        <span class="type">提现退款</span>
    {% endif %}
{%- endmacro %}

{%- macro event_detail_info(type,info) %}
    {% if type == 1 %}
        <p>N/A</p>
    {% elseif type == 2 %}
        <p>订单：{{ info.order.subject }}（{{ '￥%0.2f'|format(info.order.amount) }}）</p>
        <p>用户：{{ info.referer.name }}（{{ '%s级用户'|format(info.referer.level) }}）</p>
    {% elseif type == 3 %}
        <p>N/A</p>
    {% elseif type == 4 %}
        <p>N/A</p>
    {% endif %}
{%- endmacro %}