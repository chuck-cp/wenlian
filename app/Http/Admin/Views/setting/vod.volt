{% extends 'templates/main.volt' %}

{% block content %}

    {% set storage_region_display = vod.storage_type == 'fixed' ? 'display:block' : 'display:none' %}
    {% set std_trans_display = vod.std_trans_enabled == 1 ? 'display:block' : 'display:none' %}
    {% set encrypt_trans_display = vod.encrypt_trans_enabled == 1 ? 'display:block': 'display:none' %}
    {% set wmk_tpl_display = vod.wmk_enabled == 1 ? 'display:block' : 'display:none' %}
    {% set key_anti_display = vod.key_anti_enabled == 1 ? 'display:block': 'display:none' %}
    {% set record_anti_display = vod.record_anti_enabled == 1 ? 'display:block': 'display:none' %}
    {% set record_anti_config = vod.record_anti_config|json_decode %}
    {% set video_quality = vod.video_quality|json_decode(true) %}

    <form class="layui-form kg-form" method="POST" action="{{ url({'for':'admin.setting.vod'}) }}">
        <fieldset class="layui-elem-field layui-field-title">
            <legend>存储配置</legend>
        </fieldset>
        <div class="layui-form-item">
            <label class="layui-form-label">子应用ID</label>
            <div class="layui-input-block">
                <input class="layui-input" type="text" name="sub_app_id" value="{{ vod.sub_app_id }}" placeholder="未开通子应用填写主应用ID" lay-verify="required">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">存储方式</label>
            <div class="layui-input-block">
                <input type="radio" name="storage_type" value="nearby" title="就近存储" lay-filter="storage_type" {% if vod.storage_type == "nearby" %}checked="checked"{% endif %}>
                <input type="radio" name="storage_type" value="fixed" title="固定区域" lay-filter="storage_type" {% if vod.storage_type == "fixed" %}checked="checked"{% endif %}>
            </div>
        </div>
        <div id="storage-region-block" style="{{ storage_region_display }}">
            <div class="layui-form-item">
                <label class="layui-form-label">所在区域</label>
                <div class="layui-input-block">
                    <input class="layui-input" type="text" name="storage_region" value="{{ vod.storage_region }}">
                </div>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">原始视频处理</label>
            <div class="layui-input-block">
                <input type="radio" name="keep_origin_media" value="1" title="转码后保留" {% if vod.keep_origin_media == "1" %}checked="checked"{% endif %}>
                <input type="radio" name="keep_origin_media" value="0" title="转码后删除" {% if vod.keep_origin_media == "0" %}checked="checked"{% endif %}>
            </div>
        </div>
        <fieldset class="layui-elem-field layui-field-title">
            <legend>主分发配置</legend>
        </fieldset>
        <div class="layui-form-item">
            <label class="layui-form-label">分发协议</label>
            <div class="layui-input-block">
                <input type="radio" name="protocol" value="https" title="HTTPS" {% if vod.protocol == "https" %}checked="checked"{% endif %}>
                <input type="radio" name="protocol" value="http" title="HTTP" {% if vod.protocol == "http" %}checked="checked"{% endif %}>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">分发域名</label>
            <div class="layui-input-block">
                <input class="layui-input" type="text" name="domain" value="{{ vod.domain }}" lay-verify="required">
            </div>
        </div>
        <fieldset class="layui-elem-field layui-field-title">
            <legend>标准转码</legend>
        </fieldset>
        <div class="layui-form-item">
            <label class="layui-form-label">开启转码</label>
            <div class="layui-input-block">
                <input type="radio" name="std_trans_enabled" value="1" title="是" lay-filter="std_trans_enabled" {% if vod.std_trans_enabled == 1 %}checked="checked"{% endif %}>
                <input type="radio" name="std_trans_enabled" value="0" title="否" lay-filter="std_trans_enabled" {% if vod.std_trans_enabled == 0 %}checked="checked"{% endif %}>
            </div>
        </div>
        <div id="standard-transcode-block" style="{{ std_trans_display }}">
            <div class="layui-form-item">
                <label class="layui-form-label">转码类型</label>
                <div class="layui-input-block">
                    <input type="radio" name="transcode_type" value="normal" title="普通转码" {% if vod.transcode_type == "normal" %}checked="checked"{% endif %}>
                    <input type="radio" name="transcode_type" value="tes" title="极速高清" {% if vod.transcode_type == "tes" %}checked="checked"{% endif %}>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">视频质量</label>
                <div class="layui-input-block">
                    <input type="checkbox" name="video_quality[]" value="hd" title="高清" {% if 'hd' in video_quality %}checked="checked"{% endif %}>
                    <input type="checkbox" name="video_quality[]" value="sd" title="标清" {% if 'sd' in video_quality %}checked="checked"{% endif %}>
                    <input type="checkbox" name="video_quality[]" value="fd" title="流畅" {% if 'fd' in video_quality %}checked="checked"{% endif %}>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">开启水印</label>
                <div class="layui-input-block">
                    <input type="radio" name="wmk_enabled" value="1" title="是" lay-filter="wmk_enabled" {% if vod.wmk_enabled == 1 %}checked="checked"{% endif %}>
                    <input type="radio" name="wmk_enabled" value="0" title="否" lay-filter="wmk_enabled" {% if vod.wmk_enabled == 0 %}checked="checked"{% endif %}>
                </div>
            </div>
            <div id="wmk-tpl-block" style="{{ wmk_tpl_display }}">
                <div class="layui-form-item">
                    <label class="layui-form-label">水印模板ID</label>
                    <div class="layui-input-block">
                        <input class="layui-input" type="text" name="wmk_tpl_id" value="{{ vod.wmk_tpl_id }}">
                    </div>
                </div>
            </div>
        </div>
        <fieldset class="layui-elem-field layui-field-title">
            <legend>加密转码</legend>
        </fieldset>
        <div class="layui-form-item">
            <label class="layui-form-label">开启加密</label>
            <div class="layui-input-block">
                <input type="radio" name="encrypt_trans_enabled" value="1" title="是" lay-filter="encrypt_trans_enabled" {% if vod.encrypt_trans_enabled == 1 %}checked="checked"{% endif %}>
                <input type="radio" name="encrypt_trans_enabled" value="0" title="否" lay-filter="encrypt_trans_enabled" {% if vod.encrypt_trans_enabled == 0 %}checked="checked"{% endif %}>
            </div>
        </div>
        <div id="encrypt-transcode-block" style="{{ encrypt_trans_display }}">
            <div class="layui-form-item">
                <label class="layui-form-label">播放密钥</label>
                <div class="layui-input-block">
                    <input class="layui-input" type="text" name="encrypt_play_key" value="{{ vod.encrypt_play_key }}">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">自适应码流模板ID</label>
                <div class="layui-input-block">
                    <input class="layui-input" type="text" name="encrypt_tpl_id" value="{{ vod.encrypt_tpl_id }}">
                </div>
            </div>
        </div>
        <fieldset class="layui-elem-field layui-field-title">
            <legend>Key防盗链</legend>
        </fieldset>
        <div class="layui-form-item">
            <label class="layui-form-label">开启防盗</label>
            <div class="layui-input-block">
                <input type="radio" name="key_anti_enabled" value="1" title="是" lay-filter="key_anti_enabled" {% if vod.key_anti_enabled == 1 %}checked="checked"{% endif %}>
                <input type="radio" name="key_anti_enabled" value="0" title="否" lay-filter="key_anti_enabled" {% if vod.key_anti_enabled == 0 %}checked="checked"{% endif %}>
            </div>
        </div>
        <div id="key-anti-block" style="{{ key_anti_display }}">
            <div class="layui-form-item">
                <label class="layui-form-label">防盗链Key</label>
                <div class="layui-input-block">
                    <input class="layui-input" type="text" name="key_anti_key" value="{{ vod.key_anti_key }}">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">有效时间（秒）</label>
                <div class="layui-input-block">
                    <input class="layui-input" type="text" name="key_anti_expiry" value="{{ vod.key_anti_expiry }}">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">IP限制数</label>
                <div class="layui-input-block">
                    <input class="layui-input" type="text" name="key_anti_ip_limit" value="{{ vod.key_anti_ip_limit }}">
                </div>
            </div>
        </div>
        <fieldset class="layui-elem-field layui-field-title">
            <legend>录屏防盗</legend>
        </fieldset>
        <div class="layui-form-item">
            <label class="layui-form-label">开启防盗</label>
            <div class="layui-input-block">
                <input type="radio" name="record_anti_enabled" value="1" title="是" lay-filter="record_anti_enabled" {% if vod.record_anti_enabled == 1 %}checked="checked"{% endif %}>
                <input type="radio" name="record_anti_enabled" value="0" title="否" lay-filter="record_anti_enabled" {% if vod.record_anti_enabled == 0 %}checked="checked"{% endif %}>
            </div>
        </div>
        <div id="record-anti-block" style="{{ record_anti_display }}">
            <div class="layui-form-item">
                <label class="layui-form-label">文字颜色</label>
                <div class="layui-input-inline">
                    <input id="rac-color-input" class="layui-input" type="text" name="record_anti_config[color]" value="{{ record_anti_config.color }}">
                </div>
                <div class="layui-inline">
                    <div id="rac-color-picker"></div>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">文字大小</label>
                <div class="layui-input-inline">
                    <select name="record_anti_config[size]">
                        <option value="">请选择</option>
                        {% for value in 12..24 %}
                            {% set selected = record_anti_config.size == value ? 'selected="selected"' : '' %}
                            <option value="{{ value }}" {{ selected }}>{{ value }}px</option>
                        {% endfor %}
                    </select>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">变换频率</label>
                <div class="layui-input-inline">
                    <select name="record_anti_config[interval]">
                        <option value="">请选择</option>
                        {% for value in 3..15 %}
                            {% set selected = record_anti_config.interval == value ? 'selected="selected"' : '' %}
                            <option value="{{ value }}" {{ selected }}>{{ value }}秒</option>
                        {% endfor %}
                    </select>
                </div>
            </div>
        </div>
        <fieldset class="layui-elem-field layui-field-title">
            <legend>播放设置</legend>
        </fieldset>
        <div class="layui-form-item">
            <label class="layui-form-label">允许快进</label>
            <div class="layui-input-block">
                <input type="radio" name="fast_forward_enabled" value="1" title="是" {% if vod.fast_forward_enabled == 1 %}checked="checked"{% endif %}>
                <input type="radio" name="fast_forward_enabled" value="0" title="否" {% if vod.fast_forward_enabled == 0 %}checked="checked"{% endif %}>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">真人验证</label>
            <div class="layui-input-inline">
                <input type="radio" name="human_verify_enabled" value="1" title="是" {% if vod.human_verify_enabled == 1 %}checked="checked"{% endif %}>
                <input type="radio" name="human_verify_enabled" value="0" title="否" {% if vod.human_verify_enabled == 0 %}checked="checked"{% endif %}>
            </div>
            <div class="layui-form-mid">
                <span class="layui-font-gray">播放过程中会随机暂停，验证通过继续播放，可以用来防刷学时。</span>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">切屏停播</label>
            <div class="layui-input-inline">
                <input type="radio" name="switch_anti_enabled" value="1" title="是" {% if vod.switch_anti_enabled == 1 %}checked="checked"{% endif %}>
                <input type="radio" name="switch_anti_enabled" value="0" title="否" {% if vod.switch_anti_enabled == 0 %}checked="checked"{% endif %}>
            </div>
            <div class="layui-form-mid">
                <span class="layui-font-gray">切换屏幕会暂停播放，暂停后需要手动复播，可以用来提升专注度。</span>
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

    <form class="layui-form kg-form" method="POST" action="{{ url({'for':'admin.test.vod'}) }}">
        <fieldset class="layui-elem-field layui-field-title">
            <legend>接口测试</legend>
        </fieldset>
        <div class="layui-form-item">
            <label class="layui-form-label">请求方法</label>
            <div class="layui-input-block">
                <input class="layui-input" type="text" name="file" value="DescribeTranscodeTemplates" readonly="readonly">
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

