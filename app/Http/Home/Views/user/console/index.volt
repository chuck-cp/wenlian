{% extends 'templates/main.volt' %}

{% block content %}

    {%- macro vip_info(user) %}
        {% set vip_url = url({'for':'home.vip.index'}) %}
        {% if user.vip == 1 %}
            <a class="layui-badge layui-bg-orange" title="到期时间：{{ date('Y-m-d',user.vip_expiry_time) }}" href="{{ vip_url }}">会员</a>
        {% else %}
            <a class="layui-badge layui-bg-gray" title="开通会员" href="{{ vip_url }}">会员</a>
        {% endif %}
    {%- endmacro %}

    {% set withdraw_setting = setting('withdraw') %}

    <div class="layout-main">
        <div class="my-sidebar">{{ partial('user/console/menu') }}</div>
        <div class="my-content">
            <div class="wrap">
                <div class="my-summary">
                    <div class="profile-column">
                        <div class="avatar">
                            <img src="{{ user.avatar }}" alt="{{ user.name }}">
                        </div>
                        <div class="info">
                            <div class="name"><span>{{ user.name }}</span> {{ vip_info(user) }}</div>
                            <div class="title">{{ user.title|default('小小书童') }}</div>
                        </div>
                    </div>
                    <!-- <div class="stat-column">
                        <div class="stat-card point">
                            <div class="name">可用积分</div>
                            <div class="count"><a href="{{ url({'for':'home.uc.point_history'}) }}" title="积分记录">{{ balance.point }}</a></div>
                        </div>
                        <div class="stat-card cash">
                            <div class="name">提现额度</div>
                            <div class="count"><a href="{{ url({'for':'home.uc.cash_history'}) }}" title="收支记录">{{ '￥%0.2f'|format(balance.cash) }}</a></div>
                        </div>
                        <div class="stat-card cash">
                            <div class="name">开票额度</div>
                            <div class="count"><a href="{{ url({'for':'home.uc.invoice'},{'action':'list'}) }}" title="开票记录">{{ '￥%0.2f'|format(balance.invoice) }}</a></div>
                        </div>
                        {% if withdraw_setting['enabled'] == 1 %}
                            <div class="withdraw">
                                <a class="layui-btn layui-btn-danger" href="{{ url({'for':'home.uc.withdraw'},{'action':'apply'}) }}">提现</a>
                            </div>
                        {% else %}
                            <div class="withdraw">
                                <button class="layui-btn layui-btn-disabled">提现</button>
                            </div>
                        {% endif %}
                    </div> -->
                </div>
            </div>
            <div class="layui-card my-stats">
                <div class="layui-card-header">数据统计</div>
                <div class="layui-card-body">
                    <div class="layui-row layui-col-space10">
                        <div class="layui-col-md2">
                            <div class="stat-card">
                                <div class="name">课程数</div>
                                <div class="count">{{ user.study_course_count }}</div>
                            </div>
                        </div>
                        <!-- <div class="layui-col-md2">
                            <div class="stat-card">
                                <div class="name">考试数</div>
                                <div class="count">{{ user.study_paper_count }}</div>
                            </div>
                        </div> -->
                        <!-- <div class="layui-col-md2">
                            <div class="stat-card">
                                <div class="name">文章数</div>
                                <div class="count">{{ user.study_article_count }}</div>
                            </div>
                        </div> -->
                        <!-- <div class="layui-col-md2">
                            <div class="stat-card">
                                <div class="name">提问数</div>
                                <div class="count">{{ user.question_count }}</div>
                            </div>
                        </div> -->
                        <div class="layui-col-md2">
                            <div class="stat-card">
                                <div class="name">回答数</div>
                                <div class="count">{{ user.answer_count }}</div>
                            </div>
                        </div>
                        <div class="layui-col-md2">
                            <div class="stat-card">
                                <div class="name">评论数</div>
                                <div class="count">{{ user.comment_count }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="layui-card my-stats">
                <div class="layui-card-header">在线记录</div>
                <div class="layui-card-body">
                    <div class="my-online-list">
                        {% for item in online_stats %}
                            {% if item.online == 1 %}
                                <div class="day active" title="{{ date('Y-m-d H:i',item.active_time) }}">{{ item.day }}</div>
                            {% else %}
                                <div class="day">{{ item.day }}</div>
                            {% endif %}
                        {% endfor %}
                    </div>
                </div>
            </div>
            <!-- <div class="layui-card my-stats">
                <div class="layui-card-header">积分统计</div>
                <div class="layui-card-body">
                    <div id="point-chart" style="height:240px;"></div>
                </div>
            </div> -->
        </div>
    </div>

{% endblock %}

{% block include_js %}

    {{ js_include('lib/echarts/echarts.min.js') }}

{% endblock %}

{% block inline_js %}

    <script>

        var myChart = echarts.init(document.getElementById('point-chart'));

        var option = {
            dataset: {
                source: {{ point_stats|json_encode }}
            },
            xAxis: {type: 'category'},
            yAxis: {},
            series: [
                {type: 'line', smooth: true}
            ]
        };

        myChart.setOption(option);

    </script>

{% endblock %}
