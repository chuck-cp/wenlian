{% extends 'templates/main.volt' %}

{% block content %}

    {{ partial('macros/coupon') }}

    <div class="layout-main">
        <div class="my-sidebar">{{ partial('user/console/menu') }}</div>
        <div class="my-content">
            <div class="wrap">
                <form class="layui-form redeem-form" method="post" action="{{ url({'for':'home.coupon.redeem'}) }}">
                    <div class="layui-inline">
                        <input class="layui-input" type="text" name="code" autocomplete="off" placeholder="请输入兑换码" lay-verify="required">
                    </div>
                    <div class="layui-inline">
                        <button class="layui-btn" lay-submit="true" lay-filter="go">兑换</button>
                    </div>
                </form>
            </div>
            <div class="wrap">
                <div class="my-nav">
                    <span class="title">优惠券</span>
                    <a class="layui-btn layui-btn-sm sub-nav" href="{{ url({'for':'home.coupon.list'}) }}">领券中心</a>
                </div>
                {% if pager.total_pages > 0 %}
                    <div class="coupon-list">
                        <div class="layui-row layui-col-space20">
                            {% for item in pager.items %}
                                <div class="layui-col-md6">
                                    <div class="coupon-wrap">
                                        {{ coupon_card_info(item.coupon) }}
                                    </div>
                                </div>
                            {% endfor %}
                        </div>
                    </div>
                    {{ partial('partials/pager') }}
                    <br>
                {% endif %}
            </div>
        </div>
    </div>

{% endblock %}
