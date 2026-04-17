{% extends 'templates/main.volt' %}

{% block content %}

    {% set share_url = share_url('chapter',chapter.id,auth_user.id) %}
    {% set qrcode_url = url({'for':'home.qrcode'},{'text':share_url}) %}
    {% set course_url = url({'for':'home.course.show','id':chapter.course.id}) %}
    {% set learning_url = url({'for':'home.chapter.learning','id':chapter.id}) %}

    <div class="breadcrumb">
        <span class="layui-breadcrumb">
            <a href="{{ course_url }}"><i class="layui-icon layui-icon-return"></i> 返回课程</a>
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
                <div class="title">{{ chapter.title }}</div>
                {% if chapter.format == 'html' %}
                    <div class="content watermark ke-content kg-zoom">{{ chapter.content }}</div>
                {% elseif chapter.format == 'markdown' %}
                    <div class="content watermark markdown-body kg-zoom">{{ chapter.content }}</div>
                {% endif %}
            </div>
            <div id="comment-anchor"></div>
            {% if chapter.settings.comment_enabled == 1 %}
                <div class="article-comment wrap">
                    {{ partial('chapter/comment') }}
                </div>
            {% else %}
                <div class="wrap center gray">
                    <i class="layui-icon layui-icon-tips"></i> 评论已关闭
                </div>
            {% endif %}
        </div>
        <div class="layout-sidebar">
            {{ partial('chapter/catalog') }}
        </div>
    </div>

    <div class="layout-sticky">
        {{ partial('chapter/sticky') }}
    </div>

    <div class="layui-hide">
        <input type="hidden" name="chapter.id" value="{{ chapter.id }}">
        <input type="hidden" name="chapter.learning_url" value="{{ learning_url }}">
        <input type="hidden" name="chapter.me.plan_id" value="{{ chapter.me.plan_id }}">
    </div>

    <div class="layui-hide">
        <input type="hidden" name="share.title" value="{{ chapter.course.title }}">
        <input type="hidden" name="share.pic" value="{{ chapter.course.cover }}">
        <input type="hidden" name="share.url" value="{{ share_url }}">
        <input type="hidden" name="share.qrcode" value="{{ qrcode_url }}">
    </div>

{% endblock %}

{% block link_css %}

    {% if chapter.format == 'markdown' %}
        {{ css_link('home/css/markdown.css') }}
    {% else %}
        {{ css_link('home/css/content.css') }}
    {% endif %}

{% endblock %}

{% block include_js %}

    {{ js_include('lib/clipboard.min.js') }}
    {{ js_include('home/js/course.share.js') }}
    {{ js_include('home/js/chapter.read.js') }}
    {{ js_include('home/js/chapter.show.js') }}
    {{ js_include('home/js/comment.js') }}
    {{ js_include('home/js/copy.js') }}

    {% if site_info.copy_enabled == 0 %}
        {{ js_include('home/js/copy.watermark.js') }}
    {% endif %}

{% endblock %}