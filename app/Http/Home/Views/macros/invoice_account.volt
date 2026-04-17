{%- macro usage_type(value) %}
    {% if value == 1 %}
        {% return '增值税普票' %}
    {% elseif value == 2 %}
        {% return '增值税专票' %}
    {% else %}
        {% return 'N/A' %}
    {% endif %}
{%- endmacro %}

{%- macro head_type(value) %}
    {% if value == 1 %}
        {% return '个人' %}
    {% elseif value == 2 %}
        {% return '企业' %}
    {% elseif value == 3 %}
        {% return '组织' %}
    {% else %}
        {% return 'N/A' %}
    {% endif %}
{%- endmacro %}

{%- macro account_summary_tips(account) %}
    {% set tips = '&#10;' %}
    {% if account.tax_account %}
        {% set tips = tips ~ '纳税人识别号：' ~ account.tax_account ~ '&#10;' %}
    {% endif %}
    {% if account.bank_name %}
        {% set tips = tips ~ '基本开户银行：' ~ account.bank_name ~ '&#10;' %}
    {% endif %}
    {% if account.bank_account %}
        {% set tips = tips ~ '基本开户帐号：' ~ account.bank_account ~ '&#10;' %}
    {% endif %}
    {% if account.company_address %}
        {% set tips = tips ~ '企业注册地址：' ~ account.company_address ~ '&#10;' %}
    {% endif %}
    {% if account.company_phone %}
        {% set tips = tips ~ '企业注册电话：' ~ account.company_phone ~ '&#10;' %}
    {% endif %}
    {% return tips %}
{%- endmacro %}