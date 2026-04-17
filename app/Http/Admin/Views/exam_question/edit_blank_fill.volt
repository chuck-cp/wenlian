{% extends 'templates/main.volt' %}

{% block content %}

    {{ partial('macros/exam_question') }}

    {% set update_url = url({'for':'admin.exam_question.update','id':question.id}) %}
    {% set answers = question.answer|split(',') %}

    <fieldset class="layui-elem-field layui-field-title">
        <legend>编辑试题</legend>
    </fieldset>

    <form class="layui-form" method="POST" action="{{ update_url }}">
        <div class="layui-row">
            <div class="layui-col-md6">
                <div class="layui-form-item">
                    <label class="layui-form-label">题干</label>
                    <div class="layui-input-block">
                        <textarea id="topic" name="topic">{{ question.topic }}</textarea>
                    </div>
                </div>
                <div id="answer-block">
                    {% for answer in answers %}
                        <div class="layui-form-item" id="answer-{{ loop.index }}">
                            <label class="layui-form-label">空 {{ loop.index }} 答案</label>
                            <div class="layui-input-block">
                                <input class="layui-input" type="text" name="answer[]" value="{{ answer }}" lay-verify="required">
                            </div>
                        </div>
                    {% endfor %}
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label"></label>
                    <div class="layui-input-block">
                        <a id="add-answer" href="javascript:"><i class="layui-icon layui-icon-add-1"></i>增加</a>
                        <a id="delete-answer" href="javascript:"><i class="layui-icon layui-icon-delete"></i>删除</a>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">解析</label>
                    <div class="layui-input-block">
                        <textarea id="solution" name="solution">{{ question.solution }}</textarea>
                    </div>
                </div>
            </div>
            <div class="layui-col-md6">
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

            editor.topic = K.create('#topic', editor.options);
            editor.solution = K.create('#solution', editor.options);

            layui.use(['jquery'], function () {

                var $ = layui.jquery;

                $('.kg-submit').on('click', function () {
                    editor.topic.sync();
                    editor.solution.sync();
                });

                $('#add-answer').on('click', function () {
                    var index = $('#answer-block>.layui-form-item').length + 1;
                    if (index > 4) return false;
                    var html = '<div class="layui-form-item" id="answer-' + index + '">';
                    html += '<label class="layui-form-label">空 ' + index + ' 答案</label>';
                    html += '<div class="layui-input-block">';
                    html += '<input class="layui-input" type="text" name="answer[]" lay-verify="required">';
                    html += '</div>';
                    html += '</div>';
                    $('#answer-block').append(html);
                });

                $('#delete-answer').on('click', function () {
                    var length = $('#answer-block>.layui-form-item').length;
                    if (length < 2) return false;
                    $('#answer-block>.layui-form-item:last-child').remove();
                });
            });
        });
    </script>

{% endblock %}
