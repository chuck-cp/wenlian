{% extends 'templates/main.volt' %}

{% block content %}

    {{ partial('macros/exam_question') }}

    {% set update_url = url({'for':'admin.exam_question.update','id':question.id}) %}

    <fieldset class="layui-elem-field layui-field-title">
        <legend>编辑试题</legend>
    </fieldset>

    <form class="layui-form" method="POST" action="{{ update_url }}">
        <div class="layui-row">
            <div class="layui-col-md8">
                <div class="layui-form-item">
                    <label class="layui-form-label">题帽</label>
                    <div class="layui-input-block">
                        <textarea id="topic" name="topic">{{ question.topic }}</textarea>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">子题</label>
                    <div class="layui-input-block">
                        <table class="layui-table">
                            <tr>
                                <td width="50%">题干</td>
                                <td>题型</td>
                                <td>分数</td>
                                <td>排序</td>
                                <td width="16%">操作</td>
                            </tr>
                            {% for item in child_questions %}
                                {% set edit_url = url({'for':'admin.exam_question.edit','id':item.id}) %}
                                {% set delete_url = url({'for':'admin.exam_question.delete','id':item.id}) %}
                                <tr>
                                    <td>{{ item.topic }}</td>
                                    <td>{{ model_type(item.model) }}</td>
                                    <td>{{ item.score }}</td>
                                    <td>{{ item.priority }}</td>
                                    <td class="center">
                                        <a href="{{ edit_url }}" class="layui-btn layui-btn-xs layui-bg-blue">编辑</a>
                                        <a href="javascript:" class="layui-btn layui-btn-xs layui-btn-danger kg-delete" data-url="{{ delete_url }}">删除</a>
                                    </td>
                                </tr>
                            {% endfor %}
                        </table>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">动作</label>
                    <div class="layui-inline">
                        <select name="child_model">
                            <option value="">请选择题型</option>
                            {% for value,title in model_types %}
                                {% if value != 9 %}
                                    <option value="{{ value }}">{{ title }}</option>
                                {% endif %}
                            {% endfor %}
                        </select>
                    </div>
                    <div class="layui-inline">
                        <span class="layui-btn layui-bg-blue" id="add-child-question">添加子题</span>
                    </div>
                </div>
            </div>
            <div class="layui-col-md4">
                {{ partial('exam_question/edit_basic') }}
            </div>
        </div>
    </form>

{% endblock %}

{% block include_js %}

    {{ js_include('lib/kindeditor/kindeditor.min.js') }}
    {{ js_include('admin/js/exam.question.editor.js') }}

{% endblock %}

{% block inline_js %}

    <script>
        KindEditor.ready(function (K) {

            editor.options.width = '800px';
            editor.topic = K.create('#topic', editor.options);

            layui.use(['jquery'], function () {

                var $ = layui.jquery;

                $('.kg-submit').on('click', function () {
                    editor.topic.sync();
                });

                $('#add-child-question').on('click', function () {
                    var model = $('select[name=child_model]').val();
                    if (model === '') return false;
                    $.ajax({
                        method: 'POST',
                        url: '/admin/exam/question/create',
                        data: {
                            parent_id: {{ question.id }},
                            model: model,
                        },
                        success: function (res) {
                            window.location.href = res.location;
                        }
                    });
                    return false;
                });
            });
        });
    </script>

{% endblock %}
