layui.use(['jquery', 'form'], function () {

    var $ = layui.jquery;
    var form = layui.form;

    form.on('radio(apply_coupon)', function (data) {
        console.log('fuck')
        var $itemId = $('input[name=item_id]');
        var $itemType = $('input[name=item_type]');
        var $couponCode = $('input[name=coupon_code]');
        var $discountAmount = $('#discount-amount');
        var $totalAmount = $('#total-amount');
        var $payAmount = $('#pay-amount');
        $.ajax({
            type: 'POST',
            url: '/order/coupon',
            data: {
                item_id: $itemId.val(),
                item_type: $itemType.val(),
                coupon_code: data.value,
            },
            success: function (res) {
                $discountAmount.text('￥' + res.data.discount_amount);
                $totalAmount.text('￥' + res.data.total_amount);
                $payAmount.text('￥' + res.data.pay_amount);
                $couponCode.val(data.value);
            }
        });
    });

});