{% extends 'templates/main.volt' %}

{% block content %}

    {% set create_url = url({'for':'admin.exam_paper.create_user','id':paper.id}) %}
    {% set import_url = url({'for':'admin.exam_paper.import_user','id':paper.id}) %}

    <fieldset class="layui-elem-field layui-field-title">
        <legend>添加学员</legend>
    </fieldset>

    <div class="layui-tabs">
        <ul class="layui-tabs-header">
            <li class="layui-this">常规添加</li>
            <li>批量导入</li>
        </ul>
        <div class="layui-tabs-body">
            <div class="layui-tabs-item layui-show">
                <form class="layui-form kg-form" method="POST" action="{{ create_url }}">
                    <div class="layui-form-item">
                        <label class="layui-form-label">试卷名称</label>
                        <div class="layui-input-block">
                            <div class="layui-form-mid">{{ paper.title }}</div>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">用户帐号</label>
                        <div class="layui-input-block">
                            <input class="layui-input" type="text" name="user_id" placeholder="用户编号 / 手机号码 / 邮箱地址" lay-verify="required">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">过期时间</label>
                        <div class="layui-input-block">
                            <input class="layui-input" type="text" name="expiry_time" autocomplete="off" lay-verify="required">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label"></label>
                        <div class="layui-input-block">
                            <button class="layui-btn" lay-submit="true" lay-filter="go">提交</button>
                            <button type="button" class="kg-back layui-btn layui-btn-primary">返回</button>
                            <input type="hidden" name="paper_id" value="{{ paper.id }}">
                        </div>
                    </div>
                </form>
            </div>
            <div class="layui-tabs-item">
                <form class="layui-form kg-form" method="POST" action="{{ import_url }}">
                    <div class="layui-form-item">
                        <label class="layui-form-label">试卷名称</label>
                        <div class="layui-input-block">
                            <div class="layui-form-mid">{{ paper.title }}</div>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">模板下载</label>
                        <div class="layui-input-block">
                            <div class="layui-form-mid layui-word-aux">
                                通过模板批量导入参考学员，减少繁琐的添加操作。<a href="/static/admin/tpl/考试学员批量导入模板.xlsx" target="_blank">（下载考试学员批量导入模板）</a>
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

        layui.use(['jquery', 'upload', 'laydate'], function () {

            var $ = layui.jquery;
            var upload = layui.upload;
            var laydate = layui.laydate;

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

            laydate.render({
                elem: 'input[name=expiry_time]',
                type: 'datetime'
            });

        });

    </script>

{% endblock %}
