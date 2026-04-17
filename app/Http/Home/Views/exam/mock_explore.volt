{{ partial('macros/exam_question') }}

{%- macro answer_analysis(item, paper_user) %}
    {% set wrap_class = item.user_score > 0 ? 'row-list success' : 'row-list error' %}
    {% if item.question_model == 5 %}
        {% set wrap_class = 'row-list primary' %}
    {% endif %}
    {% if paper_user.end_time > 0 %}
        <div class="{{ wrap_class }}">
            <div class="row">
                <div class="label">我的得分：</div>
                <div class="content score">{{ item.user_score }}</div>
            </div>
            <div class="row">
                <div class="label">正确答案：</div>
                <div class="content ke-content kg-zoom">{{ item.question.answer }}</div>
            </div>
            {% if item.question.pass_rate > 0 %}
                <div class="row">
                    <div class="label">通过率：</div>
                    <div class="content pass-rate">{{ 100 * item.question.pass_rate }}%</div>
                </div>
            {% endif %}
            {% if item.question.solution %}
                <div class="row">
                    <div class="label">答案解析：</div>
                    <div class="content ke-content kg-zoom">{{ item.question.solution }}</div>
                </div>
            {% endif %}
        </div>
        <div class="report layui-badge layui-btn-danger" data-id="{{ item.question.id }}">挑错</div>
    {% endif %}
{%- endmacro %}

{%- macro single_choice(item, paper_user) %}
    {% set star_class = item.me.favorited == 1 ? 'layui-icon-star-fill' : 'layui-icon-star' %}
    <div class="exam-question wrap" id="question-{{ item.sn }}">
        {% if paper_user.end_time > 0 and item.question.parent_id == 0 %}
            <div class="favorite" data-id="{{ item.question.id }}"><i class="layui-icon {{ star_class }}"></i> 收藏</div>
        {% endif %}
        <div class="meta">{{ item.sn }}. {{ model_type(item.question.model) }}（{{ item.question.score }}分）</div>
        <div class="topic ke-content kg-zoom">{{ item.question.topic }}</div>
        <div class="special-box">
            <div class="choice-list">
                {% for choice,content in item.question.attrs.choices %}
                    <div class="choice single-choice" data-choice="{{ choice }}" data-id="{{ item.question.id }}" data-sn="{{ item.sn }}">
                        {% if choice == item.user_answer %}
                            <div class="circle active">{{ choice }}</div>
                        {% else %}
                            <div class="circle">{{ choice }}</div>
                        {% endif %}
                        <div class="content">{{ content }}</div>
                    </div>
                {% endfor %}
            </div>
        </div>
        <div class="analysis-box">{{ answer_analysis(item, paper_user) }}</div>
    </div>
{%- endmacro %}

{%- macro multiple_choice(item, paper_user) %}
    {% set star_class = item.me.favorited == 1 ? 'layui-icon-star-fill' : 'layui-icon-star' %}
    <div class="exam-question wrap" id="question-{{ item.sn }}">
        {% if paper_user.end_time > 0 and item.question.parent_id == 0 %}
            <div class="favorite" data-id="{{ item.question.id }}"><i class="layui-icon {{ star_class }}"></i> 收藏</div>
        {% endif %}
        <div class="meta">{{ item.sn }}. {{ model_type(item.question.model) }}（{{ item.question.score }}分）</div>
        <div class="topic ke-content kg-zoom">{{ item.question.topic }}</div>
        <div class="special-box">
            <div class="choice-list">
                {% for choice,content in item.question.attrs.choices %}
                    <div class="choice multiple-choice" data-choice="{{ choice }}" data-id="{{ item.question.id }}" data-sn="{{ item.sn }}">
                        {% if choice in item.user_answer %}
                            <div class="circle active">{{ choice }}</div>
                        {% else %}
                            <div class="circle">{{ choice }}</div>
                        {% endif %}
                        <div class="content">{{ content }}</div>
                    </div>
                {% endfor %}
            </div>
        </div>
        <div class="analysis-box">{{ answer_analysis(item, paper_user) }}</div>
    </div>
{%- endmacro %}

