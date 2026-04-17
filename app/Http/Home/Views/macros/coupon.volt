{%- macro coupon_card_info(coupon) %}
    {% set attrs = coupon.attrs %}
    {% set coupon_class = coupon.end_time < time() ? 'coupon-card coupon-inactive' : 'coupon-card' %}
    <div class="{{ coupon_class }}">
        <div class="left">
            {% if coupon.type == 1 %}
                <div class="row type">{{ '￥%0.2f'|format(coupon.attrs.deduct_amount) }}</div>
            {% elseif coupon.type == 2 %}
                <div class="row type">{{ coupon.attrs.discount_rate }}%</div>
                {% if coupon.attrs.max_deduct_amount > 0 %}
                    <div class="row">最多抵扣{{ '￥%0.2f'|format(coupon.attrs.max_deduct_amount) }}</div>
                {% endif %}
            {% endif %}
            {% if coupon.consume_limit > 0 %}
                <div class="row">满{{ '￥%0.2f'|format(coupon.consume_limit) }} 可用</div>
            {% else %}
                <div class="row">无门槛</div>
            {% endif %}
        </div>
        <div class="right">
            <div class="row">{{ coupon.name }}</div>
            {% if coupon.item_type == 0 %}
                <div class="row">全场通用</div>
            {% elseif coupon.item_type == 1 %}
                {% if coupon.item_id > 0 %}
                    {% set course_url = url({'for':'home.course.show','id':coupon.item_info.id}) %}
                    <div class="row layui-elip">
                        <a href="{{ course_url }}" title="{{ coupon.item_info.title }}" target="_blank">课程 - {{ coupon.item_info.title }}</a>
                    </div>
                {% else %}
                    <div class="row">课程服务适用</div>
                {% endif %}
            {% elseif coupon.item_type == 2 %}
                {% if coupon.item_id > 0 %}
                    {% set package_url = url({'for':'home.package.show','id':coupon.item_info.id}) %}
                    <div class="row layui-elip">
                        <a href="{{ package_url }}" title="{{ coupon.item_info.title }}" target="_blank">套餐 - {{ coupon.item_info.title }}</a>
                    </div>
                {% else %}
                    <div class="row">套餐服务适用</div>
                {% endif %}
            {% elseif coupon.item_type == 3 %}
                {% if coupon.item_id > 0 %}
                    {% set vip_url = url({'for':'home.vip.index'}) %}
                    <div class="row layui-elip">
                        <a href="{{ vip_url }}" title="{{ coupon.item_info.title }}" target="_blank">会员 - {{ coupon.item_info.title }}</a>
                    </div>
                {% else %}
                    <div class="row">会员服务适用</div>
                {% endif %}
            {% elseif coupon.item_type == 4 %}
                {% if coupon.item_id > 0 %}
                    {% set paper_url = url({'for':'home.exam_paper.show','id':coupon.item_info.id}) %}
                    <div class="row layui-elip">
                        <a href="{{ paper_url }}" title="{{ coupon.item_info.title }}" target="_blank">试卷 - {{ coupon.item_info.title }}</a>
                    </div>
                {% else %}
                    <div class="row">考试服务适用</div>
                {% endif %}
            {% elseif coupon.item_type == 5 %}
                {% if coupon.item_id > 0 %}
                    {% set article_url = url({'for':'home.article.show','id':coupon.item_info.id}) %}
                    <div class="row layui-elip">
                        <a href="{{ article_url }}" title="{{ coupon.item_info.title }}" target="_blank">专栏 - {{ coupon.item_info.title }}</a>
                    </div>
                {% else %}
                    <div class="row">专栏文章适用</div>
                {% endif %}
            {% endif %}
            <div class="row">{{ date('Y-m-d H:i',coupon.end_time) }} 到期</div>
        </div>
    </div>

{% endmacro %}
