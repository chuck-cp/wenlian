{% extends 'templates/main.volt' %}

{% block content %}

    {{ partial('macros/user') }}

    {% set target = request.get('target','string','search') %}
    {% set add_url = url({'for':'admin.user.add'}) %}
    {% set search_url = url({'for':'admin.user.search'}) %}
    {% set select_search_url = url({'for':'admin.user.search'},{'target':'select'}) %}

    <div class="kg-nav">
        <div class="kg-nav-left">
            <span class="layui-breadcrumb">
                <a><cite>用户管理</cite></a>
            </span>
        </div>
        <div class="kg-nav-right">
            {% if target == 'select' %}
                <a class="layui-btn layui-btn-sm" href="{{ select_search_url }}">
                    <i class="layui-icon layui-icon-search"></i>搜索用户
                </a>
            {% else %}
                <a class="layui-btn layui-btn-sm" href="{{ add_url }}">
                    <i class="layui-icon layui-icon-add-1"></i>添加用户
                </a>
                <a class="layui-btn layui-btn-sm" href="{{ search_url }}?target=export">
                    <i class="layui-icon layui-icon-search"></i>导出用户
                </a>
                <a class="layui-btn layui-btn-sm" href="{{ search_url }}?target=search">
                    <i class="layui-icon layui-icon-search"></i>搜索用户
                </a>
            {% endif %}
        </div>
    </div>

    <table class="layui-table kg-table">
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
            <th>用户头像</th>
            <th>基本信息</th>
            <th>帐号信息</th>
            <th>用户角色</th>
            <th>数据统计</th>
            <th>活跃动态</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        {% for item in pager.items %}
            {% set account_phone = item.account.phone ? item.account.phone : 'N/A' %}
            {% set account_email = item.account.email ? item.account.email : 'N/A' %}
            {% set show_url = url({'for':'admin.user.show','id':item.id}) %}
            {% set edit_url = url({'for':'admin.user.edit','id':item.id}) %}
            {% set delete_url = url({'for':'admin.user.delete','id':item.id}) %}
            {% set restore_url = url({'for':'admin.user.restore','id':item.id}) %}
            {% set assign_course_url = url({'for':'admin.user.assign_course','id':item.id}) %}
            {% set assign_paper_url = url({'for':'admin.user.assign_exam_paper','id':item.id}) %}
            {% set assign_article_url = url({'for':'admin.user.assign_article','id':item.id}) %}
            <tr>
                <td class="center">
                    <img class="kg-avatar-sm" src="{{ item.avatar }}" alt="{{ item.name }}">
                </td>
                <td>
                    <p>昵称：<a href="{{ edit_url }}">{{ item.name }}</a>{{ status_info(item) }}</p>
                    <p>编号：{{ item.id }}</p>
                </td>
                <td>
                    <p>手机：{{ account_phone }}</p>
                    <p>邮箱：{{ account_email }}</p>
                </td>
                <td>
                    <p>教学：{{ edu_role_info(item.edu_role) }}</p>
                    <p>后台：{{ admin_role_info(item.admin_role) }}</p>
                </td>
                <td>
                    <p class="meta">
                        <span>课程：{{ item.study_course_count }}</span>
                        <span>试卷：{{ item.study_paper_count }}</span>
                        <span>专栏：{{ item.study_article_count }}</span>
                    </p>
                    <p class="meta">
                        <span>提问：{{ item.question_count }}</span>
                        <span>回答：{{ item.answer_count }}</span>
                        <span>评论：{{ item.comment_count }}</span>
                    </p>
                </td>
                <td>
                    <p>注册：{{ item.create_time > 0 ? date('Y-m-d',item.create_time) : 'N/A' }}</p>
                    <p>活跃：{{ item.active_time > 0 ? date('Y-m-d',item.active_time) : 'N/A' }}</p>
                </td>
                <td class="center">
                    <div class="kg-dropdown">
                        <button class="layui-btn layui-btn-sm">操作 <i class="layui-icon layui-icon-triangle-d"></i></button>
                        <ul>
                            {% if target == 'select' %}
                                <li><a href="javascript:" class="kg-select-user" data-id="{{ item.id }}" data-name="{{ item.name }}">选择用户</a></li>
                            {% else %}
                                <li><a href="{{ show_url }}">用户详情</a></li>
                                {% if item.admin_role.id != 1 %}
                                    <li><a href="{{ edit_url }}">编辑用户</a></li>
                                    {% if item.deleted == 0 %}
                                        <li><a href="javascript:" class="kg-delete" data-url="{{ delete_url }}">删除用户</a></li>
                                    {% else %}
                                        <li><a href="javascript:" class="kg-restore" data-url="{{ restore_url }}">还原用户</a></li>
                                    {% endif %}
                                {% else %}
                                    <li><a>编辑用户</a></li>
                                {% endif %}
                                <hr>
                                <li><a href="{{ assign_course_url }}">赠送课程</a></li>
                                <li><a href="{{ assign_paper_url }}">赠送试卷</a></li>
                                <li><a href="{{ assign_article_url }}">赠送专栏</a></li>
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

{% block inline_js %}

    {% if target == 'select' %}
        <script>

            layui.use(['jquery', 'layer'], function () {

                var $ = layui.jquery;
                var layer = layui.layer;

                $('.kg-select-user').on('click', function () {
                    var user = {
                        id: $(this).data('id'),
                        name: $(this).data('name')
                    };

                    if (parent && typeof parent.selectCertGrantUser === 'function') {
                        parent.selectCertGrantUser(user);
                    }

                    var index = parent.layer.getFrameIndex(window.name);
                    parent.layer.close(index);
                });

            });

        </script>
    {% endif %}

{% endblock %}
