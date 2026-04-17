{% extends 'templates/layer.volt' %}

{% block content %}
    <form class="layui-form" method="POST" action="{{ url({'for':'home.exam_question.report','id':question.id}) }}">
        <div class="layui-form-item">
            <div class="layui-input-block" style="margin:0;">
                <textarea class="layui-textarea" name="remark" placeholder="请详细描述错误信息，挑错采纳后有积分奖励哦！" lay-verify="required"></textarea>
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-input-block center" style="margin:0;">
                <button class="layui-btn" lay-submit="true" lay-filter="go">提交</button>
                <button type="reset" class="layui-btn layui-btn-primary">重置</button>
                <input type="hidden" name="item_id" value="{{ question.id }}">
                <input type="hidden" name="item_type" value="200">
                <input type="hidden" name="reason" value="105">
            </div>
        </div>
    </form>
{% endblock %}