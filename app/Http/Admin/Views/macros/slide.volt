{%- macro target_info(value) %}
    {% if value == 1 %}
        课程
    {% elseif value == 2 %}
        单页
    {% elseif value == 3 %}
        链接
    {% elseif value == 4 %}
        试卷
    {% elseif value == 5 %}
        专栏
    {% else %}
        N/A
    {% endif %}
{%- endmacro %}

{%- macro target_attrs_info(value) %}
    {% if value.exam_paper is defined %}
        {% set url = url({'for':'home.exam_paper.show','id':value.exam_paper.id}) %}
        <a href="{{ url }}" target="_blank">{{ value.exam_paper.title }}</a>（{{ value.exam_paper.id }}）
    {% elseif value.course is defined %}
        {% set url = url({'for':'home.course.show','id':value.course.id}) %}
        <a href="{{ url }}" target="_blank">{{ value.course.title }}</a>（{{ value.course.id }}）
    {% elseif value.article is defined %}
        {% set url = url({'for':'home.article.show','id':value.article.id}) %}
        <a href="{{ url }}" target="_blank">{{ value.article.title }}</a>（{{ value.article.id }}）
    {% elseif value.page is defined %}
        {% set url = url({'for':'home.page.show','id':value.page.id}) %}
        <a href="{{ url }}" target="_blank">{{ value.page.title }}</a>（{{ value.page.id }}）
    {% elseif value.link is defined %}
        <a href="{{ value.link.url }}" target="_blank">{{ value.link.url }}</a>
    {% endif %}
{%- endmacro %}