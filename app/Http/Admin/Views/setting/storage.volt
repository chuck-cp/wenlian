{% extends 'templates/main.volt' %}

{% block content %}

    {% set doc_wmk = cos.doc_wmk|json_decode %}

    <div class="layui-tabs">
        <ul class="layui-tabs-header">
            <li class="layui-this">基本配置</li>
            <li>文档设置</li>
            <li>图片样式</li>
            <li>默认图片</li>
        </ul>
        <div class="layui-tabs-body">
            <div class="layui-tabs-item layui-show">
                <form class="layui-form kg-form" method="POST" action="{{ url({'for':'admin.setting.storage'}) }}">
                    <fieldset class="layui-elem-field layui-field-title">
                        <legend>存储桶配置</legend>
                    </fieldset>
                    <div class="layui-form-item">
                        <label class="layui-form-label">空间名称</label>
                        <div class="layui-input-block">
                            <input class="layui-input" type="text" name="bucket" value="{{ cos.bucket }}" lay-verify="required">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">所在区域</label>
                        <div class="layui-input-block">
                            <input class="layui-input" type="text" name="region" value="{{ cos.region }}" lay-verify="required">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">访问协议</label>
                        <div class="layui-input-block">
                            <input type="radio" name="protocol" value="http" title="HTTP" {% if cos.protocol == "http" %}checked="checked"{% endif %}>
                            <input type="radio" name="protocol" value="https" title="HTTPS" {% if cos.protocol == "https" %}checked="checked"{% endif %}>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">访问域名</label>
                        <div class="layui-input-block">
                            <input class="layui-input" type="text" name="domain" value="{{ cos.domain }}" lay-verify="required">
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
                <form class="layui-form kg-form" method="POST" action="{{ url({'for':'admin.test.storage'}) }}">
                    <fieldset class="layui-elem-field layui-field-title">
                        <legend>上传测试</legend>
                    </fieldset>
                    <div class="layui-form-item">
                        <label class="layui-form-label">测试文件</label>
                        <div class="layui-input-block">
                            <input class="layui-input" type="text" name="file" value="hello_world.txt" readonly="readonly">
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
            </div>
            <div class="layui-tabs-item">
                <form class="layui-form kg-form" method="POST" action="{{ url({'for':'admin.setting.storage'}) }}">
                    <div class="layui-form-item">
                        <label class="layui-form-label">允许复制</label>
                        <div class="layui-input-block">
                            <input type="radio" name="doc_copy_enabled" value="1" title="是" {% if cos.doc_copy_enabled == 1 %}checked="checked"{% endif %}>
                            <input type="radio" name="doc_copy_enabled" value="0" title="否" {% if cos.doc_copy_enabled == 0 %}checked="checked"{% endif %}>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">开启水印</label>
                        <div class="layui-input-block">
                            <input type="radio" name="doc_wmk[enabled]" value="1" title="是" {% if doc_wmk.enabled == 1 %}checked="checked"{% endif %}>
                            <input type="radio" name="doc_wmk[enabled]" value="0" title="否" {% if doc_wmk.enabled == 0 %}checked="checked"{% endif %}>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">水印文字</label>
                        <div class="layui-input-block">
                            <input class="layui-input" type="text" name="doc_wmk[text]" value="{{ doc_wmk.text }}" lay-verify="required">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">文字大小</label>
                        <div class="layui-input-inline">
                            <select name="doc_wmk[size]" lay-verify="required">
                                <option value="">请选择</option>
                                {% for value in 12..36 %}
                                    {% set selected = doc_wmk.size == value ? 'selected="selected"' : '' %}
                                    <option value="{{ value }}" {{ selected }}>{{ value }}px</option>
                                {% endfor %}
                            </select>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">水印颜色</label>
                        <div class="layui-input-inline">
                            <input id="doc-wmk-color-input" class="layui-input" type="text" name="doc_wmk[color]" value="{{ doc_wmk.color }}" lay-verify="required">
                        </div>
                        <div class="layui-inline">
                            <div id="doc-wmk-color-picker"></div>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">旋转角度</label>
                        <div class="layui-input-block">
                            <div id="doc-wmk-rotate-slider" style="padding-top:15px;"></div>
                            <input id="doc-wmk-rotate-input" type="hidden" name="doc_wmk[rotate]" value="{{ doc_wmk.rotate }}">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">水平间距</label>
                        <div class="layui-input-block">
                            <div id="doc-wmk-horizontal-slider" style="padding-top:15px;"></div>
                            <input id="doc-wmk-horizontal-input" type="hidden" name="doc_wmk[horizontal]" value="{{ doc_wmk.horizontal }}">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">垂直间距</label>
                        <div class="layui-input-block">
                            <div id="doc-wmk-vertical-slider" style="padding-top:15px;"></div>
                            <input id="doc-wmk-vertical-input" type="hidden" name="doc_wmk[vertical]" value="{{ doc_wmk.vertical }}">
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
            </div>
            <div class="layui-tabs-item">
                <table class="layui-table" lay-size="lg" style="width:80%;">
                    <colgroup>
                        <col>
                        <col>
                    </colgroup>
                    <thead>
                    <tr>
                        <th>样式名称</th>
                        <th>样式描述</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>avatar_160</td>
                        <td>imageMogr2/thumbnail/160x/interlace/0</td>
                    </tr>
                    <tr>
                        <td>cover_270</td>
                        <td>imageMogr2/thumbnail/270x/interlace/0</td>
                    </tr>
                    <tr>
                        <td>content_800</td>
                        <td>imageMogr2/thumbnail/800x/interlace/0</td>
                    </tr>
                    <tr>
                        <td>slide_1100</td>
                        <td>imageMogr2/thumbnail/1100x/interlace/0</td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="layui-tabs-item">
                <form class="layui-form kg-form" method="POST" action="{{ url({'for':'admin.upload.default_img'}) }}">
                    <div class="layui-form-item">
                        <table class="layui-table" lay-size="lg" style="width:80%;">
                            <colgroup>
                                <col>
                                <col>
                            </colgroup>
                            <thead>
                            <tr>
                                <th>文件名称</th>
                                <th>文件位置</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>用户头像</td>
                                <td>public/static/admin/img/default/user_cover.png</td>
                            </tr>
                            <tr>
                                <td>分类图标</td>
                                <td>public/static/admin/img/default/category_icon.png</td>
                            </tr>
                            <tr>
                                <td>课程封面</td>
                                <td>public/static/admin/img/default/course_cover.png</td>
                            </tr>
                            <tr>
                                <td>套餐封面</td>
                                <td>public/static/admin/img/default/package_cover.png</td>
                            </tr>
                            <tr>
                                <td>专题封面</td>
                                <td>public/static/admin/img/default/topic_cover.png</td>
                            </tr>
                            <tr>
                                <td>会员封面</td>
                                <td>public/static/admin/img/default/vip_cover.png</td>
                            </tr>
                            <tr>
                                <td>礼品封面</td>
                                <td>public/static/admin/img/default/gift_cover.png</td>
                            </tr>
                            <tr>
                                <td>轮播封面</td>
                                <td>public/static/admin/img/default/slide_cover.png</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label"></label>
                        <div class="layui-input-block">
                            <button class="layui-btn" lay-submit="true" lay-filter="go">上传</button>
                            <button type="button" class="kg-back layui-btn layui-btn-primary">返回</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

