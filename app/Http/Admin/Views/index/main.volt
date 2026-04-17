{% extends 'templates/main.volt' %}

{% block content %}

    {% set auth_type = license_info.auth_type is defined ? license_info.auth_type : 'expire_time' %}
    {% set show_app_trend = config('dashboard.app_trend', true) %}
    {% set license_url = url({'for':'admin.license'}) %}

    <div class="layui-fluid">
        <div class="layui-row layui-col-space15">
            <div class="layui-col-md8">
                {{ partial('index/main_global_stat') }}
                {{ partial('index/main_mod_stat') }}
                {{ partial('index/main_report_stat') }}
                {% if license_info.affiliate == 0 %}
                    {{ partial('index/main_app_trend') }}
                {% endif %}
            </div>
            <div class="layui-col-md4">
                {{ partial('index/main_today_stat') }}
                {{ partial('index/main_biz_review_stat') }}
                {{ partial('index/main_app_info') }}
                {{ partial('index/main_server_info') }}
                {% if license_info.remove_copyright == 0 %}
                    {{ partial('index/main_team_info') }}
                {% endif %}
            </div>
        </div>
    </div>

    {% if auth_type == 'user_count' %}
        {% if global_stat.user_count / license_info.user_count > 0.9 %}
            <div class="layui-hide" id="layer-license">
                <div class="warning-tips">
                    授权人数即将达到上限，请及时更新授权套餐！<a href="{{ license_url }}">『更新授权』</a>
                </div>
            </div>
        {% endif %}
    {% else %}
        {% if license_info.expire_time - time() < 30 * 86400 %}
            <div class="layui-hide" id="layer-license">
                <div class="warning-tips">
                    系统授权即将过期，请及时更新授权套餐！<a href="{{ license_url }}">『更新授权』</a>
                </div>
            </div>
        {% endif %}
    {% endif %}

{% endblock %}

{% block inline_css %}

    <style>
        .kg-body {
            padding: 15px 0;
            background: #f2f2f2;
        }

        .warning-tips {
            padding: 30px;
            text-align: center;
            color: red;
        }
    </style>

{% endblock %}

{% block inline_js %}

    <script>

        layui.use(['jquery', 'layer', 'helper'], function () {

            var $ = layui.jquery;
            var layer = layui.layer;
            var helper = layui.helper;

            var $licenseLayer = $('#layer-license');

            if ($licenseLayer.length > 0) {
                layer.open({
                    type: 1,
                    title: '文联云课堂',
                    area: ['480px', '160px'],
                    content: $licenseLayer.html(),
                });
            }

            var $appTrend = $('#app-trend');

            helper.ajaxLoadHtml($appTrend.data('url'), $appTrend.attr('id'));

        });

    </script>

{% endblock %}
