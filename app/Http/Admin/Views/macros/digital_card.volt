{%- macro redeem_status(item) %}
    {% if item.user_id > 0 %}
        已使用
    {% elseif item.deleted == 1 %}
        已作废
    {% else %}
        未使用
    {% endif %}
{%- endmacro %}