{%- macro blank_fill(item, paper_user) %}
    {% set star_class = item.me.favorited == 1 ? 'layui-icon-star-fill' : 'layui-icon-star' %}
    {% set org_answers = item.question.answer|split(',') %}
    {% set user_answers = item.user_answer|split(',') %}
    <div class="exam-question wrap" id="question-{{ item.sn }}">
        {% if paper_user.end_time > 0 and item.question.parent_id == 0 %}
            <div class="favorite" data-id="{{ item.question.id }}"><i class="layui-icon {{ star_class }}"></i> 收藏</div>
        {% endif %}
        <div class="meta">{{ item.sn }}. {{ model_type(item.question.model) }}（{{ item.question.score }}分）</div>
        <div class="topic ke-content kg-zoom">{{ item.question.topic }}</div>
        <div class="special-box">
            <div class="blank-fill-list" id="blank-fill-{{ item.sn }}">
                {% for key,value in org_answers %}
                    {% set readonly = paper_user.end_time > 0 ? 'readonly="readonly"' : '' %}
                    <div class="blank-fill layui-form-item">
                        <div class="layui-form-label">填空{{ loop.index }}</div>
                        <div class="layui-input-block">
                            {% if user_answers[key] is defined %}
                                <input class="layui-input blank-fill-input" type="text" value="{{ user_answers[key] }}" {{ readonly }} data-id="{{ item.question.id }}" data-sn="{{ item.sn }}">
                            {% else %}
                                <input class="layui-input blank-fill-input" type="text" {{ readonly }} data-id="{{ item.question.id }}" data-sn="{{ item.sn }}">
                            {% endif %}
                        </div>
                    </div>
                {% endfor %}
            </div>
        </div>
        <div class="analysis-box">{{ answer_analysis(item, paper_user) }}</div>
    </div>
{%- endmacro %}

{%- macro short_answer(item, paper_user) %}
    {% set star_class = item.me.favorited == 1 ? 'layui-icon-star-fill' : 'layui-icon-star' %}
    {% set readonly_attr = paper_user.end_time > 0 ? 'readonly="readonly"' : '' %}
    {% set disabled_attr = item.user_answer_files|length >= 4 ? 'disabled="disabled"' : '' %}
    {% set disabled_class = item.user_answer_files|length >= 4 ? 'layui-btn-disabled' : '' %}
    {% set manual_grade = request.get('manual_grade') %}
    <div class="exam-question wrap" id="question-{{ item.sn }}">
        {% if paper_user.end_time > 0 and item.question.parent_id == 0 %}
            <div class="favorite" data-id="{{ item.question.id }}"><i class="layui-icon {{ star_class }}"></i> 收藏</div>
        {% endif %}
        <div class="meta">{{ item.sn }}. {{ model_type(item.question.model) }}（{{ item.question.score }}分）</div>
        <div class="topic ke-content kg-zoom">{{ item.question.topic }}</div>
        <div class="special-box">
            <div class="short-answer">
                <textarea class="layui-textarea short-answer-textarea" id="answer-{{ item.question.id }}" {{ readonly_attr }} data-id="{{ item.question.id }}"
                          data-sn="{{ item.sn }}">{{ item.user_answer }}</textarea>
            </div>
            <div class="answer-file-box" data-id="{{ item.question.id }}" data-sn="{{ item.sn }}" id="answer-file-box-{{ item.question.id }}">
                <div class="preview">
                    {% for file in item.user_answer_files %}
                        <div class="item">
                            <div class="sort">{{ loop.index }}</div>
                            <div class="content"><a href="{{ file.url }}" title="{{ file.name }}" target="_blank"><i class="layui-icon layui-icon-file"></i></a></div>
                            {% if paper_user.end_time == 0 %}
                                <div class="remove"><i class="layui-icon layui-icon-delete"></i></div>
                            {% endif %}
                        </div>
                    {% endfor %}
                </div>
                {% if paper_user.end_time == 0 %}
                    <div class="action">
                        <button class="layui-btn layui-btn-sm local-upload {{ disabled_class }}" {{ disabled_attr }} data-id="{{ item.question.id }}">本地上传</button>
                        <button class="layui-btn layui-btn-sm layui-bg-blue scan-upload {{ disabled_class }}" {{ disabled_attr }} data-id="{{ item.question.id }}">扫码上传</button>
                    </div>
                {% endif %}
            </div>
        </div>
        <div class="analysis-box">{{ answer_analysis(item, paper_user) }}</div>
        {% if paper_user.end_time > 0 and manual_grade == 1 %}
            <div class="mark-box">
                <form class="layui-form">
                    <div class="layui-form-item">
                        <label class="layui-form-label">本题得分</label>
                        <div class="layui-input-block">
                            <select name="user_score" lay-filter="mark" data-id="{{ item.question.id }}">
                                <option value="">请选择</option>
                                {% for value in 0..item.question_score %}
                                    <option value="{{ value }}">{{ value }}分</option>
                                {% endfor %}
                            </select>
                        </div>
                    </div>
                </form>
            </div>
        {% endif %}
    </div>
{%- endmacro %}

