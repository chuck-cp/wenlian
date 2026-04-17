<form class="layui-form kg-form" method="POST" action="{{ url({'for':'admin.setting.oauth'}) }}">
    <div class="layui-form-item">
        <label class="layui-form-label">开启手机注册</label>
        <div class="layui-input-block">
            <input type="radio" name="register_with_phone" value="1" title="是" {% if local_auth.register_with_phone == "1" %}checked="checked"{% endif %}>
            <input type="radio" name="register_with_phone" value="0" title="否" {% if local_auth.register_with_phone == "0" %}checked="checked"{% endif %}>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">开启邮箱注册</label>
        <div class="layui-input-block">
            <input type="radio" name="register_with_email" value="1" title="是" {% if local_auth.register_with_email == "1" %}checked="checked"{% endif %}>
            <input type="radio" name="register_with_email" value="0" title="否" {% if local_auth.register_with_email == "0" %}checked="checked"{% endif %}>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">允许登录失败次数</label>
        <div class="layui-input-block">
            <input class="layui-input" type="text" name="failed_login_limit" value="{{ local_auth.failed_login_limit }}" lay-verify="number">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">登录失败锁定秒数</label>
        <div class="layui-input-block">
            <input class="layui-input" type="text" name="failed_login_lock" value="{{ local_auth.failed_login_lock }}" lay-verify="number">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">限制设备同时在线</label>
        <div class="layui-input-block">
            <input type="radio" name="mutex_login" value="1" title="是" {% if local_auth.mutex_login == "1" %}checked="checked"{% endif %}>
            <input type="radio" name="mutex_login" value="0" title="否" {% if local_auth.mutex_login == "0" %}checked="checked"{% endif %}>
        </div>
    </div>
    <div class="layui-form-item" style="margin-bottom:20px;">
        <label class="layui-form-label">同时在线设备数</label>
        <div class="layui-input-block">
            <input class="layui-input" type="text" name="mutex_client_limit" value="{{ local_auth.mutex_client_limit }}" lay-verify="number">
        </div>
    </div>
    <div class="layui-form-item" style="margin-bottom:20px;">
        <label class="layui-form-label">用户协议</label>
        <div class="layui-input-block">
            <a class="layui-btn layui-btn-normal" href="{{ url({'for':'admin.page.edit','id':'terms'}) }}">设置</a>
        </div>
    </div>
    <div class="layui-form-item" style="margin-bottom:20px;">
        <label class="layui-form-label">隐私政策</label>
        <div class="layui-input-block">
            <a class="layui-btn layui-btn-normal" href="{{ url({'for':'admin.page.edit','id':'privacy'}) }}">设置</a>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label"></label>
        <div class="layui-input-block">
            <button class="layui-btn" lay-submit="true" lay-filter="go">提交</button>
            <button type="button" class="kg-back layui-btn layui-btn-primary">返回</button>
            <input type="hidden" name="section" value="oauth.local">
        </div>
    </div>
</form>