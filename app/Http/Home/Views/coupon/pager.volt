{{ partial('macros/coupon') }}

{% if pager.total_pages > 0 %}
    <div class="coupon-list">
        <div class="layui-row layui-col-space20">
            {% for item in pager.items %}
                {% set claim_url = url({'for':'home.coupon.claim','id':item.id}) %}
                <div class="layui-col-md4">
                    <div class="coupon-wrap">
                        {{ coupon_card_info(item) }}
                        <div class="action">
                            {% if item.me.claimed == 0 %}
                                <button class="layui-btn layui-btn-danger layui-btn-sm btn-claim" data-url="{{ claim_url }}">立即领取</button>
                            {% else %}
                                <button class="layui-btn layui-btn-sm layui-btn-disabled">已经领取</button>
                            {% endif %}
                        </div>
                    </div>
                </div>
            {% endfor %}
        </div>
    </div>
    {{ partial('partials/pager_ajax') }}
{% endif %}
