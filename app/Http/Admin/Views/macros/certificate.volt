{%- macro item_type(value) %}
    {% if value == 1 %}
        课程
    {% elseif value == 4 %}
        考试
    {% elseif value == 6 %}
        专题
    {% else %}
        N/A
    {% endif %}
{%- endmacro %}

{%- macro grant_type(value) %}
    {% if value == 1 %}
        自动
    {% elseif value == 2 %}
        人工
    {% else %}
        N/A
    {% endif %}
{%- endmacro %}

{%- macro item_info(type,info) %}
    {% if type == 1 %}
        {% set course = info.course %}
        {% set url = url({'for':'home.course.show','id':course.id}) %}
        <p>课程：<a href="{{ url }}" target="_blank">{{ course.title }}</a>（{{ course.id }}）</p>
    {% elseif type == 4 %}
        {% set exam_paper = info.exam_paper %}
        {% set url = url({'for':'home.exam_paper.show','id':exam_paper.id}) %}
        <p>考试：<a href="{{ url }}" target="_blank">{{ exam_paper.title }}</a>（{{ exam_paper.id }}）</p>
    {% elseif type == 6 %}
        {% set topic = info.topic %}
        {% set url = url({'for':'home.topic.show','id':topic.id}) %}
        <p>专题：<a href="{{ url }}" target="_blank">{{ topic.title }}</a>（{{ topic.id }}）</p>
    {% endif %}
{%- endmacro %}