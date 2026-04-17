{% extends 'templates/main.volt' %}

{% block content %}

    {{ partial('macros/exam_question') }}

    <div class="kg-nav">
        <div class="kg-nav-left">
            <span class="layui-breadcrumb">
                <a class="kg-back"><i class="layui-icon layui-icon-return"></i>返回</a>
                <a><cite>{{ paper.title }}</cite></a>
                <a><cite>试卷分析</cite></a>
            </span>
        </div>
    </div>

    <div class="layui-tabs">
        <ul class="layui-tabs-header">
            <li class="layui-this">整体统计</li>
            <li>成绩分布</li>
            <li>题目统计</li>
        </ul>
        <div class="layui-tabs-body">
            <div class="layui-tabs-item layui-show">
                <table class="layui-table kg-table">
                    <thead>
                    <tr>
                        <th>总分</th>
                        <th>最高分</th>
                        <th>最低分</th>
                        <th>平均分</th>
                        <th>及格率</th>
                        <th>总人数</th>
                        <th>及格人数</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>{{ paper.total_score }}</td>
                        <td>{{ summary_stat.max_user_score }}</td>
                        <td>{{ summary_stat.min_user_score }}</td>
                        <td>{{ summary_stat.avg_user_score }}</td>
                        <td>{{ 100 * summary_stat.pass_rate }}%</td>
                        <td>{{ summary_stat.total_user_count }}</td>
                        <td>{{ summary_stat.pass_user_count }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="layui-tabs-item">
                <table class="layui-table kg-table">
                    <thead>
                    <tr>
                        <th>等级</th>
                        <th>人数</th>
                        <th>比例</th>
                    </tr>
                    </thead>
                    <tbody>
                    {% for item in range_stat %}
                        <tr>
                            <td>{{ item.name }}</td>
                            <td>{{ item.count }}</td>
                            <td>{{ 100 * item.rate }}%</td>
                        </tr>
                    {% endfor %}
                    </tbody>
                </table>
            </div>
            <div class="layui-tabs-item">
                {% for top in question_stat if top.question_count > 0 %}
                    <p>{{ model_type(top.model) }}<span class="layui-text">（共{{ top.question_count }}题）</span></p>
                    <table class="layui-table kg-table layui-form" lay-skin="line">
                        <colgroup>
                            <col>
                            <col>
                            <col>
                            <col>
                            <col width="50%">
                        </colgroup>
                        <thead>
                        <tr>
                            <th>编号</th>
                            <th>难度</th>
                            <th>完成比例</th>
                            <th>正确比例</th>
                            <th>题干</th>
                        </tr>
                        </thead>
                        <tbody>
                        {% for item in top.questions %}
                            {% set finish_percent = 100 * item.stat.finish_count / item.stat.total_count %}
                            {% set correct_percent = 100 * item.stat.correct_count / item.stat.total_count %}
                            <tr>
                                <td>{{ item.id }}</td>
                                <td>{{ level_type(item.level) }}</td>
                                <td>{{ "%d"|format(finish_percent) }}%</td>
                                <td>{{ "%d"|format(correct_percent) }}%</td>
                                <td>{{ item.topic }}</td>
                            </tr>
                        {% endfor %}
                        </tbody>
                    </table>
                {% endfor %}
            </div>
        </div>
    </div>

{% endblock %}
