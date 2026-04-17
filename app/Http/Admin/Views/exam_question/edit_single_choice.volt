{% extends 'templates/main.volt' %}

{% block content %}

    {{ partial('macros/exam_question') }}

    {% set update_url = url({'for':'admin.exam_question.update','id':question.id}) %}

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
                {% for value,title in question.attrs['choices'] %}
                    <div class="layui-form-item">
                        <label class="layui-form-label">选项{{ value }}</label>
                        <div class="layui-input-block">
                            <textarea id="choice-{{ value }}" name="choices[{{ value }}]">{{ title }}</textarea>
                        </div>
                    </div>
                {% endfor %}
                <div class="layui-form-item">
                    <label class="layui-form-label">答案</label>
                    <div class="layui-input-block">
                        {% for value,title in question.attrs['choices'] %}
                            {% set checked = question.answer == value ? 'checked="checked"' : '' %}
                            <input type="radio" name="answer" value="{{ value }}" title="{{ value }}" {{ checked }}>
                        {% endfor %}
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
            editor.choiceA = K.create('#choice-A', editor.options);
            editor.choiceB = K.create('#choice-B', editor.options);
            editor.choiceC = K.create('#choice-C', editor.options);
            editor.choiceD = K.create('#choice-D', editor.options);

            layui.use(['jquery'], function () {

                var $ = layui.jquery;

                $('.kg-submit').on('click', function () {
                    editor.topic.sync();
                    editor.solution.sync();
                    editor.choiceA.sync();
                    editor.choiceB.sync();
                    editor.choiceC.sync();
                    editor.choiceD.sync();
                });
            });
        });
    </script>

{% endblock %}
