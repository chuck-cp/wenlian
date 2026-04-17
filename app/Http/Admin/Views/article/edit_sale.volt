<form class="layui-form kg-form" method="POST" action="{{ update_url }}">
    <div class="layui-form-item">
        <div class="layui-inline">
            <label class="layui-form-label kg-label-tips" data-tips="自定义商品虚拟订阅数，提升购买吸引力">
                <i class="layui-icon layui-icon-tips"></i> 虚拟订阅
            </label>
            <div class="layui-input-inline">
                <input class="layui-input" type="text" name="fake_user_count" value="{{ article.fake_user_count }}" lay-verify="number">
            </div>
            <div class="layui-form-mid layui-word-aux">人</div>
        </div>
    </div>
    <!-- <div class="layui-form-item">
        <div class="layui-inline">
            <label class="layui-form-label">市场价格</label>
            <div class="layui-input-inline">
                <input class="layui-input" type="text" name="market_price" value="{{ article.market_price }}" lay-verify="number">
            </div>
            <div class="layui-form-mid layui-word-aux">元</div>
        </div>
    </div>
    <div class="layui-form-item">
        <div class="layui-inline">
            <label class="layui-form-label">会员价格</label>
            <div class="layui-input-inline">
                <input class="layui-input" type="text" name="vip_price" value="{{ article.vip_price }}" lay-verify="number">
            </div>
            <div class="layui-form-mid layui-word-aux">元</div>
        </div>
    </div> -->
    <div class="layui-form-item">
        <label class="layui-form-label">学习期限</label>
        <div class="layui-input-block">
            {% for value,title in study_expiry_options %}
                <input type="radio" name="study_expiry" title="{{ title }}" value="{{ value }}" {% if value == article.study_expiry %}checked="checked"{% endif %}>
            {% endfor %}
        </div>
    </div>
    <!-- <div class="layui-form-item">
        <label class="layui-form-label"></label>
        <div class="layui-input-block">
            <button id="sale-submit" class="layui-btn" lay-submit="true" lay-filter="go">提交</button>
            <button type="button" class="kg-back layui-btn layui-btn-primary">返回</button>
        </div>
    </div> -->
</form>
