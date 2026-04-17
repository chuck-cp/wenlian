{% extends 'templates/main.volt' %}

{% block content %}

    {{ partial('macros/sale') }}

    <div class="layout-main">
        <div class="my-sidebar">{{ partial('user/console/menu') }}</div>
        <div class="my-content">
            <div class="wrap">
                <form class="layui-form redeem-form" method="post" action="{{ url({'for':'home.digital_card.redeem'}) }}">
                    <div class="layui-inline">
                        <input class="layui-input" type="text" name="code" autocomplete="off" placeholder="请输入兑换码" lay-verify="required">
                    </div>
                    <div class="layui-inline">
                        <button class="layui-btn" lay-submit="true" lay-filter="go">兑换</button>
                    </div>
                </form>
            </div>
            <div class="wrap">
                <div class="my-nav">
                    <span class="title">兑换记录</span>
                </div>
                {% if pager.total_pages > 0 %}
                    <table class="layui-table" lay-size="lg" lay-skin="line">
                        <colgroup>
                            <col>
                            <col>
                            <col>
                        </colgroup>
                        <thead>
                        <tr>
                            <th>物品名称</th>
                            <th>兑换码</th>
                            <th>兑换时间</th>
                        </tr>
                        </thead>
                        <tbody>
                        {% for card in pager.items %}
                            <tr>
                                {% if card.item.type == 1 %}
                                    {% set course_url = url({'for':'home.course.show','id':card.item.id}) %}
                                    <td>
                                        <a href="{{ course_url }}" target="_blank">{{ card.item.title }}</a>
                                        <span class="layui-badge layui-bg-blue">{{ sale_item_type(card.item.type) }}</span>
                                    </td>
                                {% elseif card.item.type == 4 %}
                                    {% set paper_url = url({'for':'home.exam_paper.show','id':card.item.id}) %}
                                    <td>
                                        <a href="{{ paper_url }}" target="_blank">{{ card.item.title }}</a>
                                        <span class="layui-badge layui-bg-blue">{{ sale_item_type(card.item.type) }}</span>
                                    </td>
                                {% elseif card.item.type == 5 %}
                                    {% set article_url = url({'for':'home.article.show','id':card.item.id}) %}
                                    <td>
                                        <a href="{{ article_url }}" target="_blank">{{ card.item.title }}</a>
                                        <span class="layui-badge layui-bg-blue">{{ sale_item_type(card.item.type) }}</span>
                                    </td>
                                {% else %}
                                    <td>
                                        <span>{{ card.item.title }}</span>
                                        <span class="layui-badge layui-bg-blue">{{ sale_item_type(card.item.type) }}</span>
                                    </td>
                                {% endif %}
                                <td>
                                    <span>{{ card.code }}</span>
                                    {% if card.deleted == 1 %}
                                        <span class="layui-badge layui-bg-gray">已作废</span>
                                    {% endif %}
                                </td>
                                <td>{{ date('Y-m-d',card.redeem_time) }}</td>
                            </tr>
                        {% endfor %}
                        </tbody>
                    </table>
                    {{ partial('partials/pager') }}
                {% endif %}
            </div>
        </div>
    </div>

{% endblock %}
