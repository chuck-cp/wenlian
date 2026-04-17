{% if paper.pack_type == 1 %}

    {{ partial('exam_paper/pack_manual') }}

{% elseif paper.pack_type == 2 %}

    {{ partial('exam_paper/pack_random') }}

{% endif %}