{% extends 'templates/main.volt' %}

{% block content %}

    {{ partial('macros/exam_question') }}

    {% set category_url = url({'for':'admin.exam_question.category'}) %}
    {% set add_url = url({'for':'admin.exam_question.add'}) %}
    {% set search_url = url({'for':'admin.exam_question.search'}) %}
    {% set batch_publish_url = url({'for':'admin.exam_question.batch_publish'}) %}
    {% set batch_delete_url = url({'for':'admin.exam_question.batch_delete'}) %}

    <div class="kg-nav">
        <div class="kg-nav-left">
            <span class="layui-breadcrumb">
                <a><cite>试题管理</cite></a>
            </span>
            <span class="layui-btn layui-btn-sm layui-bg-blue kg-batch" data-url="{{ batch_publish_url }}">批量发布</span>
            <span class="layui-btn layui-btn-sm layui-bg-red kg-batch" data-url="{{ batch_delete_url }}">批量删除</span>
        </div>
        <div class="kg-nav-right">
            <a class="layui-btn layui-btn-sm" href="{{ category_url }}">
                <i class="layui-icon layui-icon-add-1"></i>分类管理
            </a>
            <a class="layui-btn layui-btn-sm" href="{{ add_url }}">
                <i class="layui-icon layui-icon-add-1"></i>添加试题
            </a>
            <a class="layui-btn layui-btn-sm" href="{{ search_url }}">
                <i class="layui-icon layui-icon-search"></i>搜索试题
            </a>
        </div>
    </div>

    <table class="layui-table layui-form kg-table">
        <colgroup>
            <col width="5%">
            <col width="40%">
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
            <th><input class="all" type="checkbox" lay-filter="all"></th>
            <th>题干</th>
            <th>分类</th>
            <th>题型</th>
            <th>难度</th>
            <th>分值</th>
            <th>通过率</th>
            <th>发布</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        {% for item in pager.items %}
            {% set edit_url = url({'for':'admin.exam_question.edit','id':item.id}) %}
            {% set update_url = url({'for':'admin.exam_question.update','id':item.id}) %}
            {% set delete_url = url({'for':'admin.exam_question.delete','id':item.id}) %}
            {% set restore_url = url({'for':'admin.exam_question.restore','id':item.id}) %}
            <tr>
                <td><input class="item" type="checkbox" value="{{ item.id }}" lay-filter="item"></td>
                <td>{{ item.topic }}</td>
                <td>
                    {% if item.category.id is defined %}
                        {{ item.category.name }}
                    {% else %}
                        N/A
                    {% endif %}
                </td>
                <td>{{ model_type(item.model) }}</td>
                <td>{{ level_type(item.level) }}</td>
                <td>{{ item.score }}</td>
                <td>
                    {% if item.join_count > 0 and item.pass_count > 0 %}
                        {{ '%d'|format(100 * item.pass_count / item.join_count) }}%
                    {% else %}
                        N/A
                    {% endif %}
                </td>
                <td><input type="checkbox" name="published" value="1" lay-text="是|否" lay-skin="switch" lay-filter="go" data-url="{{ update_url }}"
                           {% if item.published == 1 %}checked="checked"{% endif %}></td>
                <td class="center">
                    <div class="kg-dropdown">
                        <button class="layui-btn layui-btn-sm">操作 <i class="layui-icon layui-icon-triangle-d"></i></button>
                        <ul>
                            <li><a href="{{ edit_url }}">编辑试题</a></li>
                            {% if item.deleted == 0 %}
                                <li><a href="javascript:" class="kg-delete" data-url="{{ delete_url }}">删除试题</a></li>
                            {% else %}
                                <li><a href="javascript:" class="kg-restore" data-url="{{ restore_url }}">还原试题</a></li>
                            {% endif %}
                        </ul>
                    </div>
                </td>
            </tr>
        {% endfor %}
        </tbody>
    </table>

    {{ partial('partials/pager') }}

{% endblock %}