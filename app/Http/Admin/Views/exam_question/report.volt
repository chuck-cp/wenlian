{% extends 'templates/main.volt' %}

{% block content %}

    {% set edit_url = url({'for':'admin.exam_question.edit','id':question.id}) %}
    {% set report_url = url({'for':'admin.exam_question.report','id':question.id}) %}

    <fieldset class="layui-elem-field layui-field-title">
        <legend>审核挑错</legend>
    </fieldset>

    <div class="layui-tabs">
        <ul class="layui-tabs-header">
            <li class="layui-this">试题信息</li>
            <li>挑错信息</li>
            <li>审核意见</li>
        </ul>
        <div class="layui-tabs-body">
            <div class="layui-tabs-item layui-show">
                <div class="kg-mod-preview">
                    <iframe src="{{ edit_url }}" style="width:1300px;height:600px;"></iframe>
                </div>
            </div>
            <div class="layui-tabs-item">
                <table class="layui-table kg-table">
                    <colgroup>
                        <col>
                        <col>
                        <col>
                    </colgroup>
                    <thead>
                    <tr>
                        <th>报告用户</th>
                        <th>错误描述</th>
                        <th>报告时间</th>
                    </tr>
                    </thead>
                    <tbody>
                    {% for item in reports %}
                        {% set owner_url = url({'for':'home.user.show','id':item.owner.id}) %}
                        <tr>
                            <td><a href="{{ owner_url }}" target="_blank">{{ item.owner.name }}</a></td>
                            <td>{{ item.reason }}</td>
                            <td>{{ date('Y-m-d H:i:s',item.create_time) }}</td>
                        </tr>
                    {% endfor %}
                    </tbody>
                </table>
            </div>
            <div class="layui-tabs-item">
                <form class="layui-form kg-form kg-mod-form" method="POST" action="{{ report_url }}">
                    <div class="layui-form-item">
                        <label class="layui-form-label">有效挑错</label>
                        <div class="layui-input-block">
                            <input type="radio" name="accepted" value="1" title="是" lay-filter="accepted">
                            <input type="radio" name="accepted" value="0" title="否" lay-filter="accepted">
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
