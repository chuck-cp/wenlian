layui.use(['jquery', 'layer', 'form', 'upload', 'util'], function () {

    var $ = layui.jquery;
    var layer = layui.layer;
    var form = layui.form;
    var upload = layui.upload;
    var util = layui.util;

    var countdownEndTime = $('input[name="countdown.end_time"]').val();
    var countdownServerTime = $('input[name="countdown.server_time"]').val();
    var paperUserId = $('input[name="paper_user.id"]').val();
    var paperUserOver = $('input[name="paper_user.over"]').val();
    var authCode = $('input[name="paper_user.auth_code"]').val();
    var paperId = $('input[name="paper.id"]').val();

    var $sortItems = $('.exam-sort-list > .sort');
    var $sortPager = $('.exam-sort-pager');

    var pageSize = 30;
    var currentPage = 1;
    var totalCount = $sortItems.length;
    var totalPage = Math.ceil(totalCount / pageSize);

    var maxUploadCount = 4;

    var fetchIntervals = [];

    var initSortPager = function () {
        if (totalPage > 1) {
            var html = '<button class="layui-btn layui-btn-primary layui-btn-sm btn-prev">上一页</button>';
            html += '<button class="layui-btn layui-btn-primary layui-btn-sm btn-next">下一页</button>';
            $sortPager.html(html).show();
            showSortItems(1);
        }
    };

    var showSortItems = function (page) {
        var start = (page - 1) * pageSize;
        var end = page * pageSize;
        layui.each($sortItems, function (index, item) {
            if (index >= start && index < end) {
                $(item).show();
            } else {
                $(item).hide();
            }
        });
    };

    var updateAnswerFileUploadState = function (id) {
        var $buttons = $('#answer-file-box-' + id + '> .action > .layui-btn');
        var files = getUploadAnswerFiles(id);
        if (files.length >= maxUploadCount) {
            $buttons.attr('disabled', 'disabled').addClass('layui-btn-disabled');
        } else {
            $buttons.removeAttr('disabled').removeClass('layui-btn-disabled');
        }
    };

    var getUploadAnswerFiles = function (id) {
        var $items = $('#answer-file-box-' + id + '> .preview > .item');
        var result = [];
        layui.each($items, function (index, elem) {
            result.push({
                id: $(elem).data('id'),
                name: $(elem).data('name'),
                url: $(elem).data('url'),
            });
        });
        return result;
    };

    var appendUploadAnswerFiles = function (id, newFiles) {
        var uploadedFiles = getUploadAnswerFiles(id);
        if (uploadedFiles.length >= maxUploadCount) {
            return;
        }
        var html = '';
        var index = uploadedFiles.length + 1;
        var files = newFiles.slice(0, maxUploadCount - uploadedFiles.length);
        for (var file of files) {
            html += '<div class="item" data-id="' + file.id + '" data-name="' + file.name + '" data-url="' + file.url + '">' +
                '<div class="sort">' + index + '</div>' +
                '<div class="content"><a href="' + file.url + '" title="' + file.name + '" target="_blank"><i class="layui-icon layui-icon-file"></i></a></div>' +
                '<div class="remove"><i class="layui-icon layui-icon-delete"></i></div>' +
                '</div>';
            index++;
        }
        $('#answer-file-box-' + id + '>.preview').append(html);
    };

    var setFetchInterval = function (id) {
        var interval = getFetchInterval(id);
        if (interval) {
            return interval;
        }
        interval = window.setInterval(function () {
            $.ajax({
                method: 'GET',
                url: getFetchAnswerFileUrl(),
                data: {
                    paper_user_id: paperUserId,
                    question_id: id,
                },
                success: function (res) {
                    if (res.files.length > 0) {
                        appendUploadAnswerFiles(id, res.files);
                        afterAnswerFileHandle(id);
                    }
                }
            });
        }, 5000);
        fetchIntervals[id] = interval;
        return interval;
    };

    var getFetchInterval = function (id) {
        return fetchIntervals[id] || null;
    };

    var clearFetchInterval = function (id) {
        var interval = getFetchInterval(id);
        if (interval) {
            window.clearInterval(interval);
        }
    };

    var afterAnswerFileHandle = function (id) {
        var $answer = $('#answer-' + id);
        var sn = $answer.data('sn');
        var answer = $answer.val();
        var files = getUploadAnswerFiles(id);
        submitAnswer(id, answer, files);
        updateAnswerFileUploadState(id);
        if (answer.length > 0 || files.length > 0) {
            activeQuestionSort(sn);
        } else {
            inactiveQuestionSort(sn);
        }
    };

    var activeQuestionSort = function (sn) {
        var $sort = $('#sort-' + sn);
        $sort.addClass('active');
    };

    var inactiveQuestionSort = function (sn) {
        var $sort = $('#sort-' + sn);
        $sort.removeClass('active');
    };

    var getMyStudyPaperUrl = function () {
        return '/uc/study/exam/papers?exam_type=1';
    }

    var getPaperShowUrl = function () {
        return `/exam/paper/${paperId}`;
    };

    var getExamExploreUrl = function () {
        return `/exam/${paperUserId}/mock/explore`;
    };

    var getPaperSubmitUrl = function () {
        return `/exam/${paperUserId}/mock/paper/submit`;
    };

    var getPaperGradeUrl = function () {
        return `/exam/${paperUserId}/mock/paper/grade`;
    };

    var getAnswerSubmitUrl = function () {
        return `/exam/${paperUserId}/mock/answer/submit`;
    };

    var getAnswerMarkUrl = function () {
        return `/exam/${paperUserId}/mock/answer/mark`;
    };

    var getAnswerFileUploadUrl = function () {
        return '/upload/exam/answer/file';
    };

    var getAnswerFileQrcodeUrl = function () {
        return '/exam/answer/file/qrcode';
    };

    var getFetchAnswerFileUrl = function () {
        return '/exam/answer/file/fetch';
    };

    var getFavoriteUrl = function (id) {
        return `/exam/question/${id}/favorite`;
    };

    var submitAnswer = function (questionId, userAnswer, userAnswerFiles = []) {
        var url = getAnswerSubmitUrl();
        var data = {
            question_id: questionId,
            user_answer: userAnswer,
            user_answer_files: userAnswerFiles,
        };
        $.post(url, data);
    };

    initSortPager();

    $('body').on('click', '.btn-prev', function () {
        if (currentPage > 1) {
            currentPage--;
            showSortItems(currentPage);
        }
    });

    $('body').on('click', '.btn-next', function () {
        if (currentPage < totalPage) {
            currentPage++;
            showSortItems(currentPage);
        }
    });

    $('body').on('click', '.preview > .item > .remove', function () {
        var $item = $(this).parent();
        var id = $item.parent().parent().data('id');
        $item.remove();
        afterAnswerFileHandle(id);
    });

    $('.single-choice').on('click', function () {
        if (paperUserOver === '1') return;
        var id = $(this).data('id');
        var sn = $(this).data('sn');
        var answer = $(this).data('choice');
        $(this).parent().children().children('.active').removeClass('active');
        $(this).children('.circle').addClass('active');
        activeQuestionSort(sn);
        submitAnswer(id, answer);
    });

    $('.multiple-choice').on('click', function () {
        if (paperUserOver === '1') return;
        var id = $(this).data('id');
        var sn = $(this).data('sn');
        $(this).children('.circle').toggleClass('active');
        var answers = [];
        var $choices = $(this).parent().children('.choice');
        layui.each($choices, function (index, item) {
            if ($(item).children().hasClass('active')) {
                answers.push($(item).data('choice'));
            }
        });
        if (answers.length > 0) {
            activeQuestionSort(sn);
        } else {
            inactiveQuestionSort(sn);
        }
        submitAnswer(id, answers.join(','));
    });

    $('.blank-fill-input').on('blur', function () {
        if (paperUserOver === '1') return;
        var id = $(this).data('id');
        var sn = $(this).data('sn');
        var $blankFills = $('#blank-fill-' + sn).find('.blank-fill-input');
        var answers = [];
        layui.each($blankFills, function (index, item) {
            answers.push($.trim($(item).val()));
        });
        if (answers.length > 0) {
            activeQuestionSort(sn);
        } else {
            inactiveQuestionSort(sn);
        }
        submitAnswer(id, answers.join(','));
    });

    $('.short-answer-textarea').on('blur', function () {
        if (paperUserOver === '1') return;
        var id = $(this).data('id');
        var sn = $(this).data('sn');
        var answer = $(this).val();
        var files = getUploadAnswerFiles(id);
        if (answer.length > 0 || files.length > 0) {
            activeQuestionSort(sn);
        } else {
            inactiveQuestionSort(sn);
        }
        submitAnswer(id, answer, files);
    });

    $('.sort').on('click', function () {
        var sn = $(this).data('sn');
        var $element = $('#question-' + sn);
        $('html').scrollTop($element.offset().top - 100);
    });

    $('#paper-submit').on('click', function () {
        var totalCount = $('.exam-sort-list > .sort').length;
        var finishCount = $('.exam-sort-list > .active').length;
        var disableCount = $('.exam-sort-list > .disabled').length;
        var notFinishCount = totalCount - finishCount - disableCount;
        var msg = '确定要交卷吗？';
        if (notFinishCount > 0) {
            msg = '还有' + notFinishCount + '道题没有完成，确定要交卷吗？';
        }
        layer.confirm(msg, function (index) {
            $.ajax({
                method: 'POST',
                url: getPaperSubmitUrl(),
                success: function (res) {
                    layer.close(index);
                    var location = getPaperShowUrl();
                    var msg = '交卷成功';
                    if (res.paper_user.status === 4) {
                        location = getExamExploreUrl();
                        msg = '交卷成功，请查阅考试结果';
                    } else if (res.paper_user.status === 3) {
                        if (res.paper_user.grade_type === 2) {
                            location = getMyStudyPaperUrl();
                            msg = '交卷成功，请等待老师阅卷评分';
                        } else if (res.paper_user.grade_type === 3) {
                            location = getExamExploreUrl() + '?manual_grade=1';
                            msg = '交卷成功，请对照答案，给客观题评分';
                        }

                    }
                    layer.msg(msg, {icon: 1});
                    window.setTimeout(function () {
                        window.location.href = location;
                    }, 3000);
                }
            });
        });
    });

    $('#paper-grade').on('click', function () {
        var notFinishCount = 0;
        layui.each($('select[name=user_score]'), function (index, elem) {
            if ($(elem).val() === '') {
                notFinishCount++;
            }
        });
        var msg = '确定要结束阅卷吗？';
        if (notFinishCount > 0) {
            msg = '还有' + notFinishCount + '道题待评分，确定要结束阅卷吗？';
        }
        layer.confirm(msg, function (index) {
            $.ajax({
                method: 'POST',
                url: getPaperGradeUrl(),
                data: {auth_code: authCode},
                success: function () {
                    layer.close(index);
                    layer.msg('提交阅卷成功', {icon: 1});
                    window.setTimeout(function () {
                        window.location.href = getPaperShowUrl();
                    }, 3000);
                }
            });
        });
    });

    $('.favorite').on('click', function () {
        var id = $(this).data('id');
        var $icon = $(this).children('i');
        $.ajax({
            method: 'POST',
            url: getFavoriteUrl(id),
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

    $('.scan-upload').on('click', function () {
        var questionId = $(this).data('id');
        setFetchInterval(questionId);
        $.ajax({
            method: 'GET',
            url: getAnswerFileQrcodeUrl(),
            data: {
                paper_user_id: paperUserId,
                question_id: questionId,
            },
            success: function (res) {
                var content = '<div class="qrcode"><img src="' + res.qrcode + '" alt="扫码上传"></div>';
                layer.open({
                    type: 1,
                    title: false,
                    closeBtn: 1,
                    content: content,
                    cancel: function (index) {
                        if (window.confirm('确定要结束上传吗？')) {
                            layer.close(index);
                            clearFetchInterval(questionId);
                        } else {
                            return false;
                        }
                    }
                });
            }
        });
    });

    util.countdown(1000 * parseInt(countdownEndTime), 1000 * parseInt(countdownServerTime), function (date) {
        if (paperUserOver === '1') return;
        var items = [
            {date: date[1], label: '时'},
            {date: date[2], label: '分'},
            {date: date[3], label: '秒'},
        ];
        var html = '';
        layui.each(items, function (index, item) {
            if (item.date > 0) {
                html += '<span class="value">' + item.date + '</span>';
                html += '<span class="label">' + item.label + '</span>';
            }
        });
        $('.countdown > .timer').html(html);
        if (date[1] === 0 && date[2] === 0 && date[3] === 10) {
            layer.msg('交卷时间到了，系统自动交卷...', {
                icon: 1,
                time: 5000,
            }, function () {
                $.ajax({
                    method: 'POST',
                    url: getPaperSubmitUrl(),
                    success: function (res) {
                        var location = getPaperShowUrl();
                        if (res.paper_user.status === 4) {
                            location = getExamExploreUrl();
                        }
                        window.location.href = location;
                    }
                });
            });
        }
    });

    upload.render({
        elem: '.local-upload',
        url: getAnswerFileUploadUrl(),
        accept: 'file',
        size: 10000,
        exts: 'jpg|jpeg|gif|png|webp|doc|xls|ppt|docx|xlsx|pptx|pdf|zip|rar|gz|7z',
        done: function (res) {
            var id = $(this.item).data('id');
            appendUploadAnswerFiles(id, [res.data]);
            afterAnswerFileHandle(id);
        }
    });

    form.on('select(mark)', function (data) {
        var questionId = $(data.elem).data('id');
        var userScore = data.value;
        if (userScore === '') {
            layer.msg('请选择一个有效评分', {icon: 2});
            return false;
        }
        $.ajax({
            method: 'POST',
            url: getAnswerMarkUrl(),
            data: {
                question_id: questionId,
                user_score: userScore,
                auth_code: authCode,
            },
            success: function () {
                layer.msg('提交评分成功', {icon: 1});
            }
        });
    });

});
