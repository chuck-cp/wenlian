{% extends 'templates/main.volt' %}

{% block content %}

    {{ partial('macros/exam_paper') }}

    {% set category_url = url({'for':'admin.exam_paper.category'}) %}
    {% set add_url = url({'for':'admin.exam_paper.add'}) %}
    {% set search_url = url({'for':'admin.exam_paper.search'}) %}

    <div class="kg-nav">
        <div class="kg-nav-left">
            <span class="layui-breadcrumb">
                <a><cite>试卷管理</cite></a>
            </span>
        </div>
        <div class="kg-nav-right">
            <a class="layui-btn layui-btn-sm" href="{{ category_url }}">
                <i class="layui-icon layui-icon-add-1"></i>试卷分类
            </a>
            <a class="layui-btn layui-btn-sm" href="{{ add_url }}">
                <i class="layui-icon layui-icon-add-1"></i>添加试卷
            </a>
            <a class="layui-btn layui-btn-sm" href="{{ search_url }}">
                <i class="layui-icon layui-icon-search"></i>搜索试卷
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
            <col width="10%">
        </colgroup>
        <thead>
        <tr>
            <th>基本信息</th>
            <th>统计信息</th>
            <!-- <th>价格信息</th> -->
            <th>推荐</th>
            <th>发布</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        {% for item in pager.items %}
            {% set edit_url = url({'for':'admin.exam_paper.edit','id':item.id}) %}
            {% set update_url = url({'for':'admin.exam_paper.update','id':item.id}) %}
            {% set delete_url = url({'for':'admin.exam_paper.delete','id':item.id}) %}
            {% set restore_url = url({'for':'admin.exam_paper.restore','id':item.id}) %}
            {% set users_url = url({'for':'admin.exam_paper.users','id':item.id}) %}
            {% set learnings_url = url({'for':'admin.exam_paper.learnings','id':item.id}) %}
            {% set analysis_url = url({'for':'admin.exam_paper.analysis','id':item.id}) %}
            <tr>
                <td>
                    <p class="meta">标题：<a href="{{ edit_url }}">{{ item.title }}</a> （{{ item.id }}）</p>
                    <p class="meta">
                        {% if item.category.id is defined %}
                            <span>分类：{{ item.category.name }}</span>
                        {% endif %}
                        <span>模式：{{ exam_type(item.exam_type) }}</span>
                        <span>组卷：{{ pack_type(item.pack_type) }}</span>
                        <span>难度：{{ level_type(item.level) }}</span>
                    </p>
                    <p class="meta">
                        <span>总分：{{ item.total_score > 0 ? item.total_score: 'N/A' }}</span>
                        <span>题量：{{ item.question_count > 0 ? item.question_count : 'N/A' }}</span>
                        <span>时长：{{ item.duration }}分钟</span>
                        <span>创建：{{ date('Y-m-d',item.create_time) }}</span>
                    </p>
                </td>
                <td>
                    <p>参与人次：{{ item.join_count }}</p>
                    <p>及格人次：{{ item.exam_type == 1 ? item.pass_count : 'N/A' }}</p>
                </td>
                <!-- <td>
                    <p>市场价：{{ '￥%0.2f'|format(item.market_price) }}</p>
                    <p>会员价：{{ '￥%0.2f'|format(item.vip_price) }}</p>
                </td> -->
                <td><input type="checkbox" name="featured" value="1" lay-text="是|否" lay-skin="switch" lay-filter="go" data-url="{{ update_url }}"
                           {% if item.featured == 1 %}checked="checked"{% endif %}></td>
                <td><input type="checkbox" name="published" value="1" lay-text="是|否" lay-skin="switch" lay-filter="go" data-url="{{ update_url }}"
                           {% if item.published == 1 %}checked="checked"{% endif %}></td>
                <td class="center">
                    <div class="kg-dropdown">
                        <button class="layui-btn layui-btn-sm">操作 <i class="layui-icon layui-icon-triangle-d"></i></button>
                        <ul>
                            <li><a href="{{ edit_url }}">编辑试卷</a></li>
                            {% if item.deleted == 0 %}
                                <li><a href="javascript:" class="kg-delete" data-url="{{ delete_url }}">删除试卷</a></li>
                            {% else %}
                                <li><a href="javascript:" class="kg-restore" data-url="{{ restore_url }}">还原试卷</a></li>
                            {% endif %}
                            <hr>
                            <li><a href="{{ users_url }}">学员管理</a></li>
                            <li><a href="{{ learnings_url }}">考试记录</a></li>
                            {% if item.exam_type == 1 and item.pack_type == 1 %}
                                <li><a href="{{ analysis_url }}">试卷分析</a></li>
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
