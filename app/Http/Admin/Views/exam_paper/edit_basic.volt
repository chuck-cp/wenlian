<form class="layui-form kg-form" method="POST" action="{{ update_url }}">
    <div class="layui-form-item">
        <label class="layui-form-label">测评方式</label>
        <div class="layui-input-block">
            <div class="layui-form-mid layui-word-aux">{{ exam_type(paper.exam_type) }}</div>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">组卷方式</label>
        <div class="layui-input-block">
            <div class="layui-form-mid layui-word-aux">{{ pack_type(paper.pack_type) }}</div>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">试卷封面</label>
        <div class="layui-input-inline">
            <img id="img-cover" class="kg-cover" src="{{ paper.cover }}">
            <input type="hidden" name="cover" value="{{ paper.cover }}">
        </div>
        <div class="layui-input-inline" style="padding-top:35px;">
            <button id="change-cover" class="layui-btn layui-btn-sm" type="button">更换</button>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">试卷标题</label>
        <div class="layui-input-block">
            <input class="layui-input" type="text" name="title" value="{{ paper.title }}" lay-verify="required">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">试卷分类</label>
        <div class="layui-input-block">
            <select name="category_id" lay-search="true">
                <option value="">请选择</option>
                {% for option in category_options %}
                    {% set selected = paper.category_id == option.id ? 'selected="selected"' : '' %}
                    <option value="{{ option.id }}" {{ selected }}>{{ option.name }}</option>
                {% endfor %}
            </select>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">试卷标签</label>
        <div class="layui-input-block">
            <div id="xm-tag-ids"></div>
        </div>
    </div>
    {% if paper.exam_type == 1 %}
        <div class="layui-form-item">
            <label class="layui-form-label">考试时长</label>
            <div class="layui-input-block">
                <select name="duration" lay-verify="required">
                    <option value="">请选择</option>
                    {% for duration in duration_options %}
                        {% set selected = paper.duration == duration ? 'selected="selected"' : '' %}
                        <option value="{{ duration }}" {{ selected }}>{{ duration }}分钟</option>
                    {% endfor %}
                </select>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">责任教师</label>
            <div class="layui-input-block">
                <select name="teacher_id" lay-search="true">
                    <option value="">请选择</option>
                    {% for option in teacher_options %}
                        {% set selected = paper.teacher_id == option.id ? 'selected="selected"' : '' %}
                        <option value="{{ option.id }}" {{ selected }}>{{ option.name }}</option>
                    {% endfor %}
                </select>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">主观题评分</label>
            <div class="layui-input-block">
                {% for value,title in grade_types %}
                    {% set checked = paper.grade_type == value ? 'checked="checked"' : '' %}
                    <input type="radio" name="grade_type" value="{{ value }}" title="{{ title }}" {{ checked }}>
                {% endfor %}
            </div>
        </div>
    {% endif %}
    <div class="layui-form-item">
        <label class="layui-form-label">难度级别</label>
        <div class="layui-input-block">
            {% for value,title in paper_level_types %}
                {% set checked = paper.level == value ? 'checked="checked"' : '' %}
                <input type="radio" name="level" value="{{ value }}" title="{{ title }}" {{ checked }}>
            {% endfor %}
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">推荐</label>
        <div class="layui-input-block">
            <input type="radio" name="featured" value="1" title="是" {% if paper.featured == 1 %}checked="checked"{% endif %}>
            <input type="radio" name="featured" value="0" title="否" {% if paper.featured == 0 %}checked="checked"{% endif %}>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">发布</label>
        <div class="layui-input-block">
            <input type="radio" name="published" value="1" title="是" {% if paper.published == 1 %}checked="checked"{% endif %}>
            <input type="radio" name="published" value="0" title="否" {% if paper.published == 0 %}checked="checked"{% endif %}>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label"></label>
        <div class="layui-input-block">
            <button class="layui-btn" lay-submit="true" lay-filter="go">提交</button>
            <button type="button" class="kg-back layui-btn layui-btn-primary">返回</button>
        </div>
    </div>
</form>
