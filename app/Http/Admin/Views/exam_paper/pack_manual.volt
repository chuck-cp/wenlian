{% set questions_url = url({'for':'admin.exam_paper.questions','id':paper.id}) %}

<div id="pack-manual" data-url="{{ questions_url }}"></div>