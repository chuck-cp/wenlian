{% extends 'templates/main.volt' %}

{% block content %}

    {{ partial('macros/exam_question') }}

    {% set topic = request.get('topic','string','') %}
    {% set model = request.get('model','int','') %}
    {% set level = request.get('level','int','') %}
    {% set category_id = request.get('category_id','int','') %}

    <form class="layui-form" method="GET" action="{{ url({'for':'admin.exam_question.filter'}) }}">
        <div class="layui-inline">
            <div class="layui-form-item">
                <div class="layui-input-inline">
                    <input class="layui-input" type="text" name="topic" value="{{ topic }}" placeholder="题干">
                </div>
            </div>
        </div>
        <div class="layui-inline">
            <div class="layui-form-item">
                <div class="layui-input-inline">
                    <select name="category_id">
                        <option value="">分类</option>
                        {% for option in category_options %}
                            {% set selected = option.id == category_id ? 'selected="selected"' : '' %}
                            <option value="{{ option.id }}" {{ selected }}>{{ option.name }}</option>
                        {% endfor %}
                    </select>
                </div>
            </div>
        </div>
        <div class="layui-inline">
            <div class="layui-form-item">
                <div class="layui-input-inline">
                    <select name="model">
                        <option value="">题型</option>
                        {% for value,title in model_types %}
                            {% set selected = value == model ? 'selected="selected"' : '' %}
                            <option value="{{ value }}" {{ selected }}>{{ title }}</option>
                        {% endfor %}
                    </select>
                </div>
            </div>
        </div>
        <div class="layui-inline">
            <div class="layui-form-item">
                <div class="layui-input-inline">
                    <select name="level">
                        <option value="">难度</option>
                        {% for value,title in level_types %}
                            {% set selected = value == level ? 'selected="selected"' : '' %}
                            <option value="{{ value }}" {{ selected }}>{{ title }}</option>
                        {% endfor %}
                    </select>
                </div>
            </div>
        </div>
        <div class="layui-inline">
            <div class="layui-form-item">
                <button class="layui-btn layui-btn-primary" type="reset">重置</button>
                <button class="layui-btn layui-btn-normal" lay-submit="true">搜索</button>
            </div>
        </div>
    </form>

    <table class="layui-table" lay-skin="line">
        <colgroup>
            <col width="5%">
            <col>
            <col>
            <col>
            <col>
            <col>
            <col width="60%">
        </colgroup>
        <thead>
        <tr>
            <th><input id="check-all" type="checkbox"></th>
            <th>编号</th>
            <th>分类</th>
            <th>题型</th>
            <th>难度</th>
            <th>分值</th>
            <th>题干</th>
        </tr>
        </thead>
        <tbody>
        {% for item in pager.items %}
            <tr>
                <td><input class="check-item" type="checkbox" value="{{ item.id }}"></td>
                <td>{{ item.id }}</td>
                <td>
                    {% if item.category.id is defined %}
                        {{ item.category.name }}
                    {% else %}
                        N/A
                    {% endif %}
                </td>
                <td>{{ model_type(item.model) }}</td>
                <td>{{ level_type(item.level) }}</td>
                <td>{{ item.score }}</td>
                <td>{{ item.topic }}</td>
            </tr>
        {% endfor %}
        </tbody>
    </table>

    {{ partial('partials/pager') }}

{% endblock %}

{% block inline_js %}

    <script>

        layui.use(['jquery', 'layer'], function () {

            var $ = layui.jquery;

            var $checkAll = $('#check-all');
            var $checkItems = $('.check-item');

            $checkAll.on('click', function () {
                var checked = $(this).is(':checked');
                layui.each($checkItems, function (index, elem) {
                    $(elem).prop('checked', checked);
                });
                setQuestionIds();
            });

            $checkItems.on('click', function () {
                var allChecked = $('.check-item:not(:checked)').length === 0;
                $checkAll.prop('checked', allChecked);
                setQuestionIds();
            });

            var setQuestionIds = function () {
                var questionIds = [];
                layui.each($checkItems, function (index, elem) {
                    if ($(elem).is(':checked')) {
                        questionIds.push($(elem).val());
                    }
                });
                if (questionIds.length > 0) {
                    parent.layui.$('input[name=question_ids]').val(questionIds.join(','));
                }
            };

        });

    </script>

{% endblock %}