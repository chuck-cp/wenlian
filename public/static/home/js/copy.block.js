layui.use(['jquery'], function () {

    var $ = layui.jquery;

    $(document).on('contextmenu', function () {
        return false;
    });

    $(document).on('selectstart', function () {
        return false;
    });

    $(document).on('keydown', function (e) {
        if (e.key === 'F12') {
            return false;
        } else if (e.ctrlKey && e.key === 'u') {
            return false;
        } else if (e.ctrlKey && e.shiftKey && e.key === 'C') {
            return false;
        } else if (e.ctrlKey && e.shiftKey && e.key === 'I') {
            return false;
        }
    });

});