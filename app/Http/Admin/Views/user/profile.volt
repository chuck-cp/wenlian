{% extends 'templates/main.volt' %}

{% block content %}

    {% set update_url = url({'for':'admin.user.profile','id':user.id}) %}

    <form class="layui-form kg-form" method="POST" action="{{ update_url }}">
        <div class="layui-form-item">
            <div class="layui-input-block" style="margin:0;">
                <textarea name="profile" class="layui-hide" id="editor-textarea">{{ user.profile }}</textarea>
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-input-block kg-center" style="margin:0;">
                <button class="kg-submit layui-btn" lay-submit="true" lay-filter="go">提交</button>
                <button type="button" class="kg-close layui-btn layui-btn-primary">关闭</button>
            </div>
        </div>
    </form>

{% endblock %}

{% block include_js %}

    {{ js_include('lib/kindeditor/kindeditor.min.js') }}
    {{ js_include('admin/js/content.editor.js') }}

{% endblock %}

{% block inline_js %}

    <script>
        layui.use(['jquery'], function () {
            var $ = layui.jquery;
            $('.kg-close').on('click', function () {
                parent.layer.closeAll();
            });
        });
    </script>

{% endblock %}
