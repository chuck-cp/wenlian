layui.use(['jquery', 'layer'], function () {

    var $ = layui.jquery;
    var layer = layui.layer;

    var paperUserId = $('input[name="paper_user.id"]').val();

    var $modelFilter = $('.model-filter');
    var $btnUnitFresh = $('#btn-unit-fresh');
    var $btnUnitHistory = $('#btn-unit-history');
    var $freshSortBox = $('#fresh-sort-box');
    var $historySortBox = $('#history-sort-box');
    var $examQuestionBox = $('#exam-question-box');

    var finishedQuestions = [];

    var hasComplexQuestion = false;

    var historyPager = {
        page: 1,
        limit: 20,
        total_pages: 0,
        items: [],
        params: {
            model: 0
        }
    }

    var getFreshQuestionsUrl = function () {
        return `/exam/${paperUserId}/unit/questions/fresh`;
    }

    var getHistoryQuestionsUrl = function () {
        return `/exam/${paperUserId}/unit/questions/history`;
    }

    var getQuestionInfoUrl = function (id, sn) {
        return `/exam/question/${id}/info?sn=${sn}`;
    }

    var getAnswerSubmitUrl = function () {
        return `/exam/${paperUserId}/unit/answer/submit`;
    };

    var getUnitResetUrl = function () {
        return `/exam/${paperUserId}/unit/reset`;
    }

    var getQuestionFavoriteUrl = function (id) {
        return `/exam/question/${id}/favorite`;
    };

    var resetQuestionFilter = function () {
        $modelFilter.removeClass('layui-btn layui-btn-xs');
        historyPager.params.model = 0;
    };

    var loadFreshSort = function () {
        $.ajax({
            method: 'GET',
            url: getFreshQuestionsUrl(),
            data: {
                page: historyPager.page,
                limit: historyPager.limit,
                model: historyPager.params.model,
            },
            success: function (res) {
                setFreshSortContent(res.questions);
                loadFirstFreshQuestion(res.questions);
            }
        });
    }

    var loadHistorySort = function () {
        $.ajax({
            method: 'GET',
            url: getHistoryQuestionsUrl(),
            data: {
                page: historyPager.page,
                limit: historyPager.limit,
                model: historyPager.params.model,
            },
            success: function (res) {
                historyPager.total_pages = res.pager.total_pages;
                setHistorySortContent(res.pager.items);
                loadFirstHistoryQuestion(res.pager.items);
            }
        });
    }

    var setFreshSortContent = function (items) {
        var html = '';
        if (items.length > 0) {
            html += '<div class="exam-sort-list">';
            layui.each(items, function (index, item) {
                var sn = index + 1;
                html += `<div class="sort" id="sort-${item.id}" data-id="${item.id}" data-sn="${sn}">${sn}</div>`;
            });
            html += '</div>';
            html += '<div class="exam-sort-pager">';
            html += '<button class="layui-btn layui-btn-normal btn-fresh-reload">换一批</button>';
            html += '</div>';
        } else {
            if (historyPager.params.model === 0) {
                html += '<div class="exam-sort-empty">';
                html += '<button class="layui-btn layui-btn-danger btn-unit-reset">重新开始</button>';
                html += '</div>';
            } else {
                html += '<div class="exam-sort-empty">没有相关记录</div>';
            }
        }
        $freshSortBox.html(html);
    }

    var setHistorySortContent = function (items) {
        var html = '';
        if (items.length > 0) {
            html += '<div class="exam-sort-list">';
            layui.each(items, function (index, item) {
                var sn = (historyPager.page - 1) * historyPager.limit + index + 1;
                html += `<div class="sort" id="sort-${item.id}" data-id="${item.id}" data-sn="${sn}">${sn}</div>`;
            });
            html += '</div>';
        } else {
            html += '<div class="exam-sort-empty">没有相关记录</div>';
        }
        if (historyPager.total_pages > 1) {
            html += '<div class="exam-sort-pager">';
            html += '<button class="layui-btn layui-btn-primary layui-btn-sm btn-history-prev">上一页</button>';
            html += '<button class="layui-btn layui-btn-primary layui-btn-sm btn-history-next">下一页</button>';
            html += '</div>';
        }
        $historySortBox.html(html);
    }

    var loadFirstHistoryQuestion = function (items) {
        var id = items.length > 0 ? items[0].id : 0;
        var sn = (historyPager.page - 1) * historyPager.limit + 1;
        if (id > 0) {
            loadQuestion(id, sn);
        }
    }

    var loadFirstFreshQuestion = function (items) {
        var id = items.length > 0 ? items[0].id : 0;
        if (id > 0) {
            loadQuestion(id, 1);
        }
    }

    var loadQuestion = function (id, sn) {
        var url = getQuestionInfoUrl(id, sn);
        $('.exam-question').hide();
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

    var clearQuestions = function () {
        hasComplexQuestion = false;
        finishedQuestions = [];
        $examQuestionBox.html('');
    }

    var finishQuestionSort = function (id) {
        var $sort = $('#sort-' + id);
        $sort.addClass('finished');
    };

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

    var submitAnswer = function (parentId, questionId, questionModel, userAnswer) {
        $.ajax({
            method: 'POST',
            url: getAnswerSubmitUrl(),
            data: {
                question_id: questionId,
                user_answer: userAnswer,
            },
            success: function (res) {
                finishedQuestions.push(questionId);
                showAnalysis(questionId, questionModel, res.user_score);
                if (parentId > 0) {
                    hasComplexQuestion = true;
                    finishQuestionSort(parentId);
                } else {
                    finishQuestionSort(questionId);
                }
            }
        });
    };

    var autoRefresh = function () {
        var $freshBtn = $('.btn-fresh-reload');
        if ($freshBtn.length === 0) return;
        var questionCount = $('.exam-sort-list > .sort').length;
        if (finishedQuestions.length >= questionCount) {
            if (hasComplexQuestion) {
                $freshBtn.addClass('layui-anim layui-anim-downbit layui-anim-loop');
            } else {
                clearQuestions();
                loadFreshSort();
            }
        }
    };

    $btnUnitFresh.on('click', function () {
        $btnUnitFresh.removeClass('layui-btn-primary').addClass('layui-btn-normal');
        $btnUnitHistory.removeClass('layui-btn-normal').addClass('layui-btn-primary');
        $historySortBox.hide();
        $freshSortBox.show();
        resetQuestionFilter();
        clearQuestions();
        loadFreshSort();
    });

    $btnUnitHistory.on('click', function () {
        $btnUnitHistory.removeClass('layui-btn-primary').addClass('layui-btn-normal');
        $btnUnitFresh.removeClass('layui-btn-normal').addClass('layui-btn-primary');
        $freshSortBox.hide();
        $historySortBox.show();
        resetQuestionFilter();
        clearQuestions();
        loadHistorySort();
    });

    $modelFilter.on('click', function () {
        historyPager.params.model = $(this).data('model');
        var className = 'layui-btn layui-btn-xs';
        $('.model-filter').removeClass(className);
        $(this).addClass(className);
        clearQuestions();
        if ($btnUnitFresh.hasClass('layui-btn-normal')) {
            loadFreshSort();
        } else {
            loadHistorySort();
        }
    });

    $('body').on('click', '.btn-history-prev', function () {
        if (historyPager.page > 1) {
            historyPager.page--;
            loadHistorySort();
        }
    });

    $('body').on('click', '.btn-history-next', function () {
        if (historyPager.page < historyPager.total_pages) {
            historyPager.page++;
            loadHistorySort();
        }
    });

    $('body').on('click', '.btn-fresh-reload', function () {
        clearQuestions();
        loadFreshSort();
    });

    $('body').on('click', '.btn-unit-reset', function () {
        var index = layer.confirm('重新开始会清空答题记录，你确定要执行吗？', function () {
            $.ajax({
                method: 'POST',
                url: getUnitResetUrl(),
                success: function () {
                    resetQuestionFilter();
                    loadFreshSort();
                    layer.close(index);
                }
            });
        });
    });

    $('body').on('click', '.sort', function () {
        var id = $(this).data('id');
        var sn = $(this).data('sn');
        loadQuestion(id, sn);
    });

    $('body').on('click', '.favorite', function () {
        var id = $(this).data('id');
        var $icon = $(this).children('i');
        $.ajax({
            method: 'POST',
            url: getQuestionFavoriteUrl(id),
            success: function () {
                if ($icon.hasClass('layui-icon-star-fill')) {
                    $icon.removeClass('layui-icon-star-fill');
                    $icon.addClass('layui-icon-star');
                } else {
                    $icon.removeClass('layui-icon-star');
                    $icon.addClass('layui-icon-star-fill');
                }
            }
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
        submitAnswer(pid, id, model, answer);
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
        submitAnswer(pid, id, model, answer);
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
    })

    /**
     * 定时自动刷新题目
     */
    setInterval(autoRefresh, 10000);

    /**
     * 触发全新练习点击事件
     */
    $btnUnitFresh.trigger('click');

});