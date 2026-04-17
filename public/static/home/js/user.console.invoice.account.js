layui.use(['jquery', 'form'], function () {

    var $ = layui.jquery;
    var form = layui.form;

    var $usageType = $('input[name=usage_type]');
    var $headName = $('input[name=head_name]');
    var $taxAccount = $('input[name=tax_account]');
    var $taxAccountBlock = $('#tax-account-block');
    var $companyBlock = $('#company-block');

    form.on('radio(head_type)', function (data) {
        if (data.value === '1') {
            $usageType.removeAttr('checked');
            $usageType.eq(0).prop('checked', 'checked');
            $usageType.eq(1).prop('disabled', 'disabled');
            $headName.val('个人').prop('disabled', 'disabled');
            $taxAccount.val('');
            $taxAccountBlock.hide();
            $companyBlock.hide();
            form.render('radio');
        } else if (data.value === '2') {
            $usageType.removeAttr('disabled');
            $headName.val('').attr('placeholder','请输入企业名称').removeAttr('disabled');
            $taxAccountBlock.show();
            $companyBlock.show();
            form.render('radio');
        } else if (data.value === '3') {
            $usageType.removeAttr('checked');
            $usageType.eq(0).prop('checked', 'checked');
            $usageType.eq(1).prop('disabled', 'disabled');
            $headName.val('').attr('placeholder','请输入组织名称').removeAttr('disabled');
            $taxAccountBlock.show();
            $companyBlock.hide();
            form.render('radio');
        }
    });

});