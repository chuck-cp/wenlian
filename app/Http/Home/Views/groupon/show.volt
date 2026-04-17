{% extends 'templates/main.volt' %}

{% block content %}

    {{ partial('macros/sale') }}

    {% if groupon.item.type == 1 %}
        {% set item_url = url({'for':'home.course.show','id':groupon.item.id}) %}
    {% elseif groupon.item.type == 2 %}
        {% set item_url = url({'for':'home.package.show','id':groupon.item.id}) %}
    {% elseif groupon.item.type == 3 %}
        {% set item_url = url({'for':'home.vip.index'}) %}
    {% elseif groupon.item.type == 4 %}
        {% set item_url = url({'for':'home.exam_paper.show','id':groupon.item.id}) %}
    {% elseif groupon.item.type == 5 %}
        {% set item_url = url({'for':'home.article.show','id':groupon.item.id}) %}
    {% else %}
        {% set item_url = 'javascript:' %}
    {% endif %}

    {% set share_url = share_url('groupon',groupon.id,auth_user.id) %}
    {% set qrcode_url = url({'for':'home.qrcode'},{'text':share_url}) %}
    {% set direct_order_url = url({'for':'home.order.confirm'},{'item_id':groupon.item.id,'item_type':groupon.item.type}) %}
    {% set groupon_order_url = url({'for':'home.groupon.order','id':groupon.id}) %}
    {% set team_list_url = url({'for':'home.groupon.teams','id':groupon.id}) %}

    <div class="breadcrumb">
        <span class="layui-breadcrumb">
            <a href="/">首页</a>
            <a href="{{ url({'for':'home.groupon.list'}) }}">拼团</a>
            <a><cite>{{ groupon.item.title }}</cite></a>
        </span>
        <span class="share">
            <a class="share-wechat" href="javascript:" title="分享到微信"><i class="layui-icon layui-icon-login-wechat"></i></a>
            <a class="share-qq" href="javascript:" title="分享到QQ空间"><i class="layui-icon layui-icon-login-qq"></i></a>
            <a class="share-weibo" href="javascript:" title="分享到微博"><i class="layui-icon layui-icon-login-weibo"></i></a>
            <a class="share-link kg-copy" href="javascript:" title="复制链接" data-clipboard-text="{{ share_url }}"><i class="layui-icon layui-icon-share"></i></a>
        </span>
    </div>

    <div class="groupon-meta wrap">
        <div class="cover">
            <span class="type">{{ sale_item_type_badge(groupon.item.type) }}</span>
            <img src="{{ groupon.item.cover }}" alt="{{ groupon.item.title }}">
        </div>
        <div class="info">
            <div class="item title">
                <span><a href="{{ item_url }}" target="_blank" title="查看详情">{{ groupon.item.title }}</a></span>
                <span class="layui-badge">{{ '%s人团'|format(groupon.partner_limit) }}</span>
            </div>
            <div class="item price">
                <span class="key">原始价格</span>
                <span class="value origin-price">{{ '￥%0.2f'|format(groupon.item.price) }}</span>
                <span class="key">拼团价格</span>
                <span class="value member-price">{{ '￥%0.2f'|format(groupon.member_price) }}</span>
            </div>
            <div class="item">
                <span class="key">活动时间</span>
                <span class="value">{{ date('Y-m-d',groupon.start_time) }} 至 {{ date('Y-m-d',groupon.end_time) }}</span>
            </div>
            <div class="action">
                <button class="layui-btn layui-btn-normal layui-btn-sm btn-direct-order" data-url="{{ direct_order_url }}">{{ '￥%0.2f'|format(groupon.item.price) }} 直接购买</button>
                <button class="layui-btn layui-btn-danger layui-btn-sm btn-leader-order" data-url="{{ groupon_order_url }}">{{ '￥%0.2f'|format(groupon.leader_price) }} 发起拼团</button>
            </div>
        </div>
    </div>

    {% if groupon.total_team_count > 0 %}
        <div class="layui-card">
            <div class="layui-card-header">拼团队伍</div>
            <div class="layui-card-body">
                <div class="groupon-team-list" id="team-list" data-url="{{ team_list_url }}"></div>
            </div>
        </div>
    {% else %}
        <div class="groupon-empty-teams wrap">
            <i class="layui-icon layui-icon-tips"></i> 还没有人发起拼团，抓紧时间行动吧！
        </div>
    {% endif %}

    <div class="layui-hide">
        <input type="hidden" name="share.title" value="{{ groupon.item.title }}">
        <input type="hidden" name="share.pic" value="{{ groupon.item.cover }}">
        <input type="hidden" name="share.url" value="{{ share_url }}">
        <input type="hidden" name="share.qrcode" value="{{ qrcode_url }}">
    </div>

{% endblock %}

{% block include_js %}

    {{ js_include('lib/clipboard.min.js') }}
    {{ js_include('home/js/groupon.show.js') }}
    {{ js_include('home/js/groupon.share.js') }}
    {{ js_include('home/js/copy.js') }}

{% endblock %}
