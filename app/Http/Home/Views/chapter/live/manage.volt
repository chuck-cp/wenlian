{% extends 'templates/layer.volt' %}

{% block content %}

    {% set update_settings_url = url({'for':'home.live.update_settings','id':chapter.id}) %}
    {% set block_user_url = url({'for':'home.live.block_user','id':chapter.id}) %}
    {% set online_users_url = url({'for':'home.live.online_users','id':chapter.id}) %}
    {% set blocked_users_url = url({'for':'home.live.blocked_users','id':chapter.id}) %}

    <div class="layui-tabs">
        <ul class="layui-tabs-header kg-tab-title">
            <li class="layui-this">基本设置</li>
            <li>在线用户</li>
            <li>禁言名单</li>
        </ul>
        <div class="layui-tabs-body">
            <div class="layui-tabs-item layui-show">
                <form class="layui-form" method="POST" action="{{ update_settings_url }}">
                    <div class="layui-form-item">
                        <label class="layui-form-label">开启讨论</label>
                        <div class="layui-input-block">
                            <input type="radio" name="settings[chat_enabled]" value="1" title="是" {% if chapter.settings.chat_enabled == 1 %}checked="checked"{% endif %}>
                            <input type="radio" name="settings[chat_enabled]" value="0" title="否" {% if chapter.settings.chat_enabled == 0 %}checked="checked"{% endif %}>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label"></label>
                        <div class="layui-input-block">
                            <button class="layui-btn" lay-submit="true" lay-filter="go">提交</button>
                            <button type="reset" class="layui-btn layui-btn-primary">重置</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="layui-tabs-item">
                <div id="online-user-list" data-url="{{ online_users_url }}"></div>
            </div>
            <div class="layui-tabs-item">
                <div id="blocked-user-list" data-url="{{ blocked_users_url }}"></div>
            </div>
        </div>
    </div>

    <div class="layui-hide">
        <input type="hidden" name="chapter_id" value="{{ chapter.id }}">
    </div>

{% endblock %}

{% block include_js %}

    {{ js_include('home/js/chapter.live.manage.js') }}

{% endblock %}
