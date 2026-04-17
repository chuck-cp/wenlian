<div class="layui-card">
    <div class="layui-card-header">学习内容</div>
    <div class="layui-card-body">
        <ul class="my-menu">
            <li><a href="{{ url({'for':'home.uc.study_courses'}) }}">在学课程</a></li>
            <li><a href="{{ url({'for':'home.uc.study_articles'}) }}">在学专栏</a></li>
            <li><a href="{{ url({'for':'home.uc.questions'}) }}">我的提问</a></li>
            <li><a href="{{ url({'for':'home.uc.answers'}) }}">我的回答</a></li>
            <li><a href="{{ url({'for':'home.uc.favorites'}) }}">我的收藏</a></li>
        </ul>
    </div>
</div>

<div class="layui-card">
    <div class="layui-card-header">考试练习</div>
    <div class="layui-card-body">
        <ul class="my-menu">
            <li><a href="{{ url({'for':'home.uc.study_exam_papers'},{'exam_type':1}) }}">模拟考试</a></li>
            <li><a href="{{ url({'for':'home.uc.study_exam_papers'},{'exam_type':2}) }}">同步练习</a></li>
            <li><a href="{{ url({'for':'home.exam.mistake_explore'}) }}">错题训练</a></li>
            <li><a href="{{ url({'for':'home.exam.favorite_explore'}) }}">收藏训练</a></li>
        </ul>
    </div>
</div>