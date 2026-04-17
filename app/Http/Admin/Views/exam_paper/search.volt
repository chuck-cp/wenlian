{% extends 'templates/main.volt' %}

{% block content %}

    <form class="layui-form kg-form" method="GET" action="{{ url({'for':'admin.exam_paper.list'}) }}">
        <fieldset class="layui-elem-field layui-field-title">
            <legend>搜索试卷</legend>
        </fieldset>
        <div class="layui-form-item">
            <label class="layui-form-label">试卷编号</label>
            <div class="layui-input-block">
                <input class="layui-input" type="text" name="id" placeholder="编号精确匹配">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">试卷标题</label>
            <div class="layui-input-block">
                <input class="layui-input" type="text" name="title" placeholder="标题模糊匹配">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">试卷分类</label>
            <div class="layui-input-block">
                <select name="category_id">
                    <option value="">请选择</option>
                    {% for option in category_options %}
                        <option value="{{ option.id }}">{{ option.name }}</option>
                    {% endfor %}
                </select>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">责任教师</label>
            <div class="layui-input-block">
                <select name="teacher_id">
                    <option value="">请选择</option>
                    {% for option in teacher_options %}
                        <option value="{{ option.id }}">{{ option.name }}</option>
                    {% endfor %}
                </select>
            </div>
        </div>
        <div class="layui-form-item" id="time-range">
            <label class="layui-form-label">创建时间</label>
            <div class="layui-input-inline">
                <input class="layui-input" id="start-time" type="text" name="create_time[]" autocomplete="off">
            </div>
            <div class="layui-form-mid">-</div>
            <div class="layui-input-inline">
                <input class="layui-input" id="end-time" type="text" name="create_time[]" autocomplete="off">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">测评方式</label>
            <div class="layui-input-block">
                {% for value,title in exam_types %}
                    <input type="checkbox" name="exam_type[]" value="{{ value }}" title="{{ title }}">
                {% endfor %}
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">组卷方式</label>
            <div class="layui-input-block">
                {% for value,title in pack_types %}
                    <input type="checkbox" name="pack_type[]" value="{{ value }}" title="{{ title }}">
                {% endfor %}
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">主观题评分</label>
            <div class="layui-input-block">
                {% for value,title in grade_types %}
                    <input type="checkbox" name="grade_type[]" value="{{ value }}" title="{{ title }}">
                {% endfor %}
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">难度</label>
            <div class="layui-input-block">
                {% for value,title in level_types %}
                    <input type="checkbox" name="level[]" value="{{ value }}" title="{{ title }}">
                {% endfor %}
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">推荐</label>
            <div class="layui-input-block">
                <input type="radio" name="featured" value="1" title="是">
                <input type="radio" name="featured" value="0" title="否">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">发布</label>
            <div class="layui-input-block">
                <input type="radio" name="published" value="1" title="是">
                <input type="radio" name="published" value="0" title="否">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">删除</label>
            <div class="layui-input-block">
                <input type="radio" name="deleted" value="1" title="是">
                <input type="radio" name="deleted" value="0" title="否">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label"></label>
            <div class="layui-input-block">
                <button class="layui-btn" lay-submit="true">提交</button>
                <button type="button" class="kg-back layui-btn layui-btn-primary">返回</button>
            </div>
        </div>
    </form>

{% endblock %}

{% block inline_js %}

    <script>

        layui.use(['laydate'], function () {

            var laydate = layui.laydate;

            laydate.render({
                elem: '#time-range',
                type: 'datetime',
                range: ['#start-time', '#end-time'],
            });

        });

    </script>

{% endblock %}