{% endblock %}

{% block inline_js %}

    <script>

        layui.use(['jquery', 'form', 'colorpicker'], function () {

            var $ = layui.jquery;
            var form = layui.form;
            var colorpicker = layui.colorpicker;

            colorpicker.render({
                elem: '#rac-color-picker',
                color: $('#rac-color-input').val(),
                predefine: true,
                done: function (color) {
                    $('#rac-color-input').val(color);
                }
            });

            form.on('radio(storage_type)', function (data) {
                var block = $('#storage-region-block');
                if (data.value === 'fixed') {
                    block.show();
                } else {
                    block.hide();
                }
            });

            form.on('radio(std_trans_enabled)', function (data) {
                var block = $('#standard-transcode-block');
                if (data.value === '1') {
                    block.show();
                } else {
                    block.hide();
                }
            });

            form.on('radio(encrypt_trans_enabled)', function (data) {
                var block = $('#encrypt-transcode-block');
                if (data.value === '1') {
                    block.show();
                } else {
                    block.hide();
                }
            });

            form.on('radio(wmk_enabled)', function (data) {
                var block = $('#wmk-tpl-block');
                if (data.value === '1') {
                    block.show();
                } else {
                    block.hide();
                }
            });

            form.on('radio(record_anti_enabled)', function (data) {
                var block = $('#record-anti-block');
                if (data.value === '1') {
                    block.show();
                } else {
                    block.hide();
                }
            });

            form.on('radio(key_anti_enabled)', function (data) {
                var block = $('#key-anti-block');
                if (data.value === '1') {
                    block.show();
                } else {
                    block.hide();
                }
            });

        });

    </script>

{% endblock %}
