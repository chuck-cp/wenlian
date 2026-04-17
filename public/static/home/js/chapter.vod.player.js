layui.use(['jquery', 'helper'], function () {

    var $ = layui.jquery;
    var helper = layui.helper;

    var interval = null;
    var intervalTime = 15000;
    var humanVerifyFinished = false;
    var userId = window.user.id;
    var userName = window.user.name;
    var requestId = helper.getRequestId();
    var chapterId = $('input[name="chapter.id"]').val();
    var cover = $('input[name="chapter.cover"]').val();
    var planId = $('input[name="chapter.me.plan_id"]').val();
    var position = $('input[name="chapter.me.position"]').val();
    var learningUrl = $('input[name="chapter.learning_url"]').val();
    var playUrls = JSON.parse($('input[name="chapter.play_urls"]').val());
    var settings = JSON.parse($('input[name="chapter.settings"]').val());

    var rates = [
        {name: 'hd', label: '高清'},
        {name: 'sd', label: '标清'},
        {name: 'fd', label: '流畅'},
    ];

    var quality = [];

    $.each(rates, function (k, rate) {
        if (playUrls.hasOwnProperty(rate.name)) {
            quality.push({
                name: rate.label,
                url: playUrls[rate.name]['url'],
            });
        }
    });

    var options = {
        container: document.getElementById('player'),
        contextmenu: [],
        video: {
            pic: cover,
            quality: quality,
            defaultQuality: 0,
        }
    }

    if (String(settings.danmu_enabled) === '1') {
        options.danmaku = {
            id: chapterId,
            api: '/danmu/',
            maximum: 1000,
            user: userId,
        }
    }

    if (String(settings.fast_forward_enabled) === '0') {
        options.playbackSpeed = [1];
    }

    var player = new DPlayer(options);

    player.on('play', function () {
        play();
    });

    player.on('pause', function () {
        pause();
    });

    player.on('ended', function () {
        ended();
    });

    /**
     * 播放器中央播放按钮
     */
    var $playMask = $('#play-mask');

    $playMask.on('click', function () {
        $(this).hide();
        player.toggle();
    });

    var lastPosition = getLastPosition();

    /**
     * 上次播放位置
     */
    if (lastPosition > 0) {
        player.seek(lastPosition);
    }

    if (String(settings.record_anti_enabled) === '1') {
        antiVideoRecord();
    }

    if (String(settings.human_verify_enabled) === '1') {
        verifyHumanPlay();
    }

    if (String(settings.switch_anti_enabled) === '1') {
        antiScreenSwitch();
    }

    if (String(settings.fast_forward_enabled) === '0') {
        disableFastForward();
    }

    disableContextMenu();

    function getHumanVerifyStopRate() {
        var rates = [0.2, 0.3, 0.4, 0.5, 0.6];
        var index = Math.floor(Math.random() * rates.length);
        return rates[index];
    }

    function getPositionKey() {
        return 'chapter:' + chapterId + ':position';
    }

    function getLastPosition() {
        var key = getPositionKey();
        var value = localStorage.getItem(key);
        return value != null ? parseInt(value) : position;
    }

    function setLastPosition(value) {
        var key = getPositionKey();
        localStorage.setItem(key, value);
    }

    function clearLearningInterval() {
        if (interval != null) {
            clearInterval(interval);
            interval = null;
        }
    }

    function setLearningInterval() {
        interval = setInterval(learning, intervalTime);
    }

    function play() {
        hidePlayMask();
        clearLearningInterval();
        setLearningInterval();
    }

    function pause() {
        showPlayMask();
        clearLearningInterval();
    }

    function ended() {
        showPlayMask();
        learning();
        setLastPosition(0);
        clearLearningInterval();
    }

    function showPlayMask() {
        $playMask.show();
    }

    function hidePlayMask() {
        $playMask.hide();
    }

    function learning() {
        setLastPosition(player.video.currentTime);
        if (userId !== '0' && planId !== '0') {
            $.ajax({
                type: 'POST',
                url: learningUrl,
                data: {
                    plan_id: planId,
                    request_id: requestId,
                    interval_time: intervalTime,
                    human_verified: humanVerifyFinished ? 1 : 0,
                    position: player.video.currentTime,
                }
            });
        }
    }

    function antiVideoRecord() {
        var $videoWrap = $('.dplayer-video-wrap');
        var appendHtml = '<div id="anti-block">' + userName + '（' + userId + '）</div>';
        var config = settings.record_anti_config;

        setInterval(function () {
            var $position = $('#anti-block');
            if ($position.length === 0) {
                $videoWrap.append(appendHtml);
            }
            var left = 20 + Math.floor(Math.random() * 600);
            var top = 20 + Math.floor(Math.random() * 360);
            $position.css({
                'z-index': 9,
                'position': 'absolute',
                'top': top + 'px',
                'left': left + 'px',
                'font-size': config.size + 'px',
                'color': config.color,
            });
        }, config.interval * 1000);
    }

    function verifyHumanPlay() {
        var stopRate = getHumanVerifyStopRate();
        player.on('timeupdate', function () {
            var diff = player.video.currentTime - stopRate * player.video.duration;
            if (!humanVerifyFinished && diff > 0 && diff < 1) {
                player.pause();
                layer.alert('点击确认按钮，继续播放视频。', {
                    title: '真人验证',
                    area: ['360px', '180px'],
                    closeBtn: false,
                }, function (index) {
                    humanVerifyFinished = true;
                    layer.close(index);
                    player.play();
                });
            }
        });
    }

    function disableFastForward() {
        var videoTime = 0;
        player.on('timeupdate', function () {
            var videoCurrentTime = player.video.currentTime;
            if (videoCurrentTime - videoTime > 1) {
                player.seek(videoTime);
                player.notice('禁止快进', 2000);
            }
            videoTime = videoCurrentTime;
        });
    }

    function disableContextMenu() {
        $('#player').on('contextmenu', function () {
            $('.dplayer-menu').hide();
            $('.dplayer-mask').hide();
            return false;
        });
    }

    function antiScreenSwitch() {
        // 禁止画中画，画中画控制不了播放
        $('.dplayer-video').attr('disablePictureInPicture', true);
        $(document).on('visibilitychange', function () {
            if (document.hidden) {
                antiScreenSwitchHandler();
            }
        });
        $(window).on('blur', function () {
            antiScreenSwitchHandler();
        });
    }

    function antiScreenSwitchHandler() {
        player.pause();
        var content = '<div class="layui-font-red layui-padding-4">请专心学习啦，不要三心二意！</div>';
        layer.open({
            id: 'anti-switch-mask',
            type: 1,
            title: '系统提示',
            area: ['400px', '160px'],
            shade: 0.5,
            content: content,
            cancel: function () {
                player.play();
            }
        });
    }

});
