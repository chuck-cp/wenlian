<form class="layui-form account-form" method="POST" action="{{ url({'for':'home.wechat_oa.bind_register'}) }}">
    <div class="layui-form-item">
        <div class="layui-input-wrap">
            <div class="layui-input-prefix"><i class="layui-icon layui-icon-username"></i></div>
            <input id="cv-account" class="layui-input" type="text" name="account" autocomplete="off" placeholder="手机 / 邮箱" lay-verify="required">
        </div>
    </div>
    <div class="layui-form-item">
        <div class="layui-input-wrap">
            <div class="layui-input-prefix"><i class="layui-icon layui-icon-password"></i></div>
            <input class="layui-input" type="password" name="password" autocomplete="off" placeholder="密码（字母数字特殊字符6-16位）" lay-verify="required">
        </div>
    </div>
    <div class="layui-form-item">
        <div class="layui-input-inline verify-input-inline">
            <div class="layui-input-wrap">
                <div class="layui-input-prefix"><i class="layui-icon layui-icon-vercode"></i></div>
                <input class="layui-input" type="text" name="verify_code" placeholder="验证码" lay-verify="required">
            </div>
        </div>
        <div class="layui-input-inline verify-btn-inline">
            <button id="cv-emit-btn" class="layui-btn layui-btn-primary layui-btn-disabled" type="button" disabled="disabled">获取验证码</button>
        </div>
    </div>
    <div class="layui-form-item">
        <div class="layui-input-block">
            <div class="agree">
                <div class="left"><input id="register-agree" type="checkbox" name="agree" checked="checked" lay-skin="primary"></div>
                <div class="right">我已阅读并同意<a href="{{ terms_url }}" target="_blank">《用户协议》</a>和<a href="{{ privacy_url }}" target="_blank">《隐私政策》</a></div>
            </div>
        </div>
    </div>
    <div class="layui-form-item">
        <div class="layui-input-block">
            <button id="cv-submit-btn" class="layui-btn layui-btn-fluid layui-btn-disabled" disabled="disabled" lay-submit="true" lay-filter="go">注册并绑定帐号</button>
            <input type="hidden" name="ticket" value="{{ request.get('ticket') }}">
            <input type="hidden" name="return_url" value="{{ request.get('return_url') }}">
            <input id="cv-captcha-ticket" type="hidden" name="captcha[ticket]">
            <input id="cv-captcha-rand" type="hidden" name="captcha[rand]">
        </div>
    </div>
</form>
