{% set point_enabled = setting('point','enabled') %}

<div class="layui-card">
    <div class="layui-card-header">用户服务</div>
    <div class="layui-card-body">
        <ul class="my-menu">
            <!-- <li><a href="{{ url({'for':'home.uc.orders'}) }}">我的订单</a></li> -->
            <!-- <li><a href="{{ url({'for':'home.uc.refunds'}) }}">我的退款</a></li> -->
            <!-- <li><a href="{{ url({'for':'home.uc.invoice'}) }}">我的开票</a></li> -->
            <!-- <li><a href="{{ url({'for':'home.uc.reviews'}) }}">我的评价</a></li> -->
            <!-- <li><a href="{{ url({'for':'home.uc.consults'}) }}">我的咨询</a></li> -->
            <li><a href="{{ url({'for':'home.uc.certificates'}) }}">我的证书</a></li>
            <!-- <li><a href="{{ url({'for':'home.uc.coupons'}) }}">电子优惠券</a></li> -->
            <!-- <li><a href="{{ url({'for':'home.uc.digital_redeems'}) }}">电子兑换卡</a></li> -->
        </ul>
    </div>
</div>

<!-- {% if point_enabled == 1 %}
    <div class="layui-card">
        <div class="layui-card-header">积分兑换</div>
        <div class="layui-card-body">
            <ul class="my-menu">
                <li><a href="{{ url({'for':'home.point_gift.list'}) }}">积分商城</a></li>
                <li><a href="{{ url({'for':'home.uc.point_gift_redeems'}) }}">兑换记录</a></li>
                <li><a href="{{ url({'for':'home.uc.point_history'}) }}">积分记录</a></li>
            </ul>
        </div>
    </div>
{% endif %} -->

<!-- <div class="layui-card">
    <div class="layui-card-header">分销推广</div>
    <div class="layui-card-body">
        <ul class="my-menu">
            <li><a href="{{ url({'for':'home.distribution.list'}) }}">分销市场</a></li>
            <li><a href="{{ url({'for':'home.uc.affiliate'},{'action':'index'}) }}">我的分销</a></li>
            <li><a href="{{ url({'for':'home.uc.withdraw'},{'action':'list'}) }}">提现记录</a></li>
            <li><a href="{{ url({'for':'home.uc.withdraw'},{'action':'account.list'}) }}">提现账户</a></li>
        </ul>
    </div>
</div> -->

<div class="layui-card">
    <div class="layui-card-header">个人设置</div>
    <div class="layui-card-body">
        <ul class="my-menu">
            <li><a href="{{ url({'for':'home.uc.profile'}) }}">个人信息</a></li>
            <li><a href="{{ url({'for':'home.uc.contact'}) }}">收货地址</a></li>
            <li><a href="{{ url({'for':'home.uc.account'}) }}">帐号安全</a></li>
        </ul>
    </div>
</div>