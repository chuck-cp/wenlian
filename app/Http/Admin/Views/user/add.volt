{% extends 'templates/main.volt' %}

{% block content %}

    <fieldset class="layui-elem-field layui-field-title">
        <legend>添加用户</legend>
    </fieldset>

    <div class="layui-tabs">
        <ul class="layui-tabs-header">
            <li class="layui-this">常规添加</li>
            <li>批量导入</li>
        </ul>
        <div class="layui-tabs-body">
            <div class="layui-tabs-item layui-show">
                <form class="layui-form kg-form" method="POST" action="{{ url({'for':'admin.user.create'}) }}">
                    <div class="layui-form-item">
                        <label class="layui-form-label">账号</label>
                        <div class="layui-input-block">
                            <input class="layui-input" type="text" name="account" placeholder="手机 / 邮箱" lay-verify="required">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">密码</label>
                        <div class="layui-input-block">
                            <input class="layui-input" type="text" name="password" lay-verify="required">
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
                <form class="layui-form kg-form" method="POST" action="{{ url({'for':'admin.user.import'}) }}">
                    <div class="layui-form-item">
                        <label class="layui-form-label">模板下载</label>
                        <div class="layui-input-block">
                            <div class="layui-form-mid layui-word-aux">
                                通过模板批量导入用户，减少繁琐的添加操作。<a href="/static/admin/tpl/用户批量导入模板.xlsx" target="_blank">（下载用户批量导入模板）</a>
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

        layui.use(['jquery', 'form', 'upload', 'laydate'], function () {

            var $ = layui.jquery;
            var form = layui.form;
            var upload = layui.upload;
            var laydate = layui.laydate;

            laydate.render({
                elem: 'input[name=vip_expiry_time]',
                type: 'datetime'
            });

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

            form.on('radio(vip)', function (data) {
                var block = $('#vip-expiry-block');
                if (data.value === '1') {
                    block.show();
                } else {
                    block.hide();
                }
            });

        });

    </script>

{% endblock %}
