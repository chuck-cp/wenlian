<form class="layui-form kg-form" method="POST" action="{{ url({'for':'admin.setting.robot'}) }}">
    <div class="layui-form-item">
        <label class="layui-form-label">启用机器人</label>
        <div class="layui-input-block">
            <input type="radio" name="enabled" value="1" title="是" {% if wework_robot.enabled == "1" %}checked="checked"{% endif %}>
            <input type="radio" name="enabled" value="0" title="否" {% if wework_robot.enabled == "0" %}checked="checked"{% endif %}>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">Webhook</label>
        <div class="layui-input-block">
            <input class="layui-input" type="text" name="webhook_url" value="{{ wework_robot.webhook_url }}" lay-verify="required">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">技术手机号</label>
        <div class="layui-input-block">
            <input class="layui-input" type="text" name="ts_mobiles" placeholder="值班技术人员手机号，多个号码逗号分隔" value="{{ wework_robot.ts_mobiles }}">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">客服手机号</label>
        <div class="layui-input-block">
            <input class="layui-input" type="text" name="cs_mobiles" placeholder="值班客服人员手机号，多个号码逗号分隔" value="{{ wework_robot.cs_mobiles }}">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">财务手机号</label>
        <div class="layui-input-block">
            <input class="layui-input" type="text" name="fs_mobiles" placeholder="值班财务人员手机号，多个号码逗号分隔" value="{{ wework_robot.fs_mobiles }}">
        </div>
    </div>
    <div class="layui-form-item" style="margin-top:20px;">
        <label class="layui-form-label"></label>
        <div class="layui-input-block">
            <button class="layui-btn" lay-submit="true" lay-filter="go">提交</button>
            <button type="button" class="kg-back layui-btn layui-btn-primary">返回</button>
            <input type="hidden" name="section" value="wework.robot">
        </div>
    </div>
</form>

<form class="layui-form kg-form" method="POST" action="{{ url({'for':'admin.test.wework_robot'}) }}">
    <fieldset class="layui-elem-field layui-field-title">
        <legend>通知测试</legend>
    </fieldset>
    <div class="layui-form-item">
        <label class="layui-form-label"></label>
        <div class="layui-input-block">
            <button class="layui-btn" lay-submit="true" lay-filter="go">提交</button>
            <button type="button" class="kg-back layui-btn layui-btn-primary">返回</button>
        </div>
    </div>
</form>