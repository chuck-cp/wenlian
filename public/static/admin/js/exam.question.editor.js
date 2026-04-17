var editor = {};

editor.options = {
    uploadJson: '/admin/upload/exam/question/img',
    cssPath: '/static/home/css/content.css',
    width: '540px',
    height: '100px',
    minWidth: '500px',
    minHeight: '80px',
    items: [
        'selectall', '|',
        'undo', 'redo', '|',
        'forecolor', 'hilitecolor', 'bold', 'italic', 'underline', 'strikethrough', 'removeformat', '|',
        'insertorderedlist', 'insertunorderedlist', 'table', '|',
        'image', 'link', 'unlink', '|',
        'source', 'about'
    ],
    htmlTags: {
        span: ['.color', '.background-color'],
        a: ['id', 'class', 'href', 'target', 'name'],
        img: ['id', 'class', 'src', 'width', 'height', 'alt', 'title'],
        table: ['id', 'class'],
        div: ['id', 'class'],
        pre: ['id', 'class'],
        hr: ['id', 'class'],
        embed: ['id', 'class', 'src', 'width', 'height', 'type', 'loop', 'autostart', 'quality', 'align', 'wmode'],
        iframe: ['id', 'class', 'src', 'width', 'height'],
        'td,th': ['id', 'class'],
        'p,ol,ul,li,blockquote,h1,h2,h3,h4,h5,h6': ['id', 'class'],
        'br,tbody,tr,strong,b,sub,sup,em,i,u,strike,s,del': ['id', 'class'],
    },
    extraFileUploadParams: {
        csrf_token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    }
};
