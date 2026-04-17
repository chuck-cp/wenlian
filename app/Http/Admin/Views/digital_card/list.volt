{% extends 'templates/main.volt' %}

{% block content %}

    {{ partial('macros/digital_card') }}
    {{ partial('macros/sale') }}

    {% set add_url = url({'for':'admin.digital_card.add'}) %}
    {% set search_url = url({'for':'admin.digital_card.search'}) %}

    <div class="kg-nav">
        <div class="kg-nav-left">
            <span class="layui-breadcrumb">
                <a><cite>兑换码管理</cite></a>
            </span>
        </div>
        <div class="kg-nav-right">
            <a class="layui-btn layui-btn-sm" href="{{ add_url }}">
                <i class="layui-icon layui-icon-add-1"></i>添加兑换码
            </a>
            <a class="layui-btn layui-btn-sm" href="{{ search_url }}?target=export">
                <i class="layui-icon layui-icon-export"></i>导出兑换码
            </a>
            <a class="layui-btn layui-btn-sm" href="{{ search_url }}">
                <i class="layui-icon layui-icon-search"></i>搜索兑换码
            </a>
        </div>
    </div>

    <table class="kg-table layui-table">
        <colgroup>
            <col>
            <col>
            <col>
            <col width="10%">
        </colgroup>
        <thead>
        <tr>
            <th>卡片信息</th>
            <th>商品信息</th>
            <th>用户信息</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        {% for item in pager.items %}
            {% set delete_url = url({'for':'admin.digital_card.delete','id':item.id}) %}
            <tr>
                <td>
                    <p>
                        <span>编码：<code id="code-{{ item.id }}">{{ item.code }}</code></span>
                        <span class="layui-btn layui-btn-xs kg-copy" data-clipboard-target="#code-{{ item.id }}">复制</span>
                    </p>
                    <p>
                        <span>期限：{{ date('Y-m-d H:i:s',item.expire_time) }}</span>
                        <span>状态：{{ redeem_status(item) }}</span>
                    </p>
                </td>
                <td>
                    {% if item.item_type == 1 %}
                        {% set course_url = url({'for':'home.course.show','id':item.item_id}) %}
                        <p>名称：<a href="{{ course_url }}" target="_blank">{{ item.item_title }}</a>（{{ item.item_id }}）</p>
                    {% elseif item.item_type == 2 %}
                        {% set package_url = url({'for':'home.package.show','id':item.item_id}) %}
                        <p>名称：<a href="{{ package_url }}" target="_blank">{{ item.item_title }}</a>（{{ item.item_id }}）</p>
                    {% elseif item.item_type == 4 %}
                        {% set paper_url = url({'for':'home.exam_paper.show','id':item.item_id}) %}
                        <p>名称：<a href="{{ paper_url }}" target="_blank">{{ item.item_title }}</a>（{{ item.item_id }}）</p>
                    {% elseif item.item_type == 5 %}
                        {% set article_url = url({'for':'home.article.show','id':item.item_id}) %}
                        <p>名称：<a href="{{ article_url }}" target="_blank">{{ item.item_title }}</a>（{{ item.item_id }}）</p>
                    {% else %}
                        <p>名称：{{ item.item_title }}（{{ item.item_id }}）</p>
                    {% endif %}
                    <p>
                        <span>类型：{{ sale_item_type(item.item_type) }}</span>
                        <span>价格：{{ '￥%0.2f'|format(item.item_price) }}</span>
                    </p>
                </td>
                <td>
                    {% if item.user_id > 0 %}
                        {% set user_url = url({'for':'home.user.show','id':item.user_id}) %}
                        <p>名称：<a href="{{ user_url }}" target="_blank">{{ item.user_name }}</a></p>
                        <p>编号：{{ item.user_id }}</p>
                    {% else %}
                        <p>N/A</p>
                    {% endif %}
                </td>
                <td>
                    {% if item.deleted == 0 %}
                        <button class="layui-btn layui-btn-danger layui-btn-sm kg-delete" data-tips="确定要作废吗？" data-url="{{ delete_url }}">作废</button>
                    {% else %}
                        <button class="layui-btn layui-btn-sm layui-btn-disabled">作废</button>
                    {% endif %}
                </td>
            </tr>
        {% endfor %}
        </tbody>
    </table>

    {{ partial('partials/pager') }}

{% endblock %}

{% block include_js %}

    {{ js_include('lib/clipboard.min.js') }}
    {{ js_include('admin/js/copy.js') }}

{% endblock %}
