<form class="layui-form kg-form" method="POST" action="{{ url({'for':'admin.chapter.content','id':chapter.id}) }}">
    <div class="layui-form-item">
        <label class="layui-form-label">文档</label>
        <div class="layui-inline" style="width:40%;">
            <input class="layui-input" type="text" name="upload_path" value="{{ upload.path }}" placeholder="上传文件上限100M" readonly="readonly" lay-verify="required">
        </div>
        <div class="layui-inline">
            <button class="layui-btn" type="button" id="upload-doc">上传</button>
            <input type="hidden" name="upload_id" value="{{ doc.upload_id }}">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">评论</label>
        <div class="layui-input-block" style="margin:0;">
            <input type="radio" name="settings[comment_enabled]" value="1" title="开启" {% if doc.settings['comment_enabled'] == 1 %}checked="checked"{% endif %}>
            <input type="radio" name="settings[comment_enabled]" value="0" title="关闭" {% if doc.settings['comment_enabled'] == 0 %}checked="checked"{% endif %}>
        </div>
    </div>
    <div class="layui-form-item">
        <div class="layui-input-block">
            <button class="layui-btn" lay-submit="true" lay-filter="go">提交</button>
            <button type="button" class="kg-back layui-btn layui-btn-primary">返回</button>
        </div>
    </div>
</form>
