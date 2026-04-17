layui.use(['jquery', 'layer'], function () {

    var $ = layui.jquery;
    var layer = layui.layer;

    var $examSortBox = $('#exam-sort-box');
    var $examQuestionBox = $('#exam-question-box');

    var finishedQuestions = [];

    var pager = {
        page: 1,
        limit: 20,
        total_pages: 0,
        total_items: 0,
        items: [],
        params: {
            model: 0
        }
    }

    var getQuestionListUrl = function () {
        return `/exam/favorite/questions`;
    }

    var getQuestionInfoUrl = function (id, sn) {
        return `/exam/question/${id}/info?type=favorite&sn=${sn}`;
    }

    var getAnswerCheckUrl = function (id) {
        return `/exam/question/${id}/answer/check`;
    };

    var getFavoriteUrl = function (id) {
        return `/exam/question/${id}/favorite`;
    };

    var loadExamSort = function () {
        emptyAllQuestion();
        $.ajax({
            method: 'GET',
            url: getQuestionListUrl(),
            data: {
                page: pager.page,
                limit: pager.limit,
                model: pager.params.model,
            },
            success: function (res) {
                pager.total_pages = res.pager.total_pages;
                pager.total_items = res.pager.total_items;
                pager.items = res.pager.items;
                setTotalCount();
                setExamSortContent();
                loadFirstQuestion();
            }
        });
    }

    var setTotalCount = function () {
        $('#total-count').text(pager.total_items);
    }

    var setExamSortContent = function () {
        var html = '';
        if (pager.items.length > 0) {
            html += '<div class="exam-sort-list">';
            layui.each(pager.items, function (index, item) {
                var sn = (pager.page - 1) * pager.limit + index + 1;
                html += `<div class="sort" id="sort-${item.id}" data-id="${item.id}" data-sn="${sn}">${sn}</div>`;
            });
            html += '</div>';
        } else {
            html += '<div class="exam-sort-empty">没有相关记录</div>';
        }
        if (pager.total_pages > 1) {
            html += '<div class="exam-sort-pager">';
            html += '<button class="layui-btn layui-btn-primary layui-btn-sm btn-page-prev">上一页</button>';
            html += '<button class="layui-btn layui-btn-primary layui-btn-sm btn-page-next">下一页</button>';
            html += '</div>';
        }
        $examSortBox.html(html);
    }

    var loadFirstQuestion = function () {
        var id = pager.items.length > 0 ? pager.items[0].id : 0;
        var sn = (pager.page - 1) * pager.limit + 1;
        if (id > 0) {
            loadQuestion(id, sn);
        }
    }

    var loadQuestion = function (id, sn) {
        var url = getQuestionInfoUrl(id, sn);
        var items = $('.exam-question');
        layui.each(items, function (index, item) {
            if ($(item).data('pid') === 0) {
                $(item).hide();
            }
        });
        var $question = $('#question-' + id);
        var $loading = $('#question-loading');
        if ($question.length === 0) {
            $loading.show();
            $.get(url, function (html) {
                $examQuestionBox.append(html);
                $loading.hide();
            });
        }
        $question.show();
    }

    var finishQuestion = function (id) {
        finishedQuestions.push(id);
        $('#sort-' + id).addClass('finished');
    };

    var disableQuestion = function (id) {
        $('#question-' + id).addClass('disabled');
        $('#sort-' + id).addClass('disabled');
    }

    var emptyAllQuestion = function () {
        $('#exam-question-box').html('');
    }

    var hasAnswered = function (id) {
        return finishedQuestions.includes(id);
    }

    var showAnalysis = function (questionId, questionModel, userScore) {
        var $analysisBox = $('#question-' + questionId).find('.analysis-box');
        var $rowList = $analysisBox.children('.row-list');
        var $userScore = $analysisBox.find('.score');
        var className = userScore > 0 ? 'success' : 'error';
        if (questionModel === 5) {
            className = 'primary';
        }
        $analysisBox.removeClass('layui-hide');
        $rowList.addClass(className);
        $userScore.text(userScore);
    };

    var checkAnswer = function (parentId, questionId, questionModel, userAnswer) {
        $.ajax({
            method: 'POST',
            url: getAnswerCheckUrl(questionId),
            data: {user_answer: userAnswer},
            success: function (res) {
                if (parentId > 0) {
                    finishQuestion(parentId);
                } else {
                    finishQuestion(questionId);
                }
                showAnalysis(questionId, questionModel, res.user_score);
            }
        });
    };

    $('.model-filter').on('click', function () {
        pager.params.model = $(this).data('model');
        var className = 'layui-btn layui-btn-xs';
        $('.model-filter').removeClass(className);
        $(this).addClass(className);
        loadExamSort();
    });

    $('body').on('click', '.btn-page-prev', function () {
        if (pager.page > 1) {
            pager.page--;
            loadExamSort();
        }
    });

    $('body').on('click', '.btn-page-next', function () {
        if (pager.page < pager.total_pages) {
            pager.page++;
            loadExamSort();
        }
    });

    $('body').on('click', '.sort', function () {
        var id = $(this).data('id');
        var sn = $(this).data('sn');
        loadQuestion(id, sn);
    });

    $('body').on('click', '.delete', function () {
        var id = $(this).data('id');
        layer.confirm('确定要移出收藏吗？', function (index) {
            $.ajax({
                method: 'POST',
                url: getFavoriteUrl(id),
                success: function () {
                    disableQuestion(id);
                    layer.close(index);
                }
            });
        });
    });

    $('body').on('click', '.check-answer', function () {
        var id = $(this).data('id');
        var pid = $(this).data('pid');
        var model = $(this).data('model');
        var answer = $('#answer-' + id).val();
        if (hasAnswered(id)) {
            return false;
        }
        checkAnswer(pid, id, model, answer);
    });

    $('body').on('click', '.single-choice', function () {
        var id = $(this).data('id');
        var pid = $(this).data('pid');
        var model = $(this).data('model');
        var answer = $(this).data('choice')
        if (hasAnswered(id)) {
            return false;
        }
        $(this).children('.circle').addClass('active');
        checkAnswer(pid, id, model, answer);
    });

    $('body').on('click', '.multiple-choice', function () {
        var id = $(this).data('id');
        if (hasAnswered(id)) {
            return false;
        }
        $(this).children('.circle').toggleClass('active');
        var answers = [];
        var $choices = $(this).parent().children('.choice');
        layui.each($choices, function (index, item) {
            if ($(item).children().hasClass('active')) {
                answers.push($(item).data('choice'));
            }
        });
        if (answers.length > 0) {
            $('#answer-' + id).val(answers.join(','));
        }
    });

    $('body').on('blur', '.blank-fill-input', function () {
        var id = $(this).data('id');
        var $blankFills = $('#blank-fill-' + id).find('.blank-fill-input');
        var answers = [];
        layui.each($blankFills, function (index, item) {
            answers.push($.trim($(item).val()));
        });
        if (answers.length > 0) {
            $('#answer-' + id).val(answers.join(','));
        }
    });

    $('body').on('blur', '.short-answer-textarea', function () {
        var id = $(this).data('id');
        var answer = $(this).val();
        if (answer.length > 0) {
            $('#answer-' + id).val(answer);
        }
    });

    loadExamSort();

});