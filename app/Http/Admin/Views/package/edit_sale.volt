<form class="layui-form kg-form" method="POST" action="{{ action_url }}">
    <!-- <div class="layui-form-item">
        <div class="layui-inline">
            <label class="layui-form-label">市场价格</label>
            <div class="layui-input-inline">
                <input class="layui-input" type="text" name="market_price" value="{{ package.market_price }}" lay-verify="number">
            </div>
            <div class="layui-form-mid layui-word-aux">元</div>
        </div>
    </div>
    <div class="layui-form-item">
        <div class="layui-inline">
            <label class="layui-form-label">会员价格</label>
            <div class="layui-input-inline">
                <input class="layui-input" type="text" name="vip_price" value="{{ package.vip_price }}" lay-verify="number">
            </div>
            <div class="layui-form-mid layui-word-aux">元</div>
        </div>
    </div> -->
    <div class="layui-form-item">
        <label class="layui-form-label"></label>
        <div class="layui-input-block">
            <button class="layui-btn" lay-submit="true" lay-filter="go">提交</button>
            <button type="button" class="kg-back layui-btn layui-btn-primary">返回</button>
        </div>
    </div>
</form>
