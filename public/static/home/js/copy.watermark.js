layui.use(['jquery'], function () {

    var $ = layui.jquery;

    var svg = `<svg xmlns="http://www.w3.org/2000/svg" width="200" height="120">
                  <text x="50%" y="50%" 
                    fill="red"
                    fill-opacity="0.08"
                    font-size="16"
                    text-anchor="middle"
                    transform="rotate(-30,100,60)"
                  >{content}</text>
                </svg>`;

    var content = '@' + window.site.title;

    var encoder = new TextEncoder();
    var svgBytes = encoder.encode(svg.replace('{content}', content));
    var bgImage = `url("data:image/svg+xml;base64,${btoa(String.fromCharCode.apply(null, svgBytes))}")`;

    $('.watermark').css({
        'background-image': bgImage,
        'background-repeat': 'repeat',
    });

});