<form class="layui-form kg-form" method="POST" action="{{ action_url }}">
    <div class="layui-form-item">
        <label class="layui-form-label">封面</label>
        <div class="layui-input-inline">
            <img id="img-cover" class="kg-cover" src="{{ package.cover }}">
            <input type="hidden" name="cover" value="{{ package.cover }}">
        </div>
        <div class="layui-input-inline" style="padding-top:35px;">
            <button id="change-cover" class="layui-btn layui-btn-sm" type="button">更换</button>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">标题</label>
        <div class="layui-input-block">
            <input class="layui-input" type="text" name="title" value="{{ package.title }}" lay-verify="required">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">简介</label>
        <div class="layui-input-block">
            <textarea class="layui-textarea" name="summary">{{ package.summary }}</textarea>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label"></label>
        <div class="layui-input-block">
            <button class="layui-btn" lay-submit="true" lay-filter="go">提交</button>
            <button type="button" class="kg-back layui-btn layui-btn-primary">返回</button>
        </div>
    </div>
</form>
