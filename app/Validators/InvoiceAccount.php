<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Validators;

use App\Exceptions\BadRequest as BadRequestException;
use App\Models\InvoiceAccount as InvoiceAccountModel;
use App\Repos\InvoiceAccount as InvoiceAccountRepo;

class InvoiceAccount extends Validator
{

    public function checkInvoiceAccount($id)
    {
        $accountRepo = new InvoiceAccountRepo();

        $account = $accountRepo->findById($id);

        if (!$account) {
            throw new BadRequestException('invoice_account.not_found');
        }

        return $account;
    }

    public function checkUsageType($type)
    {
        $usageTypes = InvoiceAccountModel::usageTypes();

        if (!array_key_exists($type, $usageTypes)) {
            throw new BadRequestException('invoice_account.invalid_usage_type');
        }

        $enabledUsageTypes = InvoiceAccountModel::getEnabledUsageTypes();

        if (!array_key_exists($type, $enabledUsageTypes)) {
            throw new BadRequestException('invoice_account.usage_type_disabled');
        }

        return $type;
    }

    public function checkHeadType($type)
    {
        if (!array_key_exists($type, InvoiceAccountModel::headTypes())) {
            throw new BadRequestException('invoice_account.invalid_head_type');
        }

        return $type;
    }

    public function checkHeadName($name)
    {
        $value = $this->filter->sanitize($name, ['trim', 'string']);

        $length = kg_strlen($value);

        if ($length < 2 || $length > 30) {
            throw new BadRequestException('invoice_account.invalid_head_name');
        }

        return $value;
    }

    public function checkTaxAccount($account)
    {
        $value = $this->filter->sanitize($account, ['trim', 'string']);

        $length = kg_strlen($value);

        $case1 = in_array($length, [15, 18, 20]);
        $case2 = preg_match('/^\w+$/', $value);

        if (!$case1 || !$case2) {
            throw new BadRequestException('invoice_account.invalid_tax_account');
        }

        return $value;
    }

    public function checkBankName($name)
    {
        $value = $this->filter->sanitize($name, ['trim', 'string']);

        $length = kg_strlen($value);

        if ($length < 2 || $length > 30) {
            throw new BadRequestException('invoice_account.invalid_bank_name');
        }

        return $value;
    }

    public function checkBankAccount($account)
    {
        $value = $this->filter->sanitize($account, ['trim', 'int']);

        if (!preg_match('/^[0-9]{12,20}$/', $value)) {
            throw new BadRequestException('invoice_account.invalid_bank_account');
        }

        return $value;
    }

    public function checkCompanyAddress($address)
    {
        $value = $this->filter->sanitize($address, ['trim', 'string']);

        $length = kg_strlen($value);

        if ($length < 2 || $length > 30) {
            throw new BadRequestException('invoice_account.invalid_company_address');
        }

        return $value;
    }

    public function checkCompanyPhone($phone)
    {
        $value = $this->filter->sanitize($phone, ['trim', 'string']);

        $length = kg_strlen($value);

        if ($length < 11 || $length > 16) {
            throw new BadRequestException('invoice_account.invalid_company_phone');
        }

        return $value;
    }

    public function checkAccountLimit($userId)
    {
        $repo = new InvoiceAccountRepo();

        $accounts = $repo->findByUserId($userId);

        if ($accounts->count() > 10) {
            throw new BadRequestException('invoice_account.reach_account_limit');
        }
    }

}
