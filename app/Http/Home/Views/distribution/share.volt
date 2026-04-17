{% extends 'templates/layer.volt' %}

{% block content %}

    {% set item = distribution.item %}

    {% if item.type == 1 %}
        {% set type = 'course' %}
    {% elseif item.type == 2 %}
        {% set type = 'package' %}
    {% elseif item.type == 3 %}
        {% set type = 'vip' %}
    {% elseif item.type == 4 %}
        {% set type = 'exam_paper' %}
    {% elseif item.type == 5 %}
        {% set type = 'article' %}
    {% else %}
        {% set type = 'home' %}
    {% endif %}

    {% set share_url = share_url(type,item.id,auth_user.id) %}
    {% set qrcode_url = url({'for':'home.qrcode'},{'text':share_url}) %}

    <div class="dist-share">
        <div class="share-link">
            <div class="layui-inline">
                <input class="layui-input" type="text" name="link" value="{{ share_url }}" id="tc">
            </div>
            <div class="layui-inline">
                <button class="layui-btn kg-copy" type="button" data-clipboard-target="#tc">复制</button>
            </div>
        </div>
        <div class="icon-list">
            <div class="icon">
                <a href="javascript:" title="分享到微信"><i class="iconfont icon-wechat"></i></a>
            </div>
            <div class="icon">
                <a href="javascript:" title="分享到QQ"><i class="iconfont icon-qq"></i></a>
            </div>
            <div class="icon">
                <a href="javascript:" title="分享到QQ空间"><i class="iconfont icon-qzone"></i></a>
            </div>
            <div class="icon">
                <a href="javascript:" title="分享到微博"><i class="iconfont icon-weibo"></i></a>
            </div>
            <div class="icon">
                <a href="javascript:" title="分享到豆瓣"><i class="iconfont icon-douban"></i></a>
            </div>
        </div>
    </div>

    <div class="layui-hide">
        <input type="hidden" name="share.title" value="{{ distribution.item.title }}">
        <input type="hidden" name="share.pic" value="{{ distribution.item.cover }}">
        <input type="hidden" name="share.url" value="{{ share_url }}">
        <input type="hidden" name="share.qrcode" value="{{ qrcode_url }}">
    </div>

{% endblock %}

{% block include_js %}

    {{ js_include('lib/clipboard.min.js') }}
    {{ js_include('home/js/copy.js') }}
    {{ js_include('home/js/distribution.share.js') }}

{% endblock %}
