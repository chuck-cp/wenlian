{% extends 'templates/main.volt' %}

{% block content %}

    {{ partial('macros/coupon') }}

    {%- macro cart_exam_paper_card(paper) %}
        {% set paper_url = url({'for':'home.exam_paper.show','id':paper.id}) %}
        <div class="cart-item-card">
            <div class="cover">
                <span class="type layui-badge layui-bg-blue">试卷</span>
                <img src="{{ paper.cover }}" alt="{{ paper.title }}">
            </div>
            <div class="info">
                <p><a href="{{ paper_url }}" target="_blank">{{ paper.title }}</a></p>
                <!-- <p>
                    <span class="key">市场价格</span>
                    <span class="price">{{ '￥%0.2f'|format(paper.market_price) }}</span>
                    <span class="key">会员价格</span>
                    <span class="price">{{ '￥%0.2f'|format(paper.vip_price) }}</span>
                </p> -->
                <p>
                    <span class="key">学习期限</span>
                    <span class="value">{{ paper.study_expiry }} 个月</span>
                    <!-- {% if paper.refund_expiry > 0 %}
                        <span class="key">退款期限</span>
                        <span class="value">{{ paper.refund_expiry }} 天</span>
                    {% else %}
                        <span class="key">退款期限</span>
                        <span class="value">不支持</span>
                    {% endif %} -->
                </p>
            </div>
        </div>
    {%- endmacro %}

    {%- macro cart_course_card(course) %}
        {% set course_url = url({'for':'home.course.show','id':course.id}) %}
        <div class="cart-item-card">
            <div class="cover">
                <span class="type layui-badge layui-bg-green">课程</span>
                <img src="{{ course.cover }}" alt="{{ course.title }}">
            </div>
            <div class="info">
                <p><a href="{{ course_url }}" target="_blank">{{ course.title }}</a></p>
                <!-- <p>
                    <span class="key">市场价格</span>
                    <span class="price">{{ '￥%0.2f'|format(course.market_price) }}</span>
                    <span class="key">会员价格</span>
                    <span class="price">{{ '￥%0.2f'|format(course.vip_price) }}</span>
                </p> -->
                {% if course.model in [1,2,3] %}
                    <p>
                        <span class="key">学习期限</span>
                        <span class="value">{{ course.study_expiry }} 个月</span>
                        <!-- {% if course.refund_expiry > 0 %}
                            <span class="key">退款期限</span>
                            <span class="value">{{ course.refund_expiry }} 天</span>
                        {% else %}
                            <span class="key">退款期限</span>
                            <span class="value">不支持</span>
                        {% endif %} -->
                    </p>
                {% elseif course.model == 4 %}
                    <p>
                        <span class="key">上课时间</span>
                        <span class="value">{{ course.attrs.start_date }} ~ {{ course.attrs.end_date }}</span>
                        <span class="key">上课地点</span>
                        <span class="value">{{ course.attrs.location }}</span>
                    </p>
                {% endif %}
            </div>
        </div>
    {%- endmacro %}

    {%- macro cart_article_card(article) %}
        {% set article_url = url({'for':'home.article.show','id':article.id}) %}
        <div class="cart-item-card">
            <div class="cover">
                <span class="type layui-badge layui-bg-red">专栏</span>
                <img src="{{ article.cover }}" alt="{{ article.title }}">
            </div>
            <div class="info">
                <p><a href="{{ article_url }}" target="_blank">{{ article.title }}</a></p>
                <!-- <p>
                    <span class="key">市场价格</span>
                    <span class="price">{{ '￥%0.2f'|format(article.market_price) }}</span>
                    <span class="key">会员价格</span>
                    <span class="price">{{ '￥%0.2f'|format(article.vip_price) }}</span>
                </p> -->
                <p>
                    <span class="key">学习期限</span>
                    <span class="value">{{ article.study_expiry }} 个月</span>
                    <!-- <span class="key">退款期限</span>
                    <span class="value">不支持</span> -->
                </p>
            </div>
        </div>
    {%- endmacro %}

    {%- macro cart_vip_card(vip) %}
        <!-- <div class="cart-item-card">
            <div class="cover">
                <span class="type layui-badge layui-bg-orange">会员</span>
                <img src="{{ vip.cover }}" alt="{{ vip.title }}">
            </div>
            <div class="info">
                <p>会员服务</p>
                <p>
                    <span class="key">服务价格</span>
                    <span class="price">{{ '￥%0.2f'|format(vip.price) }}</span>
                </p>
                <p>
                    <span class="key">服务期限</span>
                    <span class="expiry">{{ vip.expiry }} 个月</span>
                </p>
            </div>
        </div> -->
    {%- endmacro %}

    <div class="layui-breadcrumb breadcrumb">
        <a href="/">首页</a>
        <!-- <a><cite>确认订单</cite></a> -->
    </div>

    <div class="cart-item-list wrap">
        {% if confirm.item_type == 1 %}
            {% set course = confirm.item_info.course %}
            {{ cart_course_card(course) }}
        {% elseif confirm.item_type == 2 %}
            {% set package = confirm.item_info.package %}
            {% for course in package.courses %}
                {{ cart_course_card(course) }}
            {% endfor %}
        {% elseif confirm.item_type == 3 %}
            {% set vip = confirm.item_info.vip %}
            {{ cart_vip_card(vip) }}
        {% elseif confirm.item_type == 4 %}
            {% set exam_paper = confirm.item_info.exam_paper %}
            {{ cart_exam_paper_card(exam_paper) }}
        {% elseif confirm.item_type == 5 %}
            {% set article = confirm.item_info.article %}
            {{ cart_article_card(article) }}
        {% endif %}
    </div>

    {% set coupon_list_url = url({'for':'home.coupon.list'}) %}

    {% if confirm.allow_apply_coupon == 1 %}
        <div class="cart-coupon-list">
            <div class="layui-card widget-card">
                <!-- <div class="more">
                    <a href="{{ coupon_list_url }}">领券中心</a>
                </div>
                <div class="layui-card-header">优惠券</div>
                <div class="layui-card-body">
                    <form class="layui-form">
                        <div class="layui-row layui-col-space20">
                            {% for coupon in coupons %}
                                <div class="layui-col-md4">
                                    <div class="coupon-wrap">
                                        {{ coupon_card_info(coupon,coupon.end_time,0) }}
                                        <div class="action">
                                            <input type="radio" name="code_encrypt" value="{{ coupon.code_encrypt }}" title="立即使用" lay-filter="apply_coupon">
                                        </div>
                                    </div>
                                </div>
                            {% else %}
                                <div class="no-matches">没有发现可用优惠券哦，<a href="{{ coupon_list_url }}">到领券中心看看吧！</a></div>
                            {% endfor %}
                        </div>
                    </form>
                </div> -->
            </div>
        </div>
    {% endif %}

    <div class="cart-stats wrap">
        <!-- <div class="info">
            商品总价：<span id="total-amount" class="amount">{{ '￥%0.2f'|format(confirm.total_amount) }}</span>
            优惠金额：<span id="discount-amount" class="amount">{{ '￥%0.2f'|format(confirm.discount_amount) }}</span>
            支付金额：<span id="pay-amount" class="amount pay-amount">{{ '￥%0.2f'|format(confirm.pay_amount) }}</span>
        </div>
        <form class="layui-form cart-form" method="post" action="{{ url({'for':'home.order.create'}) }}">
            <button class="layui-btn layui-btn-danger order-btn" lay-submit="true" lay-filter="go">提交订单</button>
            <input type="hidden" name="item_id" value="{{ confirm.item_id }}">
            <input type="hidden" name="item_type" value="{{ confirm.item_type }}">
            <input type="hidden" name="coupon_code">
        </form> -->
    </div>

{% endblock %}

{% block include_js %}

    {{ js_include('home/js/order.confirm.js') }}

{% endblock %}
