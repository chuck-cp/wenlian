{% set child_flag = question.parent_id > 0 ? '（子题）' : '' %}

<div class="layui-form-item">
    <label class="layui-form-label">题型</label>
    <div class="layui-input-block">
        <div class="layui-form-mid layui-text">{{ model_type(question.model) }} {{ child_flag }}</div>
    </div>
</div>
{% if question.model == 9 %}
    <div class="layui-form-item">
        <label class="layui-form-label">分值</label>
        <div class="layui-input-block">
            <div class="layui-form-mid layui-text">{{ question.score }}</div>
        </div>
    </div>
{% else %}
    <div class="layui-form-item">
        <label class="layui-form-label">分值</label>
        <div class="layui-input-block">
            <select name="score" lay-verify="required">
                <option value="">请选择</option>
                {% for value in 1..30 %}
                    {% set selected = question.score == value ? 'selected="selected"' : '' %}
                    <option value="{{ value }}" {{ selected }}>{{ value }}</option>
                {% endfor %}
            </select>
        </div>
    </div>
{% endif %}
{% if question.parent_id == 0 %}
    <div class="layui-form-item">
        <label class="layui-form-label">分类</label>
        <div class="layui-input-block">
            <select name="category_id" lay-search="true">
                <option value="">请选择</option>
                {% for option in category_options %}
                    {% set selected = question.category_id == option.id ? 'selected="selected"' : '' %}
                    <option value="{{ option.id }}" {{ selected }}>{{ option.name }}</option>
                {% endfor %}
            </select>
        </div>
    </div>
{% endif %}
<div class="layui-form-item">
    <label class="layui-form-label">难度</label>
    <div class="layui-input-block">
        <select name="level" lay-verify="required">
            <option value="">请选择</option>
            {% for value,title in level_types %}
                {% set selected = question.level == value ? 'selected="selected"' : '' %}
                <option value="{{ value }}" {{ selected }}>{{ title }}</option>
            {% endfor %}
        </select>
    </div>
</div>
{% if question.parent_id > 0 %}
    <div class="layui-form-item">
        <label class="layui-form-label">排序</label>
        <div class="layui-input-block">
            <input class="layui-input" type="text" name="priority" value="{{ question.priority }}" lay-verify="number">
        </div>
    </div>
{% endif %}
<div class="layui-form-item">
    <label class="layui-form-label">发布</label>
    <div class="layui-input-block">
        <input type="radio" name="published" value="1" title="是" {% if question.published == 1 %}checked="checked"{% endif %}>
        <input type="radio" name="published" value="0" title="否" {% if question.published == 0 %}checked="checked"{% endif %}>
    </div>
</div>
<div class="layui-form-item">
    <label class="layui-form-label"></label>
    <div class="layui-input-block">
        <button class="kg-submit layui-btn" lay-submit="true" lay-filter="go">提交</button>
        <button type="button" class="kg-back layui-btn layui-btn-primary">返回</button>
    </div>
</div>