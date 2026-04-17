<form class="layui-form kg-form" method="POST" action="{{ url({'for':'admin.setting.mobile'}) }}">
    <fieldset class="layui-elem-field layui-field-title">
        <legend>顶部模块</legend>
    </fieldset>
    <div class="layui-form-item">
        <label class="layui-form-label">模块内容</label>
        <div class="layui-input-block">
            <input type="checkbox" name="index_module[top][]" value="search" title="搜索" {% if 'search' in index_module['top'] %}checked="checked"{% endif %}>
            <input type="checkbox" name="index_module[top][]" value="slide" title="轮播" {% if 'slide' in index_module['top'] %}checked="checked"{% endif %}>
        </div>
    </div>
    <fieldset class="layui-elem-field layui-field-title">
        <legend>导航模块</legend>
    </fieldset>
    <div class="layui-form-item">
        <label class="layui-form-label">模块内容</label>
        <div class="layui-input-block">
            <input type="checkbox" name="index_module[nav][]" value="exam" title="考试中心" {% if 'exam' in index_module['nav'] %}checked="checked"{% endif %}>
            <input type="checkbox" name="index_module[nav][]" value="article" title="专栏文章" {% if 'article' in index_module['nav'] %}checked="checked"{% endif %}>
            <input type="checkbox" name="index_module[nav][]" value="question" title="社区问答" {% if 'question' in index_module['nav'] %}checked="checked"{% endif %}>
            <input type="checkbox" name="index_module[nav][]" value="point_gift" title="积分商城" {% if 'point_gift' in index_module['nav'] %}checked="checked"{% endif %}>
            <input type="checkbox" name="index_module[nav][]" value="flash_sale" title="限时秒杀" {% if 'flash_sale' in index_module['nav'] %}checked="checked"{% endif %}>
            <input type="checkbox" name="index_module[nav][]" value="groupon" title="团购中心" {% if 'groupon' in index_module['nav'] %}checked="checked"{% endif %}>
            <input type="checkbox" name="index_module[nav][]" value="distribution" title="分销中心" {% if 'distribution' in index_module['nav'] %}checked="checked"{% endif %}>
            <input type="checkbox" name="index_module[nav][]" value="coupon" title="领券中心" {% if 'coupon' in index_module['nav'] %}checked="checked"{% endif %}>
        </div>
    </div>
    <fieldset class="layui-elem-field layui-field-title">
        <legend>内容模块</legend>
    </fieldset>
    <div class="layui-form-item">
        <label class="layui-form-label">模块内容</label>
        <div class="layui-input-block">
            <input type="checkbox" name="index_module[content][]" value="live_preview" title="直播预告" {% if 'live_preview' in index_module['content'] %}checked="checked"{% endif %}>
            <input type="checkbox" name="index_module[content][]" value="featured_course" title="推荐课程" {% if 'featured_course' in index_module['content'] %}checked="checked"{% endif %}>
            <input type="checkbox" name="index_module[content][]" value="new_course" title="新上课程" {% if 'new_course' in index_module['content'] %}checked="checked"{% endif %}>
            <input type="checkbox" name="index_module[content][]" value="free_course" title="免费课程" {% if 'free_course' in index_module['content'] %}checked="checked"{% endif %}>
            <input type="checkbox" name="index_module[content][]" value="top_teacher" title="名师推荐" {% if 'top_teacher' in index_module['content'] %}checked="checked"{% endif %}>
            <input type="checkbox" name="index_module[content][]" value="new_article" title="最新文章" {% if 'new_article' in index_module['content'] %}checked="checked"{% endif %}>
            <input type="checkbox" name="index_module[content][]" value="new_question" title="最新问题" {% if 'new_question' in index_module['content'] %}checked="checked"{% endif %}>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label"></label>
        <div class="layui-input-block">
            <button class="layui-btn" lay-submit="true" lay-filter="go">提交</button>
            <button type="button" class="kg-back layui-btn layui-btn-primary">返回</button>
            <input type="hidden" name="tab" value="index">
        </div>
    </div>
</form>
