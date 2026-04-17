<form class="layui-form kg-form" method="POST" action="{{ url({'for':'admin.setting.mobile'}) }}">
    <div class="layui-form-item">
        <label class="layui-form-label">运行状态</label>
        <div class="layui-input-block">
            <input type="radio" name="status" value="normal" title="正常" lay-filter="status" {% if mobile.status == 'normal' %}checked="checked"{% endif %}>
            <input type="radio" name="status" value="closed" title="关闭" lay-filter="status" {% if mobile.status == 'closed' %}checked="checked"{% endif %}>
        </div>
    </div>
    <div id="closed-tips-block" style="{{ closed_tips_display }}">
        <div class="layui-form-item">
            <label class="layui-form-label">关闭原因</label>
            <div class="layui-input-block">
                <input class="layui-input" type="text" name="closed_tips" value="{{ mobile.closed_tips }}">
            </div>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">公开访问</label>
        <div class="layui-input-block">
            <input type="radio" name="private" value="0" title="开启" {% if mobile.private == 0 %}checked="checked"{% endif %}>
            <input type="radio" name="private" value="1" title="关闭" {% if mobile.private == 1 %}checked="checked"{% endif %}>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label"></label>
        <div class="layui-input-block">
            <button class="layui-btn" lay-submit="true" lay-filter="go">提交</button>
            <button type="button" class="kg-back layui-btn layui-btn-primary">返回</button>
            <input type="hidden" name="tab" value="basic">
        </div>
    </div>
</form>