{% endblock %}

{% block inline_js %}

    <script>

        layui.use(['jquery', 'slider', 'colorpicker'], function () {

            var $ = layui.jquery;
            var slider = layui.slider;
            var colorpicker = layui.colorpicker;

            colorpicker.render({
                elem: '#doc-wmk-color-picker',
                predefine: true,
                format: 'rgb',
                alpha: true,
                color: $('#doc-wmk-color-input').val(),
                done: function (color) {
                    $('#doc-wmk-color-input').val(color);
                }
            });

            slider.render({
                elem: '#doc-wmk-rotate-slider',
                min: 0,
                max: 360,
                step: 10,
                value: $('#doc-wmk-rotate-input').val(),
                done: function (value) {
                    $('#doc-wmk-rotate-input').val(value);
                }
            });

            slider.render({
                elem: '#doc-wmk-horizontal-slider',
                min: 0,
                max: 200,
                step: 10,
                value: $('#doc-wmk-horizontal-input').val(),
                done: function (value) {
                    $('#doc-wmk-horizontal-input').val(value);
                }
            });

            slider.render({
                elem: '#doc-wmk-vertical-slider',
                min: 0,
                max: 300,
                step: 10,
                value: $('#doc-wmk-vertical-input').val(),
                done: function (value) {
                    $('#doc-wmk-vertical-input').val(value);
                }
            });

        });

    </script>

{% endblock %}
