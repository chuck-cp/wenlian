{{ partial('macros/exam_question') }}

{%- macro answer_analysis(question) %}
    <div class="row-list primary">
        <div class="row">
            <div class="label">我的得分：</div>
            <div class="content score"></div>
        </div>
        <div class="row">
            <div class="label">正确答案：</div>
            <div class="content ke-content kg-zoom">{{ question.answer }}</div>
        </div>
        {% if question.pass_rate > 0 %}
            <div class="row">
                <div class="label">通过率：</div>
                <div class="content pass-rate">{{ 100 * question.pass_rate }}%</div>
            </div>
        {% endif %}
        {% if question.solution %}
            <div class="row">
                <div class="label">答案解析：</div>
                <div class="content ke-content kg-zoom">{{ question.solution }}</div>
            </div>
        {% endif %}
    </div>
    <div class="report layui-badge layui-btn-danger" data-id="{{ question.id }}">挑错</div>
{%- endmacro %}

{%- macro single_choice(question, sn) %}
    {% set star_class = question.me.favorited == 1 ? 'layui-icon-star-fill' : 'layui-icon-star' %}
    <div class="exam-question wrap" id="question-{{ question.id }}">
        {% if question.parent_id == 0 %}
            <div class="favorite" data-id="{{ question.id }}"><i class="layui-icon {{ star_class }}"></i> 收藏</div>
        {% endif %}
        <div class="meta">{{ sn }}. {{ model_type(question.model) }}（{{ question.score }}分）</div>
        <div class="topic ke-content kg-zoom">{{ question.topic }}</div>
        <div class="special-box">
            <div class="choice-list">
                {% for choice,content in question.attrs.choices %}
                    <div class="choice single-choice" data-choice="{{ choice }}" data-sn="{{ sn }}" data-id="{{ question.id }}" data-pid="{{ question.parent_id }}" data-model="{{ question.model }}">
                        <div class="circle">{{ choice }}</div>
                        <div class="content">{{ content }}</div>
                    </div>
                {% endfor %}
            </div>
        </div>
        <div class="analysis-box layui-hide">{{ answer_analysis(question) }}</div>
    </div>
{%- endmacro %}

{%- macro multiple_choice(question, sn) %}
    {% set star_class = question.me.favorited == 1 ? 'layui-icon-star-fill' : 'layui-icon-star' %}
    <div class="exam-question wrap" id="question-{{ question.id }}">
        {% if question.parent_id == 0 %}
            <div class="favorite" data-id="{{ question.id }}"><i class="layui-icon {{ star_class }}"></i> 收藏</div>
        {% endif %}
        <div class="meta">{{ sn }}. {{ model_type(question.model) }}（{{ question.score }}分）</div>
        <div class="topic ke-content kg-zoom">{{ question.topic }}</div>
        <div class="special-box">
            <div class="choice-list">
                {% for choice,content in question.attrs.choices %}
                    <div class="choice multiple-choice" data-choice="{{ choice }}" data-sn="{{ sn }}" data-id="{{ question.id }}" data-pid="{{ question.parent_id }}" data-model="{{ question.model }}">
                        <div class="circle">{{ choice }}</div>
                        <div class="content">{{ content }}</div>
                    </div>
                {% endfor %}
            </div>
        </div>
        <div class="compare-box">
            <button class="layui-btn layui-btn-danger check-answer" data-sn="{{ sn }}" data-id="{{ question.id }}" data-pid="{{ question.parent_id }}" data-model="{{ question.model }}">比对答案</button>
            <input type="hidden" id="answer-{{ question.id }}">
        </div>
        <div class="analysis-box layui-hide">{{ answer_analysis(question) }}</div>
    </div>
{%- endmacro %}

