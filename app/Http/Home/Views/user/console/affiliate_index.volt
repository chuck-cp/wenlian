{% extends 'templates/main.volt' %}

{% block content %}

    {% set sale_trend_url = url({'for':'home.uc.affiliate'},{'action':'sale_trend'}) %}
    {% set level_types = {'0':'全部','1':'一级用户','2':'二级用户','3':'三级用户'} %}
    {% set level = request.get('level','int','0') %}

    {% set affiliate_setting = setting('affiliate') %}
    {% set withdraw_setting = setting('withdraw') %}

    <div class="layout-main">
        <div class="my-sidebar">{{ partial('user/console/menu') }}</div>
        <div class="my-content">
            <div class="wrap">
                <div class="my-summary">
                    <div class="profile-column">
                        <div class="avatar">
                            <img src="{{ auth_user.avatar }}" alt="{{ auth_user.name }}">
                        </div>
                        <div class="info">
                            <div class="name"><span>{{ auth_user.name }}</span></div>
                            {% if referer.id is defined %}
                                <div class="title">推荐人：{{ referer.name }}</div>
                            {% else %}
                                <div class="title">推荐人：无</div>
                            {% endif %}
                        </div>
                    </div>
                    <!-- <div class="com-column">
                        {% if affiliate_setting['v1_com_enabled'] == 1 %}
                            <div class="stat-card">
                                <div class="name">一级抽成</div>
                                <div class="count">{{ affiliate_setting['v1_com_rate'] }}%</div>
                            </div>
                        {% endif %}
                        {% if affiliate_setting['v2_com_enabled'] == 1 %}
                            <div class="stat-card">
                                <div class="name">二级抽成</div>
                                <div class="count">{{ affiliate_setting['v2_com_rate'] }}%</div>
                            </div>
                        {% endif %}
                        {% if affiliate_setting['v3_com_enabled'] == 1 %}
                            <div class="stat-card">
                                <div class="name">三级抽成</div>
                                <div class="count">{{ affiliate_setting['v3_com_rate'] }}%</div>
                            </div>
                        {% endif %}
                    </div> -->
                    <!-- <div class="withdraw-column">
                        <div class="stat-card cash">
                            <div class="name">可用金额</div>
                            <div class="count"><a href="{{ url({'for':'home.uc.cash_history'}) }}" title="收支记录">{{ '￥%0.2f'|format(balance.cash) }}</a></div>
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
            <div class="wrap">
                <!-- <div class="my-nav">
                    <span class="title">分销团队</span>
                    {% for key,value in level_types %}
                        {% set class = (level == key) ? 'layui-btn layui-btn-xs' : 'none' %}
                        {% set url = (key == '0') ? url({'for':'home.uc.affiliate'},{'action':'index'}) : url({'for':'home.uc.affiliate'},{'action':'index','level':key}) %}
                        <a class="{{ class }}" href="{{ url }}">{{ value }}</a>
                    {% endfor %}
                    <a class="layui-btn layui-btn-sm sub-nav sale-trend" href="javascript:" data-url="{{ sale_trend_url }}">分销统计</a>
                </div> -->
                {% if pager.total_pages > 0 %}
                    <table class="layui-table" lay-size="lg" lay-skin="line">
                        <colgroup>
                            <col width="10%">
                            <col>
                            <col>
                            <col>
                            <col width="15%">
                        </colgroup>
                        <thead>
                        <tr>
                            <th>头像</th>
                            <th>名称</th>
                            <th>级别</th>
                            <th>加入时间</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        {% for item in pager.items %}
                            {% set user_url = url({'for':'home.user.show','id':item.user.id}) %}
                            <tr>
                                <td><img class="avatar-sm" src="{{ item.user.avatar }}" alt="{{ item.user.name }}"></td>
                                <td><a href="{{ user_url }}" target="_blank">{{ item.user.name }}</a></td>
                                <td>{{ '%s级'|format(item.level) }}</td>
                                <td>{{ date('Y-m-d',item.create_time) }}</td>
                                <td>
                                    <a class="layui-btn layui-btn-xs" href="{{ user_url }}" target="_blank">查看</a>
                                </td>
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

{% block inline_js %}

    <script>
        layui.use(['jquery', 'layer'], function () {

            var $ = layui.jquery;
            var layer = layui.layer;

            $('.sale-trend').on('click', function () {
                var url = $(this).data('url');
                layer.open({
                    type: 2,
                    title: '分销统计',
                    content: url,
                    area: ['800px', '540px'],
                });
            });

        });
    </script>

{% endblock %}