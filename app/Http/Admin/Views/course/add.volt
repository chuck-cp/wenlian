{% extends 'templates/main.volt' %}

{% block content %}

    <fieldset class="layui-elem-field layui-field-title">
        <legend>添加课程</legend>
    </fieldset>

    <div class="layui-tabs">
        <ul class="layui-tabs-header">
            <li class="layui-this">常规添加</li>
            <li>模板导入</li>
        </ul>
        <div class="layui-tabs-body">
            <div class="layui-tabs-item layui-show">
                <form class="layui-form kg-form" method="POST" action="{{ url({'for':'admin.course.create'}) }}">
                    <div class="layui-form-item">
                        <label class="layui-form-label">类型</label>
                        <div class="layui-input-block">
                            {% for value,title in model_types %}
                                {% set checked = value == 1 ? 'checked="checked"' : '' %}
                                <input type="radio" name="model" value="{{ value }}" title="{{ title }}" {{ checked }} lay-filter="model">
                            {% endfor %}
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label"></label>
                        <div class="layui-input-block">
                            <div class="layui-form-mid layui-word-aux" id="model-tips"></div>
                        </div>
                    </div>
                    <div class="layui-form-item" style="margin:25px 0 35px 0;">
                        <label class="layui-form-label">标题</label>
                        <div class="layui-input-block">
                            <input class="layui-input" type="text" name="title" lay-verify="required">
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
                <form class="layui-form kg-form" method="POST" action="{{ url({'for':'admin.course.import'}) }}">
                    <div class="layui-form-item">
                        <label class="layui-form-label"></label>
                        <div class="layui-input-block">
                            <div class="layui-form-mid layui-word-aux">
                                通过模板导入课程目录，减少繁琐的课目添加操作。<a href="/static/admin/tpl/课程目录导入模板.md" target="_blank">（下载课目导入模板）</a>
                            </div>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">模板文件</label>
                        <div class="layui-inline" style="width:30%;">
                            <input class="layui-input" type="text" name="path" lay-verify="required">
                        </div>
                        <div class="layui-inline">
                            <button class="layui-btn" type="button" id="upload-file">上传</button>
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
                <fieldset class="layui-elem-field layui-field-title">
                    <legend>标记说明</legend>
                </fieldset>
                <table class="layui-table kg-table" lay-size="lg">
                    <thead>
                    <tr>
                        <th>属性</th>
                        <th>标记</th>
                        <th>说明</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>课程 / 课时类型</td>
                        <td>&lt;model&gt;</td>
                        <td>model 取值：点播，直播，图文，文档，面授</td>
                    </tr>
                    <tr>
                        <td>课程标题</td>
                        <td>##</td>
                        <td>标记后带空格</td>
                    </tr>
                    <tr>
                        <td>章标题</td>
                        <td>###</td>
                        <td>标记后带空格</td>
                    </tr>
                    <tr>
                        <td>节标题</td>
                        <td>*</td>
                        <td>标记后带空格</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

{% endblock %}

{% block inline_js %}

    <script>

        layui.use(['jquery', 'form', 'layer', 'upload'], function () {

            var $ = layui.jquery;
            var form = layui.form;
            var layer = layui.layer;
            var upload = layui.upload;

            var modelTips = {
                '1': '通过音视频呈现课程内容，内容可视化，有图像有声音，适合大部分场景',
                '2': '通过直播呈现课程内容，交互性强，适合需要交互反馈、情绪表达的场景',
                '3': '通过图文呈现课程内容，简单直接，适合撰写文档、书籍、教程的场景',
                '4': '面对面讲授课程内容，传统教学，适合有条件开展线下教学的场景',
                '5': '通过文档呈现课程内容，在线预览 WORD，PPT，PDF 等文档内容',
            };

            var modelTipsBlock = $('#model-tips');

            form.on('radio(model)', function (data) {
                modelTipsBlock.html(modelTips[data.value]);
            });

            modelTipsBlock.html(modelTips['1']);

            upload.render({
                elem: '#upload-file',
                url: '/admin/upload/tmp/file',
                accept: 'file',
                acceptMime: 'text/*',
                before: function () {
                    layer.load();
                },
                done: function (res, index, upload) {
                    $('input[name=path]').val(res.file.path);
                    layer.closeAll('loading');
                },
                error: function (index, upload) {
                    layer.msg('上传文件失败', {icon: 2});
                }
            });

        });

    </script>

{% endblock %}
