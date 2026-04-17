{%- macro source_type(value) %}
    {% if value == 1 %}
        原创
    {% elseif value == 2 %}
        转载
    {% elseif value == 3 %}
        翻译
    {% else %}
        N/A
    {% endif %}
{%- endmacro %}

{%- macro source_type_badge(value) %}
    {% if value == 1 %}
        <span class="layui-badge layui-bg-green">原创</span>
    {% elseif value == 2 %}
        <span class="layui-badge layui-bg-orange">转载</span>
    {% elseif value == 3 %}
        <span class="layui-badge layui-bg-blue">翻译</span>
    {% endif %}
{%- endmacro %}

{% macro article_card(article) %}
    {% set article_url = url({'for':'home.article.show','id':article.id}) %}
    <div class="course-card">
        <div class="model">{{ source_type_badge(article.source_type) }}</div>
        <div class="cover">
            <a href="{{ article_url }}" target="_blank">
                <img src="{{ article.cover }}" alt="{{ article.title }}" title="{{ article.title }}">
            </a>
        </div>
        <div class="info">
            <div class="title layui-elip">
                <a href="{{ article_url }}" title="{{ article.title }}" target="_blank">{{ article.title }}</a>
            </div>
            <div class="meta">
                {% if article.market_price == 0 %}
                    <span class="free">全员免费</span>
                    <span class="user">{{ article.user_count }} 人学习</span>
                    <span class="user">{{ article.like_count }} 人喜欢</span>
                {% elseif article.vip_price == 0 %}
                    <span class="free">会员免费</span>
                    <span class="user">{{ article.user_count }} 人购买</span>
                    <span class="user">{{ article.like_count }} 人喜欢</span>
                {% elseif article.market_price > 0 %}
                    <span class="price">{{ '￥%0.2f'|format(article.market_price) }}</span>
                    <span class="user">{{ article.user_count }} 人购买</span>
                    <span class="user">{{ article.like_count }} 人喜欢</span>
                {% endif %}
            </div>
        </div>
    </div>
{% endmacro %}
