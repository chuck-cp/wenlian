{% extends 'templates/main.volt' %}

{% block content %}

    {{ partial('macros/article') }}

    {% set share_url = share_url('article',article.id,auth_user.id) %}
    {% set qrcode_url = url({'for':'home.qrcode'},{'text':share_url}) %}
    {% set order_url = url({'for':'home.order.confirm'},{'item_id':article.id,'item_type':5}) %}
    {% set related_url = url({'for':'home.article.related','id':article.id}) %}

    <div class="breadcrumb">
        <span class="layui-breadcrumb">
            <a href="/">首页</a>
            <a><cite>专栏</cite></a>
            <a><cite>详情</cite></a>
        </span>
        <span class="share">
            <a class="share-wechat" href="javascript:" title="分享到微信"><i class="layui-icon layui-icon-login-wechat"></i></a>
            <a class="share-qq" href="javascript:" title="分享到QQ空间"><i class="layui-icon layui-icon-login-qq"></i></a>
            <a class="share-weibo" href="javascript:" title="分享到微博"><i class="layui-icon layui-icon-login-weibo"></i></a>
            <a class="share-link kg-copy" href="javascript:" title="复制链接" data-clipboard-text="{{ share_url }}"><i class="layui-icon layui-icon-share"></i></a>
        </span>
    </div>

    <div class="layout-main watermark">
        <div class="layout-content">
            <div class="article-info wrap">
                <div class="title">{{ article.title }}</div>
                <div class="meta">
                    <div class="left">
                        <span class="source">{{ source_type_badge(article.source_type) }}</span>
                        <span class="time">{{ article.create_time|time_ago }}</span>
                        <span class="view">{{ article.view_count }} 阅读</span>
                        <span class="comment">{{ article.comment_count }} 评论</span>
                    </div>
                </div>
                {% if article.format == 'html' %}
                    <div class="content watermark ke-content kg-zoom">{{ article.content }}</div>
                {% elseif article.format == 'markdown' %}
                    <div class="content watermark markdown-body kg-zoom">{{ article.content }}</div>
                {% endif %}
                {% if article.market_price > 0 %}
                    {% if article.me.logged == 0 or article.me.allow_order == 1 %}
                        <div class="pay-tips">
                            <button class="layui-btn layui-btn-danger btn-buy" data-url="{{ order_url }}">解锁全文（{{ '￥%0.2f'|format(article.market_price) }}）</button>
                        </div>
                    {% endif %}
                {% endif %}
                {% if article.tags %}
                    <div class="tags">
                        {% for item in article.tags %}
                            {% set url = url({'for':'home.article.list'},{'tag_id':item.id}) %}
                            <a href="{{ url }}" class="layui-btn layui-btn-xs">{{ item.name }}</a>
                        {% endfor %}
                    </div>
                {% endif %}
            </div>
            <div id="comment-anchor"></div>
            {% if article.closed == 0 %}
                <div class="article-comment wrap">
                    {{ partial('article/comment') }}
                </div>
            {% else %}
                <div class="wrap center gray">
                    <i class="layui-icon layui-icon-tips"></i> 评论已关闭
                </div>
            {% endif %}
        </div>
        <div class="layout-sidebar">
            {% if article.owner.id > 0 %}
                {{ partial('article/show_owner') }}
            {% endif %}
            <div class="sidebar" id="sidebar-related" data-url="{{ related_url }}"></div>
        </div>
    </div>

    <div class="layout-sticky">
        {{ partial('article/sticky') }}
    </div>

    <div class="layui-hide">
        <input type="hidden" name="share.title" value="{{ article.title }}">
        <input type="hidden" name="share.pic" value="{{ article.cover }}">
        <input type="hidden" name="share.url" value="{{ share_url }}">
        <input type="hidden" name="share.qrcode" value="{{ qrcode_url }}">
    </div>

{% endblock %}

{% block link_css %}

    {% if article.format == 'markdown' %}
        {{ css_link('home/css/markdown.css') }}
    {% else %}
        {{ css_link('home/css/content.css') }}
    {% endif %}

{% endblock %}

{% block include_js %}

    {{ js_include('lib/clipboard.min.js') }}
    {{ js_include('home/js/article.show.js') }}
    {{ js_include('home/js/article.share.js') }}
    {{ js_include('home/js/comment.js') }}
    {{ js_include('home/js/copy.js') }}

    {% if site_info.copy_enabled == 0 %}
        {{ js_include('home/js/copy.watermark.js') }}
    {% endif %}

{% endblock %}