{%- macro complex_question(item, paper_user) %}
    {% set star_class = item.me.favorited == 1 ? 'layui-icon-star-fill' : 'layui-icon-star' %}
    <div class="complex-question wrap" id="question-{{ item.sn }}">
        {% if paper_user.end_time > 0 %}
            <div class="favorite" data-id="{{ item.question.id }}"><i class="layui-icon {{ star_class }}"></i> 收藏</div>
        {% endif %}
        <div class="meta">{{ item.sn }}. {{ model_type(item.question.model) }}（{{ item.question.score }}分）</div>
        <div class="topic ke-content kg-zoom">{{ item.question.topic }}</div>
        <div class="complex-question-list">
            {% for child in item.children %}
                {% if child.question_model == 1 %}
                    {{ single_choice(child, paper_user) }}
                {% elseif child.question_model == 2 %}
                    {{ multiple_choice(child, paper_user) }}
                {% elseif child.question_model == 3 %}
                    {{ single_choice(child, paper_user) }}
                {% elseif child.question_model == 4 %}
                    {{ blank_fill(child, paper_user) }}
                {% elseif child.question_model == 5 %}
                    {{ short_answer(child, paper_user) }}
                {% endif %}
            {% endfor %}
        </div>
    </div>
{%- endmacro %}

{%- macro exam_on_sorts(questions) %}
    <div class="exam-sort-list">
        {% for item in questions %}
            {% if item.question_model == 9 %}
                <div class="sort disabled" id="sort-{{ item.sn }}" data-sn="{{ item.sn }}">{{ item.sn }}</div>
                {% for child in item.children %}
                    {% if child.user_answer == '' %}
                        <div class="sort" id="sort-{{ child.sn }}" data-sn="{{ child.sn }}">{{ child.sn }}</div>
                    {% else %}
                        <div class="sort active" id="sort-{{ child.sn }}" data-sn="{{ child.sn }}">{{ child.sn }}</div>
                    {% endif %}
                {% endfor %}
            {% else %}
                {% if item.user_answer == '' %}
                    <div class="sort" id="sort-{{ item.sn }}" data-sn="{{ item.sn }}">{{ item.sn }}</div>
                {% else %}
                    <div class="sort active" id="sort-{{ item.sn }}" data-sn="{{ item.sn }}">{{ item.sn }}</div>
                {% endif %}
            {% endif %}
        {% endfor %}
    </div>
{%- endmacro %}

{%- macro exam_off_sorts(questions) %}
    <div class="exam-sort-list">
        {% for item in questions %}
            {% if item.question_model == 9 %}
                <div class="sort disabled" id="sort-{{ item.sn }}" data-sn="{{ item.sn }}">{{ item.sn }}</div>
                {% for child in item.children %}
                    {% if child.question_model == 5 %}
                        <div class="sort primary" id="sort-{{ child.sn }}" data-sn="{{ child.sn }}">{{ child.sn }}</div>
                    {% elseif child.user_score > 0 %}
                        <div class="sort success" id="sort-{{ child.sn }}" data-sn="{{ child.sn }}">{{ child.sn }}</div>
                    {% else %}
                        <div class="sort error" id="sort-{{ child.sn }}" data-sn="{{ child.sn }}">{{ child.sn }}</div>
                    {% endif %}
                {% endfor %}
            {% else %}
                {% if item.question_model == 5 %}
                    <div class="sort primary" id="sort-{{ item.sn }}" data-sn="{{ item.sn }}">{{ item.sn }}</div>
                {% elseif item.user_score > 0 %}
                    <div class="sort success" id="sort-{{ item.sn }}" data-sn="{{ item.sn }}">{{ item.sn }}</div>
                {% else %}
                    <div class="sort error" id="sort-{{ item.sn }}" data-sn="{{ item.sn }}">{{ item.sn }}</div>
                {% endif %}
            {% endif %}
        {% endfor %}
    </div>
{%- endmacro %}

