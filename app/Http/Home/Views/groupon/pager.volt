{{ partial('macros/sale') }}

{% if pager.total_pages > 0 %}
    <div class="groupon-list">
        <div class="layui-row layui-col-space20">
            {% for sale in pager.items %}
                {% set groupon_url = url({'for':'home.groupon.show','id':sale.id}) %}
                <div class="layui-col-md3">
                    <div class="course-card groupon-card">
                        <div class="model">{{ sale_item_type_badge(sale.item.type) }}</div>
                        <div class="cover">
                            <a href="{{ groupon_url }}" target="_blank">
                                <img src="{{ sale.item.cover }}" alt="{{ sale.item.title }}" title="{{ sale.item.title }}">
                            </a>
                        </div>
                        <div class="info">
                            <div class="title layui-elip">
                                <a href="{{ groupon_url }}" title="{{ sale.item.title }}" target="_blank">{{ sale.item.title }}</a>
                            </div>
                            <div class="meta">
                                <span class="origin-price">{{ '￥%0.2f'|format(sale.item.price) }}</span>
                                <span class="price">{{ '￥%0.2f'|format(sale.member_price) }}</span>
                                <span class="partner-limit">{{ sale.partner_limit }}人团</span>
                            </div>
                        </div>
                    </div>
                </div>
            {% endfor %}
        </div>
    </div>
    {{ partial('partials/pager_ajax') }}
{% endif %}
