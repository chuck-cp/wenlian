{% extends 'templates/main.volt' %}

{% block content %}
    
    {% set action_url = url({'for':'admin.setting.security'}) %}

    <div class="layui-tabs">
        <ul class="layui-tabs-header">
            <li class="layui-this">内容审核</li>
            <li>限流控制</li>
            <li>黑名单</li>
        </ul>
        <div class="layui-tabs-body">
            <div class="layui-tabs-item layui-show">
                <form class="layui-form kg-form" method="POST" action="{{ action_url }}">
                    <div class="layui-form-item">
                        <label class="layui-form-label">使用说明</label>
                        <div class="layui-input-block">
                            <div class="layui-form-mid layui-word-aux">开启内容自动审核，大大降低违规风险，违规内容自动屏蔽，疑似违规人工复核。注意：此服务依赖腾讯云存储，<a href="https://cloud.tencent.com/document/product/436/45435" target="_blank">【官方文档】</a></div>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">审核咨询</label>
                        <div class="layui-input-block">
                            <input type="radio" name="consult_enabled" value="1" title="是" {% if audit.consult_enabled == "1" %}checked="checked"{% endif %}>
                            <input type="radio" name="consult_enabled" value="0" title="否" {% if audit.consult_enabled == "0" %}checked="checked"{% endif %}>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">审核评价</label>
                        <div class="layui-input-block">
                            <input type="radio" name="review_enabled" value="1" title="是" {% if audit.review_enabled == "1" %}checked="checked"{% endif %}>
                            <input type="radio" name="review_enabled" value="0" title="否" {% if audit.review_enabled == "0" %}checked="checked"{% endif %}>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">审核提问</label>
                        <div class="layui-input-block">
                            <input type="radio" name="question_enabled" value="1" title="是" {% if audit.question_enabled == "1" %}checked="checked"{% endif %}>
                            <input type="radio" name="question_enabled" value="0" title="否" {% if audit.question_enabled == "0" %}checked="checked"{% endif %}>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">审核回答</label>
                        <div class="layui-input-block">
                            <input type="radio" name="answer_enabled" value="1" title="是" {% if audit.answer_enabled == "1" %}checked="checked"{% endif %}>
                            <input type="radio" name="answer_enabled" value="0" title="否" {% if audit.answer_enabled == "0" %}checked="checked"{% endif %}>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">审核评论</label>
                        <div class="layui-input-block">
                            <input type="radio" name="comment_enabled" value="1" title="是" {% if audit.comment_enabled == "1" %}checked="checked"{% endif %}>
                            <input type="radio" name="comment_enabled" value="0" title="否" {% if audit.comment_enabled == "0" %}checked="checked"{% endif %}>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label"></label>
                        <div class="layui-input-block">
                            <button class="layui-btn" lay-submit="true" lay-filter="go">提交</button>
                            <button type="button" class="kg-back layui-btn layui-btn-primary">返回</button>
                            <input type="hidden" name="section" value="security.audit">
                        </div>
                    </div>
                </form>
            </div>
            <div class="layui-tabs-item">
                <form class="layui-form kg-form" method="POST" action="{{ action_url }}">
                    <div class="layui-form-item">
                        <label class="layui-form-label">开启限流</label>
                        <div class="layui-input-block">
                            <input type="radio" name="enabled" value="1" title="是" {% if throttle.enabled == "1" %}checked="checked"{% endif %}>
                            <input type="radio" name="enabled" value="0" title="否" {% if throttle.enabled == "0" %}checked="checked"{% endif %}>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">计数周期（秒）</label>
                        <div class="layui-input-block">
                            <input class="layui-input" type="text" name="interval" value="{{ throttle.interval }}" lay-verify="number">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">请求频率（次）</label>
                        <div class="layui-input-block">
                            <input class="layui-input" type="text" name="rate_limit" value="{{ throttle.rate_limit }}" lay-verify="number">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label"></label>
                        <div class="layui-input-block">
                            <button class="layui-btn" lay-submit="true" lay-filter="go">提交</button>
                            <button type="button" class="kg-back layui-btn layui-btn-primary">返回</button>
                            <input type="hidden" name="section" value="security.throttle">
                        </div>
                    </div>
                </form>
            </div>
            <div class="layui-tabs-item">
                <form class="layui-form kg-form" method="POST" action="{{ action_url }}">
                    <div class="layui-form-item">
                        <label class="layui-form-label">开启黑名单</label>
                        <div class="layui-input-block">
                            <input type="radio" name="enabled" value="1" title="是" {% if blacklist.enabled == "1" %}checked="checked"{% endif %}>
                            <input type="radio" name="enabled" value="0" title="否" {% if blacklist.enabled == "0" %}checked="checked"{% endif %}>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">受限IP列表</label>
                        <div class="layui-input-block">
                            <textarea class="layui-textarea" name="content">{{ blacklist.content }}</textarea>
                            <div class="layui-form-mid gray">多个IP使用半角逗号分隔</div>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label"></label>
                        <div class="layui-input-block">
                            <button class="layui-btn" lay-submit="true" lay-filter="go">提交</button>
                            <button type="button" class="kg-back layui-btn layui-btn-primary">返回</button>
                            <input type="hidden" name="section" value="security.blacklist">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

{% endblock %}
