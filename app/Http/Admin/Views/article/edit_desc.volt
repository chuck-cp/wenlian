<form class="layui-form kg-form" method="POST" action="{{ update_url }}">
    <div class="layui-form-item">
        {% if article.format == 'html' %}
            <div class="layui-input-block" style="margin:0;">
                <textarea name="content" class="layui-hide" id="editor-textarea">{{ article.content }}</textarea>
            </div>
        {% elseif article.format == 'markdown' %}
            <div class="layui-input-block" style="margin:0;">
                <div id="vditor"></div>
                <textarea name="content" class="layui-hide" id="vditor-textarea">{{ article.markdown }}</textarea>
            </div>
        {% endif %}
    </div>
    <div class="layui-form-item">
        <div class="layui-input-block kg-center" style="margin:0;">
            <button class="kg-submit layui-btn" lay-submit="true" lay-filter="go">提交</button>
            <button type="button" class="kg-back layui-btn layui-btn-primary">返回</button>
        </div>
    </div>
</form>