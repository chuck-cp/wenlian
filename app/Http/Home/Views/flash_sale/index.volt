{% extends 'templates/main.volt' %}

{% block content %}

    {{ partial('macros/sale') }}

    {%- macro sale_status(value) %}
        {% if value == 1 %}
            未开始
        {% elseif value == 2 %}
            进行中
        {% elseif value == 3 %}
            已结束
        {% else %}
            N/A
        {% endif %}
    {% endmacro %}

    {%- macro sale_item_info(sale,status) %}
        {% if sale.item.type == 1 %}
            {% set item_url = url({'for':'home.course.show','id':sale.item.id}) %}
        {% elseif sale.item.type == 2 %}
            {% set item_url = url({'for':'home.package.show','id':sale.item.id}) %}
        {% elseif sale.item.type == 3 %}
            {% set item_url = url({'for':'home.vip.index'}) %}
        {% elseif sale.item.type == 4 %}
            {% set item_url = url({'for':'home.exam_paper.show','id':sale.item.id}) %}
        {% elseif sale.item.type == 5 %}
            {% set item_url = url({'for':'home.article.show','id':sale.item.id}) %}
        {% endif %}
        {% set order_url = url({'for':'home.flash_sale.order','id':sale.id}) %}
        <div class="course-card">
            <div class="model">{{ sale_item_type_badge(sale.item.type) }}</div>
            <div class="cover">
                <a href="{{ item_url }}" target="_blank">
                    <img src="{{ sale.item.cover }}" alt="{{ sale.item.title }}" title="{{ sale.item.title }}">
                </a>
            </div>
            <div class="info">
                <div class="title layui-elip">
                    <a href="{{ item_url }}" target="_blank" title="{{ sale.item.title }}">{{ sale.item.title }}</a>
                </div>
                <div class="meta">
                    <span class="origin-price">{{ '￥%0.2f'|format(sale.item.price) }}</span>
                    <span class="price">{{ '￥%0.2f'|format(sale.price) }}</span>
                    {% if status == 2 %}
                        <span class="layui-badge order" data-url="{{ order_url }}">立即购买</span>
                    {% else %}
                        <span class="layui-badge layui-bg-gray">立即购买</span>
                    {% endif %}
                </div>
            </div>
        </div>
    {% endmacro %}

    <div class="layui-breadcrumb breadcrumb">
        <a href="/">首页</a>
        <a><cite>秒杀</cite></a>
    </div>

    {% for date_sale in sales %}
        <div class="index-wrap wrap">
            <div class="header">{{ date_sale.date }}</div>
            <div class="content">
                <div class="layui-tabs">
                    <ul class="layui-tabs-header">
                        {% for item in date_sale.items %}
                            {% set class = item.selected == 1 ? 'layui-this' : 'none' %}
                            <li class="{{ class }}">{{ item.hour }}（{{ sale_status(item.status) }}）</li>
                        {% endfor %}
                    </ul>
                    <div class="layui-tabs-body">
                        {% for item in date_sale.items %}
                            {% set class = item.selected == 1 ? 'layui-tabs-item layui-show' : 'layui-tabs-item' %}
                            <div class="{{ class }}">
                                <div class="index-course-list">
                                    <div class="layui-row layui-col-space20">
                                        {% for sale in item.items %}
                                            <div class="layui-col-md3">
                                                {{ sale_item_info(sale,item.status) }}
                                            </div>
                                        {% endfor %}
                                    </div>
                                </div>
                            </div>
                        {% endfor %}
                    </div>
                </div>
            </div>
        </div>
    {% endfor %}

{% endblock %}

{% block include_js %}

    {{ js_include('home/js/flash_sale.js') }}

{% endblock %}
