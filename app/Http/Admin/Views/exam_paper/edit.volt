{% extends 'templates/main.volt' %}

{% block content %}

    {{ partial('macros/exam_paper') }}
    {{ partial('macros/exam_question') }}

    {% set update_url = url({'for':'admin.exam_paper.update','id':paper.id}) %}

    <fieldset class="layui-elem-field layui-field-title">
        <legend>编辑试卷</legend>
    </fieldset>

    <div class="layui-tabs">
        <ul class="layui-tabs-header">
            <li class="layui-this">基本信息</li>
            <li>内容介绍</li>
            <li>营销设置</li>
            <li>组卷信息</li>
        </ul>
        <div class="layui-tabs-body">
            <div class="layui-tabs-item layui-show">
                {{ partial('exam_paper/edit_basic') }}
            </div>
            <div class="layui-tabs-item">
                {{ partial('exam_paper/edit_desc') }}
            </div>
            <div class="layui-tabs-item">
                {{ partial('exam_paper/edit_sale') }}
            </div>
            <div class="layui-tabs-item">
                {{ partial('exam_paper/edit_pack') }}
            </div>
        </div>
    </div>

    <div class="layui-hide">
        <input type="hidden" name="pack_type" value="{{ paper.pack_type }}">
        <input type="hidden" name="paper_id" value="{{ paper.id }}">
        <input type="hidden" name="question_ids">
    </div>

{% endblock %}

{% block include_js %}

    {{ js_include('lib/xm-select.js') }}
    {{ js_include('lib/kindeditor/kindeditor.min.js') }}
    {{ js_include('admin/js/content.editor.js') }}
    {{ js_include('admin/js/cover.upload.js') }}

{% endblock %}

{% block inline_js %}

    <script>
        xmSelect.render({
            el: '#xm-tag-ids',
            name: 'xm_tag_ids',
            max: 5,
            filterable: true,
            filterMethod: function (val, item) {
                return item.name.toLowerCase().indexOf(val.toLowerCase()) !== -1;
            },
            data: {{ xm_tags|json_encode }}
        });
    </script>

    <script>
        xmSelect.render({
            el: '#xm-category-ids',
            name: 'xm_category_ids',
            tree: {
                show: true,
                simple: true,
                expandedKeys: true,
            },
            data: {{ question_xm_categories|json_encode }}
        });
    </script>

    <script>

        layui.use(['jquery', 'form', 'layer', 'helper'], function () {

            var $ = layui.jquery;
            var form = layui.form;
            var layer = layui.layer;
            var helper = layui.helper;

            var packType = $('input[name=pack_type]').val();
            var paperId = $('input[name=paper_id]').val();

            var loadPaperQuestions = function () {
                var $packManual = $('#pack-manual');
                helper.ajaxLoadHtml($packManual.data('url'), $packManual.attr('id'));
            }

            var addPaperQuestions = function () {
                var questionIds = $('input[name=question_ids]').val();
                if (questionIds.length === 0) {
                    return false;
                }
                $.ajax({
                    type: 'POST',
                    url: '/admin/exam/paper/question/create',
                    data: {
                        paper_id: paperId,
                        question_ids: questionIds,
                    }, success: function () {
                        loadPaperQuestions();
                        layer.msg('添加试题成功', {icon: 1});
                    }
                });
            }

            var showRandPackageTips = function (data) {

                var meetTips = '<i class="layui-icon layui-icon-ok green"></i>';
                var notMeetTips = '<i class="layui-icon layui-icon-close red"></i>';

                var meet = data.single_choice.meet === 1;
                $('#single-choice-meet').html(meet ? meetTips : notMeetTips);

                var meet2 = data.multiple_choice.meet === 1;
                $('#multiple-choice-meet').html(meet2 ? meetTips : notMeetTips);

                var meet3 = data.true_false.meet === 1;
                $('#true-false-meet').html(meet3 ? meetTips : notMeetTips);

                var meet4 = data.blank_fill.meet === 1;
                $('#blank-fill-meet').html(meet4 ? meetTips : notMeetTips);

                var meet5 = data.short_answer.meet === 1;
                $('#short-answer-meet').html(meet5 ? meetTips : notMeetTips);

                var meet6 = data.complex_question.meet === 1;
                $('#complex-question-meet').html(meet6 ? meetTips : notMeetTips);

                $('.kg-meet').show();
            }

            if (packType === '1') {
                loadPaperQuestions();
            }

            form.on('submit(rand_package)', function (data) {
                var modelCount = 0;
                layui.each($('select.model'), function (index, elem) {
                    if ($(elem).val() !== '0') modelCount++;
                });
                if (modelCount === 0) {
                    layer.msg('请选择至少一种题型和题量', {icon: 2});
                    return false;
                }
                var submit = $(this);
                submit.attr('disabled', 'disabled').addClass('layui-btn-disabled');
                $.ajax({
                    type: 'POST',
                    url: data.form.action,
                    data: data.field,
                    success: function (res) {
                        if (res.data.meet_all === 1) {
                            layer.msg('随机组卷成功', {icon: 1});
                        } else {
                            layer.msg('结果不满足需求，请调整组卷条件', {icon: 2});
                        }
                        showRandPackageTips(res.data);
                        submit.removeAttr('disabled').removeClass('layui-btn-disabled');
                    },
                    error: function () {
                        submit.removeAttr('disabled').removeClass('layui-btn-disabled');
                    }
                });
                return false;
            });

            $('body').on('click', '.kg-add-question', function () {
                layer.open({
                    type: 2,
                    title: '添加试题',
                    content: '/admin/exam/question/filter',
                    area: ['80%', '90%'],
                    btn: ['批量添加', '关闭窗口'],
                    btnAlign: 'c',
                    yes: function (index) {
                        addPaperQuestions();
                    },
                    btn2: function (index) {
                        layer.close(index);
                    }
                });
            });

            $('body').on('click', '.kg-delete-question', function () {
                var data = {
                    paper_id: $(this).data('paper-id'),
                    question_id: $(this).data('question-id'),
                };
                layer.confirm('确定要删除吗？', function (index) {
                    $.ajax({
                        type: 'POST',
                        url: '/admin/exam/paper/question/delete',
                        data: data,
                        success: function () {
                            loadPaperQuestions();
                            layer.close(index);
                        }
                    });
                });
            });

        });

    </script>

{% endblock %}
