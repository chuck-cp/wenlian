{% extends 'templates/main.volt' %}

{% block content %}

    {% set add_url = url({'for':'admin.group.add'}) %}

    <div class="kg-nav">
        <div class="kg-nav-left">
            <span class="layui-breadcrumb">
                <a><cite>分组管理</cite></a>
            </span>
        </div>
        <div class="kg-nav-right">
            <a class="layui-btn layui-btn-sm" href="{{ add_url }}">
                <i class="layui-icon layui-icon-add-1"></i>添加分组
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
            <col>
            <col>
            <col width="12%">
        </colgroup>
        <thead>
        <tr>
            <th>编号</th>
            <th>名称</th>
            <th>学员</th>
            <th>课程</th>
            <th>试卷</th>
            <th>专栏</th>
            <th>过期时间</th>
            <th>发布</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        {% for item in pager.items %}
            {% set edit_url = url({'for':'admin.group.edit','id':item.id}) %}
            {% set update_url = url({'for':'admin.group.update','id':item.id}) %}
            {% set delete_url = url({'for':'admin.group.delete','id':item.id}) %}
            {% set restore_url = url({'for':'admin.group.restore','id':item.id}) %}
            {% set users_url = url({'for':'admin.group.users','id':item.id}) %}
            {% set courses_url = url({'for':'admin.group.courses','id':item.id}) %}
            {% set papers_url = url({'for':'admin.group.exam_papers','id':item.id}) %}
            {% set articles_url = url({'for':'admin.group.articles','id':item.id}) %}
            {% set expiry_time = item.expiry_time > 0 ? date('Y-m-d H:i:s',item.expiry_time) : 'N/A' %}
            <tr>
                <td>{{ item.id }}</td>
                <td><a href="{{ edit_url }}">{{ item.name }}</a></td>
                <td><a href="{{ users_url }}">{{ item.user_count }}</a></td>
                <td><a href="{{ courses_url }}">{{ item.course_count }}</a></td>
                <td><a href="{{ papers_url }}">{{ item.paper_count }}</a></td>
                <td><a href="{{ articles_url }}">{{ item.article_count }}</a></td>
                <td>{{ expiry_time }}</td>
                <td><input type="checkbox" name="published" value="1" lay-text="是|否" lay-skin="switch" lay-filter="go" data-url="{{ update_url }}"
                           {% if item.published == 1 %}checked="checked"{% endif %}>
                </td>
                <td class="center">
                    <div class="kg-dropdown">
                        <button class="layui-btn layui-btn-sm">操作 <i class="layui-icon layui-icon-triangle-d"></i></button>
                        <ul>
                            <li><a href="{{ edit_url }}">编辑分组</a></li>
                            {% if item.deleted == 0 %}
                                <li><a href="javascript:" class="kg-delete" data-url="{{ delete_url }}">删除分组</a></li>
                            {% else %}
                                <li><a href="javascript:" class="kg-restore" data-url="{{ restore_url }}">还原分组</a></li>
                            {% endif %}
                            <hr>
                            <li><a href="{{ users_url }}">学员管理</a></li>
                            <li><a href="{{ courses_url }}">课程管理</a></li>
                            <li><a href="{{ papers_url }}">试卷管理</a></li>
                            <li><a href="{{ articles_url }}">专栏管理</a></li>
                        </ul>
                    </div>
                </td>
            </tr>
        {% endfor %}
        </tbody>
    </table>

    {{ partial('partials/pager') }}

{% endblock %}
