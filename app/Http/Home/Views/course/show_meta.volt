{%- macro vod_meta_info(course) %}
    <p class="item">
        <span class="key">课程时长</span>
        <span class="value">{{ course.attrs.duration|duration }}</span>
    </p>
    {{ meta_stats_info(course) }}
{%- endmacro %}

{%- macro live_meta_info(course) %}
    <p class="item">
        <span class="key">直播时间</span>
        <span class="value">{{ course.attrs.start_date }} 至 {{ course.attrs.end_date }}</span>
    </p>
    {{ meta_stats_info(course) }}
{%- endmacro %}

{%- macro read_meta_info(course) %}
    <p class="item">
        <span class="key">课程时长</span>
        <span class="value">{{ course.attrs.duration|duration }}</span>
    </p>
    {{ meta_stats_info(course) }}
{%- endmacro %}

{%- macro offline_meta_info(course) %}
    {% set search_url = "https://map.baidu.com/search/%s?querytype=s&wd=%s"|format(course.attrs.location,course.attrs.location) %}
    <p class="item">
        <span class="key">上课时间</span>
        <span class="value">{{ course.attrs.start_date }} ~ {{ course.attrs.end_date }}</span>
    </p>
    <p class="item">
        <span class="key">上课地点</span>
        <span class="value">{{ course.attrs.location }}</span>
        <a class="value" href="{{ search_url }}" title="查看地理位置" target="_blank">
            <i class="layui-icon layui-icon-location"></i>
        </a>
    </p>
    {{ meta_stats_info(course) }}
{%- endmacro %}

{%- macro doc_meta_info(course) %}
    <p class="item">
        <span class="key">课程文档</span>
        <span class="value">{{ course.attrs.size|human_size }}</span>
    </p>
    {{ meta_stats_info(course) }}
{%- endmacro %}

{%- macro meta_stats_info(course) %}
    <p class="item">
        <span class="key">学习人次</span>
        <span class="value">{{ course.user_count }}</span>
    </p>
{%- endmacro %}

<div class="course-meta wrap">
    <div class="cover">
        <span class="model">{{ model_type_badge(course.model) }}</span>
        <img src="{{ course.cover }}" alt="{{ course.title }}">
    </div>
    <div class="info">
        {% if course.model == 1 %}
            {{ vod_meta_info(course) }}
        {% elseif course.model == 2 %}
            {{ live_meta_info(course) }}
        {% elseif course.model == 3 %}
            {{ read_meta_info(course) }}
        {% elseif course.model == 4 %}
            {{ offline_meta_info(course) }}
        {% elseif course.model == 5 %}
            {{ doc_meta_info(course) }}
        {% endif %}
    </div>
</div>
