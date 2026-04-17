{% extends 'templates/main.volt' %}

{% block content %}

    <fieldset class="layui-elem-field layui-field-title">
        <legend>添加试题</legend>
    </fieldset>

    <div class="layui-tabs">
        <ul class="layui-tabs-header">
            <li class="layui-this">常规添加</li>
            <li>模板导入</li>
        </ul>
        <div class="layui-tabs-body">
            <div class="layui-tabs-item layui-show">
                <form class="layui-form kg-form" method="POST" action="{{ url({'for':'admin.exam_question.create'}) }}">
                    <div class="layui-form-item">
                        <label class="layui-form-label">题型</label>
                        <div class="layui-input-block">
                            {% for value,title in model_types %}
                                {% set checked = value == 1 ? 'checked="checked"' : '' %}
                                <input type="radio" name="model" value="{{ value }}" title="{{ title }}" {{ checked }}>
                            {% endfor %}
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
                <form class="layui-form kg-form" method="POST" action="{{ url({'for':'admin.exam_question.import'}) }}">
                    <div class="layui-form-item">
                        <label class="layui-form-label"></label>
                        <div class="layui-input-block">
                            <div class="layui-form-mid layui-word-aux">
                                通过模板批量导入试题，减少繁琐的试题添加操作。<a href="/static/admin/tpl/试题批量导入模板.xlsx" target="_blank">（下载试题批量导入模板）</a>
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
            </div>
        </div>
    </div>

{% endblock %}

{% block inline_js %}

    <script>

        layui.use(['jquery', 'layer', 'upload'], function () {

            var $ = layui.jquery;
            var layer = layui.layer;
            var upload = layui.upload;

            upload.render({
                elem: '#upload-file',
                url: '/admin/upload/tmp/file',
                accept: 'file',
                exts: 'xls|xlsx',
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
