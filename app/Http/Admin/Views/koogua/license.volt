{% extends 'templates/main.volt' %}

{% block content %}

    <div class="kg-license-wrap">
        <fieldset class="layui-elem-field layui-field-title">
            <legend>系统授权</legend>
            <form class="layui-form" method="POST" action="{{ url({'for':'admin.koogua.license'}) }}">
                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <textarea class="layui-textarea" name="content" placeholder="请填写授权密钥..." lay-verify="required"></textarea>
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button class="layui-btn layui-btn-fluid" lay-submit="true" lay-filter="go">确认提交</button>
                    </div>
                </div>
            </form>
        </fieldset>
        <div class="copyright">
            {% if site_info.copyright %}
                <span>&copy; {{ site_info.copyright }}</span>
            {% endif %}
            {% if site_info.icp_sn %}
                <a href="{{ site_info.icp_link }}" target="_blank">{{ site_info.icp_sn }}</a>
            {% endif %}
            {% if site_info.isp_sn %}
                <a href="{{ site_info.isp_link }}" target="_blank">{{ site_info.isp_sn }}</a>
            {% endif %}
            {% if site_info.police_sn %}
                <a href="{{ site_info.police_link }}" target="_blank">{{ site_info.police_sn }}</a>
            {% endif %}
        </div>
    </div>

{% endblock %}

{% block inline_js %}

    <script>
        if (window !== top) {
            top.location.href = window.location.href;
        }
    </script>

{% endblock %}