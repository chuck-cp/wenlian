{% if course.me.logged == 0 %}
    {% set login_url = url({'for':'home.account.login'}) %}
    <div class="sidebar wrap">
        <a class="layui-btn layui-btn-fluid" href="{{ login_url }}">用户登录</a>
    </div>
{% elseif course.me.allow_study == 1 %}
    {% set study_url = url({'for':'home.course.study','id':course.id}) %}
    <div class="sidebar wrap">
        <button class="layui-btn layui-btn-fluid layui-btn-normal btn-study" data-url="{{ study_url }}">开始学习</button>
    </div>
{% elseif course.me.allow_order == 1 %}
    {% set order_url = url({'for':'home.order.confirm'},{'item_id':course.id,'item_type':1}) %}
    <div class="sidebar wrap">
        <button class="layui-btn layui-btn-fluid layui-btn-danger btn-buy" data-url="{{ order_url }}">立即报名</button>
    </div>
{% endif %}