<!DOCTYPE html>
<html lang="zh-CN-Hans">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="renderer" content="webkit">
    <meta name="csrf-token" content="{{ csrfToken.getToken() }}">
    <title>{{ paper.title }} - 模拟考试</title>
    {% if site_info.favicon %}
        {{ icon_link(site_info.favicon,false) }}
    {% else %}
        {{ icon_link('favicon.ico') }}
    {% endif %}
    {{ css_link('lib/layui/css/layui.css') }}
    {{ css_link('home/css/content.css') }}
    {{ css_link('home/css/common.css') }}
</head>
<body class="main watermark">

{% set manual_grade = request.get('manual_grade') %}
{% set auth_code = request.get('auth_code') %}

{% if manual_grade == 1 %}
    <div class="exam-header wrap">
        <div class="exam-nav">
            <div class="left">
                <span class="title">{{ paper.title }}</span>
            </div>
            <div class="right">
                <div class="submit">
                    <button class="layui-btn layui-btn-normal" id="paper-grade">结束阅卷</button>
                </div>
            </div>
        </div>
    </div>
{% else %}
    <div class="exam-header wrap">
        {% if paper_user.end_time > 0 %}
            <div class="exam-nav">
                <div class="left">
                    <span class="title">{{ paper.title }}</span>
                </div>
                <div class="right">
                    <div class="score">试卷总分：<strong>{{ paper_user.paper_score }}</strong></div>
                    <div class="score">我的得分：<strong>{{ paper_user.user_score }}</strong></div>
                </div>
            </div>
        {% else %}
            <div class="exam-nav">
                <div class="left">
                    <span class="title">{{ paper.title }}</span>
                </div>
                <div class="right">
                    <div class="score">试卷总分：<strong>{{ paper_user.paper_score }}</strong></div>
                    <div class="countdown">倒计时：<span class="timer">00:00</span></div>
                    <div class="submit">
                        <button class="layui-btn layui-btn-normal" id="paper-submit">立即交卷</button>
                    </div>
                </div>
            </div>
        {% endif %}
    </div>
{% endif %}

<div class="exam-main">
    <div class="exam-content">
        <div class="exam-question-list">
            {% for item in questions %}
                {% if item.question_model == 1 %}
                    {{ single_choice(item, paper_user) }}
                {% elseif item.question_model == 2 %}
                    {{ multiple_choice(item, paper_user) }}
                {% elseif item.question_model == 3 %}
                    {{ single_choice(item, paper_user) }}
                {% elseif item.question_model == 4 %}
                    {{ blank_fill(item, paper_user) }}
                {% elseif item.question_model == 5 %}
                    {{ short_answer(item, paper_user) }}
                {% elseif item.question_model == 9 %}
                    {{ complex_question(item, paper_user) }}
                {% endif %}
            {% endfor %}
        </div>
    </div>
    <div class="exam-sidebar">
        <div class="exam-sort-box wrap">
            {% if paper_user.end_time == 0 %}
                {{ exam_on_sorts(questions) }}
            {% else %}
                {{ exam_off_sorts(questions) }}
            {% endif %}
            <div class="exam-sort-pager" style="display:none;"></div>
        </div>
    </div>
</div>

<div class="layui-hide">
    <input type="hidden" name="countdown.end_time" value="{{ paper_user.start_time + paper_user.paper_duration }}">
    <input type="hidden" name="countdown.server_time" value="{{ time() }}">
    <input type="hidden" name="paper_user.id" value="{{ paper_user.id }}">
    <input type="hidden" name="paper_user.over" value="{{ paper_user.end_time > 0 ? 1 : 0 }}">
    <input type="hidden" name="paper_user.auth_code" value="{{ auth_code }}">
    <input type="hidden" name="paper.id" value="{{ paper.id }}">
</div>

{{ partial('partials/js_vars') }}
{{ js_include('lib/layui/layui.js') }}
{{ js_include('home/js/common.js') }}
{{ js_include('home/js/exam.mock.explore.js') }}
{{ js_include('home/js/exam.question.report.js') }}

{% if paper.settings.switch_anti_enabled == 1 %}
    {{ js_include('home/js/exam.monitor.js') }}
{% endif %}

{% if site_info.copy_enabled == 0 %}
    {{ js_include('home/js/copy.block.js') }}
    {{ js_include('home/js/copy.watermark.js') }}
{% endif %}

</body>
</html>
