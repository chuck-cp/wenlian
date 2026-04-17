{% extends 'templates/main.volt' %}

{% block content %}

    {{ partial('macros/exam_paper') }}

    {%- macro meta_expiry_info(paper) %}
        <p class="item">
            <span class="key">学习期限</span>
            <span class="value">{{ paper.study_expiry }} 个月</span>
            {% if paper.refund_expiry > 0 %}
                <span class="key">退款期限</span>
                <span class="value">{{ paper.refund_expiry }} 天</span>
            {% else %}
                <span class="key">退款期限</span>
                <span class="value">不支持</span>
            {% endif %}
        </p>
    {%- endmacro %}

    <!-- {%- macro meta_price_info(paper) %}
        <p class="item">
            {% if paper.market_price > 0 %}
                <span class="key">市场价格</span>
                <span class="value price">{{ '￥%0.2f'|format(paper.market_price) }}</span>
            {% else %}
                <span class="key">市场价格</span>
                <span class="value free">免费</span>
            {% endif %}
            {% if paper.vip_price > 0 %}
                <span class="key">会员价格</span>
                <span class="value price">{{ '￥%0.2f'|format(paper.vip_price) }}</span>
            {% else %}
                <span class="key">会员价格</span>
                <span class="value free">免费</span>
            {% endif %}
        </p>
    {%- endmacro %} -->

    {%- macro meta_stats_info(paper) %}
        <p class="item">
            <span class="key">题目数量</span>
            <span class="value">{{ paper.question_count }}</span>
            <span class="key">考试时间</span>
            <span class="value">{{ paper.duration }} 分钟</span>
        </p>
        <p class="item">
            <span class="key">难度级别</span>
            <span class="value">{{ level_type(paper.level) }}</span>
            <span class="key">报名人次</span>
            <span class="value">{{ paper.join_count }}</span>
            {% if paper.pass_score > 0 %}
                <span class="key">通过人次</span>
                <span class="value">{{ paper.pass_count }}</span>
            {% endif %}
        </p>
    {%- endmacro %}

    {% set favorite_class = paper.me.favorited == 1 ? 'layui-icon-star-fill' : 'layui-icon-star' %}
    {% set favorite_title = paper.me.favorited == 1 ? '取消收藏' : '收藏考试' %}
    {% set favorite_url = url({'for':'home.exam_paper.favorite','id':paper.id}) %}

    {% set share_url = share_url('exam_paper',paper.id,auth_user.id) %}
    {% set qrcode_url = url({'for':'home.qrcode'},{'text':share_url}) %}

    {% set history_url = url({'for':'home.exam_paper.mock_history','id':paper.id}) %}
    {% set top_users_url = url({'for':'home.exam_paper.top_mock_users','id':paper.id}) %}
    {% set latest_users_url = url({'for':'home.exam_paper.latest_mock_users','id':paper.id}) %}

    <div class="breadcrumb">
        <span class="layui-breadcrumb">
            <a href="/">首页</a>
            <a href="{{ url({'for':'home.exam_paper.list'}) }}">考试</a>
            <a><cite>{{ paper.title }}</cite></a>
        </span>
        <span class="share">
            <a class="share-wechat" href="javascript:" title="分享到微信"><i class="layui-icon layui-icon-login-wechat"></i></a>
            <a class="share-qq" href="javascript:" title="分享到QQ空间"><i class="layui-icon layui-icon-login-qq"></i></a>
            <a class="share-weibo" href="javascript:" title="分享到微博"><i class="layui-icon layui-icon-login-weibo"></i></a>
            <a class="share-link kg-copy" href="javascript:" title="复制链接" data-clipboard-text="{{ share_url }}"><i class="layui-icon layui-icon-share"></i></a>
        </span>
    </div>

    <div class="course-meta wrap">
        <div class="cover">
            <span class="model">{{ exam_type_badge(paper.exam_type) }}</span>
            <img src="{{ paper.cover }}" alt="{{ paper.title }}">
        </div>
        <div class="info">
            {{ meta_expiry_info(paper) }}
            {{ meta_price_info(paper) }}
            {{ meta_stats_info(paper) }}
        </div>
    </div>

    <div class="layout-main">
        <div class="layout-content">
            <div class="layui-card">
                <div class="layui-card-header">我的记录</div>
                <div class="layui-card-body">
                    {% if auth_user.id > 0 %}
                        <div class="history-list" id="history-list" data-url="{{ history_url }}"></div>
                    {% else %}
                        <div class="no-records">没有相关记录</div>
                    {% endif %}
                </div>
            </div>
        </div>
        <div class="layout-sidebar">
            {% if paper.me.logged == 0 %}
                {% set login_url = url({'for':'home.account.login'}) %}
                <div class="sidebar wrap">
                    <a class="layui-btn layui-btn-fluid" href="{{ login_url }}">用户登录</a>
                </div>
            {% elseif paper.me.owned == 1 %}
                {% set join_url = url({'for':'home.exam_paper.join','id':paper.id}) %}
                <div class="sidebar wrap">
                    <button class="layui-btn layui-btn-normal layui-btn-fluid btn-join" data-url="{{ join_url }}">开始考试</button>
                </div>
            {% elseif paper.me.allow_order == 1 %}
                {% set order_url = url({'for':'home.order.confirm'},{'item_id':paper.id,'item_type':4}) %}
                <div class="sidebar wrap">
                    <button class="layui-btn layui-btn-danger layui-btn-fluid btn-buy" data-url="{{ order_url }}">立即购买</button>
                </div>
            {% endif %}
            <div class="sidebar wrap">
                <div class="layui-tabs user-tab">
                    <ul class="layui-tabs-header">
                        <li class="layui-this">最新学员</li>
                        <li>成绩排行</li>
                    </ul>
                    <div class="layui-tabs-body">
                        <div class="layui-tabs-item layui-show" id="latest-mock-users" data-url="{{ latest_users_url }}"></div>
                        <div class="layui-tabs-item" id="top-mock-users" data-url="{{ top_users_url }}"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="layout-sticky">
        <div class="toolbar-sticky">
            <div class="item">
                <div class="icon" title="学习人次">
                    <i class="layui-icon layui-icon-user icon-user"></i>
                </div>
                <div class="text">{{ paper.join_count }}</div>
            </div>
            <div class="item" id="toolbar-favorite">
                <div class="icon" title="{{ favorite_title }}" data-url="{{ favorite_url }}">
                    <i class="layui-icon icon-star {{ favorite_class }}"></i>
                </div>
                <div class="text" data-count="{{ paper.favorite_count }}">{{ paper.favorite_count }}</div>
            </div>
        </div>
    </div>

    <div class="layui-hide">
        <input type="hidden" name="share.title" value="{{ paper.title }}">
        <input type="hidden" name="share.pic" value="{{ paper.cover }}">
        <input type="hidden" name="share.url" value="{{ share_url }}">
        <input type="hidden" name="share.qrcode" value="{{ qrcode_url }}">
    </div>

{% endblock %}

{% block include_js %}

    {{ js_include('lib/clipboard.min.js') }}
    {{ js_include('home/js/exam.paper.show.js') }}
    {{ js_include('home/js/exam.paper.share.js') }}
    {{ js_include('home/js/copy.js') }}

{% endblock %}
