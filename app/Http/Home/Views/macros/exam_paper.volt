{%- macro exam_type(value) %}
    {% if value == 1 %}
        考试
    {% elseif value == 2 %}
        练习
    {% else %}
        N/A
    {% endif %}
{%- endmacro %}

{%- macro pack_type(value) %}
    {% if value == 1 %}
        人工组卷
    {% elseif value == 2 %}
        随机组卷
    {% else %}
        N/A
    {% endif %}
{%- endmacro %}

{%- macro level_type(value) %}
    {% if value == 1 %}
        初级
    {% elseif value == 2 %}
        中级
    {% elseif value == 3 %}
        高级
    {% else %}
        N/A
    {% endif %}
{%- endmacro %}

{%- macro exam_type_badge(value) %}
    {% if value == 1 %}
        <span class="layui-badge layui-bg-green">考试</span>
    {% elseif value == 2 %}
        <span class="layui-badge layui-bg-blue">练习</span>
    {% endif %}
{%- endmacro %}

{% macro exam_paper_card(paper) %}
    {% set paper_url = url({'for':'home.exam_paper.show','id':paper.id}) %}
    <div class="course-card">
        <div class="model">{{ exam_type_badge(paper.exam_type) }}</div>
        <div class="cover">
            <a href="{{ paper_url }}" target="_blank">
                <img src="{{ paper.cover }}" alt="{{ paper.title }}" title="{{ paper.title }}">
            </a>
        </div>
        <div class="info">
            <div class="title layui-elip">
                <a href="{{ paper_url }}" title="{{ paper.title }}" target="_blank">{{ paper.title }}</a>
            </div>
            <div class="meta">
                {% if paper.market_price == 0 %}
                    <span class="free">全员免费</span>
                    <span class="level">{{ level_type(paper.level) }}</span>
                    <span class="user">{{ paper.join_count }} 人报名</span>
                {% elseif paper.vip_price == 0 %}
                    <span class="free">会员免费</span>
                    <span class="level">{{ level_type(paper.level) }}</span>
                    <span class="user">{{ paper.join_count }} 人购买</span>
                {% elseif paper.market_price > 0 %}
                    <span class="price">{{ '￥%0.2f'|format(paper.market_price) }}</span>
                    <span class="level">{{ level_type(paper.level) }}</span>
                    <span class="user">{{ paper.join_count }} 人购买</span>
                {% endif %}
            </div>
        </div>
    </div>
{% endmacro %}

{%- macro sidebar_exam_paper_card(paper) %}
    {% set paper_url = url({'for':'home.exam_paper.show','id':paper.id}) %}
    <div class="sidebar-course-card">
        <div class="cover">
            <img src="{{ paper.cover }}" alt="{{ paper.title }}">
        </div>
        <div class="info">
            <div class="title layui-elip">
                <a href="{{ paper_url }}" title="{{ paper.title }}" target="_blank">{{ paper.title }}</a>
            </div>
            <div class="meta">
                {% if paper.market_price == 0 %}
                    <span class="free">全员免费</span>
                    <span class="level">{{ level_type(paper.level) }}</span>
                    <span class="user">{{ paper.join_count }} 人报名</span>
                {% elseif paper.vip_price == 0 %}
                    <span class="price">会员免费</span>
                    <span class="level">{{ level_type(paper.level) }}</span>
                    <span class="user">{{ paper.join_count }} 人购买</span>
                {% elseif paper.market_price > 0 %}
                    <span class="price">{{ '￥%0.2f'|format(paper.market_price) }}</span>
                    <span class="level">{{ level_type(paper.level) }}</span>
                    <span class="user">{{ paper.join_count }} 人购买</span>
                {% endif %}
            </div>
        </div>
    </div>
{%- endmacro %}
