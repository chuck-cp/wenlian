{% extends 'templates/main.volt' %}

{% block content %}

    {{ partial('macros/article') }}

    {% set add_url = url({'for':'admin.article.add'}) %}
    {% set search_url = url({'for':'admin.article.search'}) %}
    {% set category_url = url({'for':'admin.article.category'}) %}

    <div class="kg-nav">
        <div class="kg-nav-left">
            <span class="layui-breadcrumb">
                <a><cite>文章管理</cite></a>
            </span>
        </div>
        <div class="kg-nav-right">
            <a class="layui-btn layui-btn-sm" href="{{ category_url }}">
                <i class="layui-icon layui-icon-add-1"></i>文章分类
            </a>
            <a class="layui-btn layui-btn-sm" href="{{ add_url }}">
                <i class="layui-icon layui-icon-add-1"></i>添加文章
            </a>
            <a class="layui-btn layui-btn-sm" href="{{ search_url }}">
                <i class="layui-icon layui-icon-search"></i>搜索文章
            </a>
        </div>
    </div>

    <table class="layui-table layui-form kg-table">
        <colgroup>
            <col>
            <col>
            <col>
            <col>
            <col>
            <col>
            <col width="10%">
        </colgroup>
        <thead>
        <tr>
            <th>基本信息</th>
            <th>统计信息</th>
            <!-- <th>价格信息</th> -->
            <th>推荐</th>
            <th>关闭</th>
            <th>发布</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        {% for item in pager.items %}
            {% set article_url = url({'for':'home.article.show','id':item.id}) %}
            {% set edit_url = url({'for':'admin.article.edit','id':item.id}) %}
            {% set update_url = url({'for':'admin.article.update','id':item.id}) %}
            {% set delete_url = url({'for':'admin.article.delete','id':item.id}) %}
            {% set restore_url = url({'for':'admin.article.restore','id':item.id}) %}
            {% set users_url = url({'for':'admin.article.users','id':item.id}) %}
            {% set comments_url = url({'for':'admin.comment.list'},{'item_id':item.id,'item_type':2}) %}
            <tr>
                <td>
                    <p>标题：<a href="{{ edit_url }}">{{ item.title }}</a>（{{ item.id }}）</p>
                    <p class="meta">
                        {% if item.category.id is defined %}
                            <span>分类：{{ item.category.name }}</span>
                        {% endif %}
                        {% if item.owner.id is defined %}
                            <span>作者：{{ item.owner.name }}</span>
                        {% endif %}
                    </p>
                    <p class="meta">
                        <span>来源：{{ source_type(item.source_type) }}</span>
                        <span>创建：{{ date('Y-m-d',item.create_time) }}</span>
                    </p>
                </td>
                <td>
                    <p class="meta">
                        <span>学员：{{ item.user_count }}</span>
                        <span>浏览：{{ item.view_count }}</span>
                        <span>评论：{{ item.comment_count }}</span>
                    </p>
                    <p class="meta">
                        <span>点赞：{{ item.like_count }}</span>
                        <span>收藏：{{ item.favorite_count }}</span>
                    </p>
                </td>
                <!-- <td>
                    <p>市场价：{{ '￥%0.2f'|format(item.market_price) }}</p>
                    <p>会员价：{{ '￥%0.2f'|format(item.vip_price) }}</p>
                </td> -->
                <td><input type="checkbox" name="featured" value="1" lay-text="是|否" lay-skin="switch" lay-filter="go" data-url="{{ update_url }}"
                           {% if item.featured == 1 %}checked="checked"{% endif %}></td>
                <td><input type="checkbox" name="closed" value="1" lay-text="是|否" lay-skin="switch" lay-filter="go" data-url="{{ update_url }}"
                           {% if item.closed == 1 %}checked="checked"{% endif %}></td>
                <td><input type="checkbox" name="published" value="1" lay-text="是|否" lay-skin="switch" lay-filter="go" data-url="{{ update_url }}"
                           {% if item.published == 1 %}checked="checked"{% endif %}></td>
                <td class="center">
                    <div class="kg-dropdown">
                        <button class="layui-btn layui-btn-sm">操作 <i class="layui-icon layui-icon-triangle-d"></i></button>
                        <ul>
                            <li><a href="{{ article_url }}" target="_blank">浏览文章</a></li>
                            <li><a href="{{ edit_url }}">编辑文章</a></li>
                            {% if item.deleted == 0 %}
                                <li><a href="javascript:" class="kg-delete" data-url="{{ delete_url }}">删除文章</a></li>
                            {% else %}
                                <li><a href="javascript:" class="kg-restore" data-url="{{ restore_url }}">还原文章</a></li>
                            {% endif %}
                            <hr>
                            <li><a href="{{ users_url }}">学员管理</a></li>
                            <li><a href="{{ comments_url }}">评论管理</a></li>
                        </ul>
                    </div>
                </td>
            </tr>
        {% endfor %}
        </tbody>
    </table>

    {{ partial('partials/pager') }}

{% endblock %}
