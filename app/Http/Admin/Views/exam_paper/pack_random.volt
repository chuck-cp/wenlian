{% set single_choice = paper.attrs['conditions']['single_choice'] %}
{% set multiple_choice = paper.attrs['conditions']['multiple_choice'] %}
{% set true_false = paper.attrs['conditions']['true_false'] %}
{% set blank_fill = paper.attrs['conditions']['blank_fill'] %}
{% set short_answer = paper.attrs['conditions']['short_answer'] %}
{% set complex_question = paper.attrs['conditions']['complex_question'] %}

<form class="layui-form kg-form" method="POST" action="{{ url({'for':'admin.exam_paper_question.random'}) }}">
    <div class="layui-form-item">
        <label class="layui-form-label">试题分类</label>
        <div class="layui-input-block">
            <div id="xm-category-ids"></div>
        </div>
    </div>
    <div class="layui-form-item">
        <div class="layui-inline">
            <label class="layui-form-label">单选题数量</label>
            <div class="layui-input-inline">
                <select class="model" name="single_choice[limit]">
                    <option value="0">请选择</option>
                    {% for value in 1..100 %}
                        {% if value == single_choice['limit'] %}
                            <option value="{{ value }}" selected="selected">{{ value }}</option>
                        {% else %}
                            <option value="{{ value }}">{{ value }}</option>
                        {% endif %}
                    {% endfor %}
                </select>
            </div>
        </div>
        <div class="layui-inline">
            <label class="layui-form-label">试题难度</label>
            <div class="layui-input-inline" style="width:300px;">
                {% for value,title in question_level_types %}
                    {% if value in single_choice['level'] %}
                        <input type="checkbox" name="single_choice[level][]" value="{{ value }}" title="{{ title }}" checked="checked">
                    {% else %}
                        <input type="checkbox" name="single_choice[level][]" value="{{ value }}" title="{{ title }}">
                    {% endif %}
                {% endfor %}
            </div>
        </div>
        <div class="layui-inline kg-meet" style="display:none;">
            <label class="layui-form-label">满足条件</label>
            <div class="layui-input-inline">
                <div class="layui-form-mid layui-text" id="single-choice-meet">N/A</div>
            </div>
        </div>
    </div>
    <div class="layui-form-item">
        <div class="layui-inline">
            <label class="layui-form-label">多选题数量</label>
            <div class="layui-input-inline">
                <select class="model" name="multiple_choice[limit]">
                    <option value="0">请选择</option>
                    {% for value in 1..30 %}
                        {% if value == multiple_choice['limit'] %}
                            <option value="{{ value }}" selected="selected">{{ value }}</option>
                        {% else %}
                            <option value="{{ value }}">{{ value }}</option>
                        {% endif %}
                    {% endfor %}
                </select>
            </div>
        </div>
        <div class="layui-inline">
            <label class="layui-form-label">试题难度</label>
            <div class="layui-input-inline" style="width:300px;">
                {% for value,title in question_level_types %}
                    {% if value in multiple_choice['level'] %}
                        <input type="checkbox" name="multiple_choice[level][]" value="{{ value }}" title="{{ title }}" checked="checked">
                    {% else %}
                        <input type="checkbox" name="multiple_choice[level][]" value="{{ value }}" title="{{ title }}">
                    {% endif %}
                {% endfor %}
            </div>
        </div>
        <div class="layui-inline kg-meet" style="display:none;">
            <label class="layui-form-label">满足条件</label>
            <div class="layui-input-inline">
                <div class="layui-form-mid layui-text" id="multiple-choice-meet">N/A</div>
            </div>
        </div>
    </div>
    <div class="layui-form-item">
        <div class="layui-inline">
            <label class="layui-form-label">判断题数量</label>
            <div class="layui-input-inline">
                <select class="model" name="true_false[limit]">
                    <option value="0">请选择</option>
                    {% for value in 1..30 %}
                        {% if value == true_false['limit'] %}
                            <option value="{{ value }}" selected="selected">{{ value }}</option>
                        {% else %}
                            <option value="{{ value }}">{{ value }}</option>
                        {% endif %}
                    {% endfor %}
                </select>
            </div>
        </div>
        <div class="layui-inline">
            <label class="layui-form-label">试题难度</label>
            <div class="layui-input-inline" style="width:300px;">
                {% for value,title in question_level_types %}
                    {% if value in true_false['level'] %}
                        <input type="checkbox" name="true_false[level][]" value="{{ value }}" title="{{ title }}" checked="checked">
                    {% else %}
                        <input type="checkbox" name="true_false[level][]" value="{{ value }}" title="{{ title }}">
                    {% endif %}
                {% endfor %}
            </div>
        </div>
        <div class="layui-inline kg-meet" style="display:none;">
            <label class="layui-form-label">满足条件</label>
            <div class="layui-input-inline">
                <div class="layui-form-mid layui-text" id="true-false-meet">N/A</div>
            </div>
        </div>
    </div>
    <div class="layui-form-item">
        <div class="layui-inline">
            <label class="layui-form-label">填空题数量</label>
            <div class="layui-input-inline">
                <select class="model" name="blank_fill[limit]">
                    <option value="0">请选择</option>
                    {% for value in 1..30 %}
                        {% if value == blank_fill['limit'] %}
                            <option value="{{ value }}" selected="selected">{{ value }}</option>
                        {% else %}
                            <option value="{{ value }}">{{ value }}</option>
                        {% endif %}
                    {% endfor %}
                </select>
            </div>
        </div>
        <div class="layui-inline">
            <label class="layui-form-label">试题难度</label>
            <div class="layui-input-inline" style="width:300px;">
                {% for value,title in question_level_types %}
                    {% if value in blank_fill['level'] %}
                        <input type="checkbox" name="blank_fill[level][]" value="{{ value }}" title="{{ title }}" checked="checked">
                    {% else %}
                        <input type="checkbox" name="blank_fill[level][]" value="{{ value }}" title="{{ title }}">
                    {% endif %}
                {% endfor %}
            </div>
        </div>
        <div class="layui-inline kg-meet" style="display:none;">
            <label class="layui-form-label">满足条件</label>
            <div class="layui-input-inline">
                <div class="layui-form-mid layui-text" id="blank-fill-meet">N/A</div>
            </div>
        </div>
    </div>
    <div class="layui-form-item">
        <div class="layui-inline">
            <label class="layui-form-label">简答题数量</label>
            <div class="layui-input-inline">
                <select class="model" name="short_answer[limit]">
                    <option value="0">请选择</option>
                    {% for value in 1..30 %}
                        {% if value == short_answer['limit'] %}
                            <option value="{{ value }}" selected="selected">{{ value }}</option>
                        {% else %}
                            <option value="{{ value }}">{{ value }}</option>
                        {% endif %}
                    {% endfor %}
                </select>
            </div>
        </div>
        <div class="layui-inline">
            <label class="layui-form-label">试题难度</label>
            <div class="layui-input-inline" style="width:300px;">
                {% for value,title in question_level_types %}
                    {% if value in short_answer['level'] %}
                        <input type="checkbox" name="short_answer[level][]" value="{{ value }}" title="{{ title }}" checked="checked">
                    {% else %}
                        <input type="checkbox" name="short_answer[level][]" value="{{ value }}" title="{{ title }}">
                    {% endif %}
                {% endfor %}
            </div>
        </div>
        <div class="layui-inline kg-meet" style="display:none;">
            <label class="layui-form-label">满足条件</label>
            <div class="layui-input-inline">
                <div class="layui-form-mid layui-text" id="short-answer-meet">N/A</div>
            </div>
        </div>
    </div>
    <div class="layui-form-item">
        <div class="layui-inline">
            <label class="layui-form-label">题帽题数量</label>
            <div class="layui-input-inline">
                <select class="model" name="complex_question[limit]">
                    <option value="0">请选择</option>
                    {% for value in 1..30 %}
                        {% if value == complex_question['limit'] %}
                            <option value="{{ value }}" selected="selected">{{ value }}</option>
                        {% else %}
                            <option value="{{ value }}">{{ value }}</option>
                        {% endif %}
                    {% endfor %}
                </select>
            </div>
        </div>
        <div class="layui-inline">
            <label class="layui-form-label">试题难度</label>
            <div class="layui-input-inline" style="width:300px;">
                {% for value,title in question_level_types %}
                    {% if value in complex_question['level'] %}
                        <input type="checkbox" name="complex_question[level][]" value="{{ value }}" title="{{ title }}" checked="checked">
                    {% else %}
                        <input type="checkbox" name="complex_question[level][]" value="{{ value }}" title="{{ title }}">
                    {% endif %}
                {% endfor %}
            </div>
        </div>
        <div class="layui-inline kg-meet" style="display:none;">
            <label class="layui-form-label">满足条件</label>
            <div class="layui-input-inline">
                <div class="layui-form-mid layui-text" id="complex-question-meet">N/A</div>
            </div>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label"></label>
        <div class="layui-input-block">
            <button class="layui-btn" lay-submit="true" lay-filter="rand_package">提交</button>
            <button type="button" class="kg-back layui-btn layui-btn-primary">返回</button>
            <input type="hidden" name="paper_id" value="{{ paper.id }}">
        </div>
    </div>
</form>