{%- macro blank_fill(question, sn) %}
    {% set star_class = question.me.favorited == 1 ? 'layui-icon-star-fill' : 'layui-icon-star' %}
    {% set org_answers = question.answer|split(',') %}
    <div class="exam-question wrap" id="question-{{ question.id }}">
        {% if question.parent_id == 0 %}
            <div class="favorite" data-id="{{ question.id }}"><i class="layui-icon {{ star_class }}"></i> 收藏</div>
        {% endif %}
        <div class="meta">{{ sn }}. {{ model_type(question.model) }}（{{ question.score }}分）</div>
        <div class="topic ke-content kg-zoom">{{ question.topic }}</div>
        <div class="special-box">
            <div class="blank-fill-list" id="blank-fill-{{ question.id }}">
                {% for key,value in org_answers %}
                    <div class="blank-fill layui-form-item">
                        <div class="layui-form-label">填空{{ loop.index }}</div>
                        <div class="layui-input-block">
                            <input class="layui-input blank-fill-input" type="text" data-id="{{ question.id }}">
                        </div>
                    </div>
                {% endfor %}
            </div>
        </div>
        <div class="compare-box">
            <button class="layui-btn layui-btn-danger check-answer" data-sn="{{ sn }}" data-id="{{ question.id }}" data-pid="{{ question.parent_id }}" data-model="{{ question.model }}">比对答案</button>
            <input type="hidden" id="answer-{{ question.id }}">
        </div>
        <div class="analysis-box layui-hide">{{ answer_analysis(question) }}</div>
    </div>
{%- endmacro %}

{%- macro short_answer(question, sn) %}
    {% set star_class = question.me.favorited == 1 ? 'layui-icon-star-fill' : 'layui-icon-star' %}
    <div class="exam-question wrap" id="question-{{ question.id }}">
        {% if question.parent_id == 0 %}
            <div class="favorite" data-id="{{ question.id }}"><i class="layui-icon {{ star_class }}"></i> 收藏</div>
        {% endif %}
        <div class="meta">{{ sn }}. {{ model_type(question.model) }}（{{ question.score }}分）</div>
        <div class="topic ke-content kg-zoom">{{ question.topic }}</div>
        <div class="special-box">
            <div class="short-answer">
                <textarea class="layui-textarea short-answer-textarea" data-id="{{ question.id }}"></textarea>
            </div>
        </div>
        <div class="compare-box">
            <button class="layui-btn layui-btn-danger check-answer" data-sn="{{ sn }}" data-id="{{ question.id }}" data-pid="{{ question.parent_id }}" data-model="{{ question.model }}">比对答案</button>
            <input type="hidden" id="answer-{{ question.id }}">
        </div>
        <div class="analysis-box layui-hide">{{ answer_analysis(question) }}</div>
    </div>
{%- endmacro %}

{%- macro complex_question(question, sn) %}
    {% set star_class = question.me.favorited == 1 ? 'layui-icon-star-fill' : 'layui-icon-star' %}
    <div class="exam-question wrap" id="question-{{ question.id }}">
        <div class="favorite" data-id="{{ question.id }}"><i class="layui-icon {{ star_class }}"></i> 收藏</div>
        <div class="meta">{{ sn }}. {{ model_type(question.model) }}（{{ question.score }}分）</div>
        <div class="topic ke-content kg-zoom">{{ question.topic }}</div>
        <div class="complex-question-list">
            {% for child in question.children %}
                {% if child.model == 1 %}
                    {{ single_choice(child, loop.index) }}
                {% elseif child.model == 2 %}
                    {{ multiple_choice(child, loop.index) }}
                {% elseif child.model == 3 %}
                    {{ single_choice(child, loop.index) }}
                {% elseif child.model == 4 %}
                    {{ blank_fill(child, loop.index) }}
                {% elseif child.model == 5 %}
                    {{ short_answer(child, loop.index) }}
                {% endif %}
            {% endfor %}
        </div>
    </div>
{%- endmacro %}

{% if question.model == 1 %}
    {{ single_choice(question, sn) }}
{% elseif question.model == 2 %}
    {{ multiple_choice(question, sn) }}
{% elseif question.model == 3 %}
    {{ single_choice(question, sn) }}
{% elseif question.model == 4 %}
    {{ blank_fill(question, sn) }}
{% elseif question.model == 5 %}
    {{ short_answer(question, sn) }}
{% elseif question.model == 9 %}
    {{ complex_question(question, sn) }}
{% endif %}
