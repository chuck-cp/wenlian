<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Invoice;

use App\Models\InvoiceAccount as InvoiceAccountModel;
use App\Models\User as UserModel;
use App\Repos\InvoiceAccount as InvoiceAccountRepo;
use App\Services\Logic\Service as LogicService;
use App\Validators\InvoiceAccount as InvoiceAccountValidator;

class AccountCreate extends LogicService
{

    public function handle()
    {
        $post = $this->request->getPost();

        $user = $this->getLoginUser();

        $validator = new InvoiceAccountValidator();

        $validator->checkAccountLimit($user->id);

        $headType = $validator->checkHeadType($post['head_type']);
        $usageType = $validator->checkUsageType($post['usage_type']);
        $headName = $validator->checkHeadName($post['head_name']);

        $accountRepo = new InvoiceAccountRepo();

        $myAccounts = $accountRepo->findByUserId($user->id);

        foreach ($myAccounts as $myAccount) {

            $case1 = $myAccount->usage_type == $usageType;
            $case2 = $myAccount->head_type == $headType;
            $case3 = $myAccount->head_name == $headName;
            $case4 = $myAccount->deleted == 0;

            if ($case1 && $case2 && $case3 && $case4) {
                return $myAccount;
            }
        }

        $account = new InvoiceAccountModel();

        switch ($headType) {
            case InvoiceAccountModel::HEAD_TYPE_PERSON:
                $account = $this->createPersonAccount($user, $post);
                break;
            case InvoiceAccountModel::HEAD_TYPE_COMPANY:
                $account = $this->createCompanyAccount($user, $post);
                break;
            case InvoiceAccountModel::HEAD_TYPE_ORG:
                $account = $this->createOrgAccount($user, $post);
                break;
        }

        return $account;
    }

    protected function createPersonAccount(UserModel $user, $post)
    {
        $account = new InvoiceAccountModel();

        $validator = new InvoiceAccountValidator();

        $account->head_name = $validator->checkHeadName($post['head_name']);
        $account->usage_type = InvoiceAccountModel::USAGE_TYPE_NORMAL;
        $account->head_type = InvoiceAccountModel::HEAD_TYPE_PERSON;
        $account->user_id = $user->id;

        $account->create();

        return $account;
    }

    protected function createOrgAccount(UserModel $user, $post)
    {
        $account = new InvoiceAccountModel();

        $validator = new InvoiceAccountValidator();

        $account->user_id = $user->id;
        $account->usage_type = InvoiceAccountModel::USAGE_TYPE_NORMAL;
        $account->head_type = InvoiceAccountModel::HEAD_TYPE_ORG;
        $account->head_name = $validator->checkHeadName($post['head_name']);

        if (!empty($post['tax_account'])) {
            $account->tax_account = $validator->checkTaxAccount($post['tax_account']);
        }

        $account->create();

        return $account;
    }

    protected function createCompanyAccount(UserModel $user, $post)
    {
        $account = new InvoiceAccountModel();

        $validator = new InvoiceAccountValidator();

        $account->user_id = $user->id;
        $account->head_type = InvoiceAccountModel::HEAD_TYPE_COMPANY;
        $account->head_name = $validator->checkHeadName($post['head_name']);
        $account->usage_type = $validator->checkUsageType($post['usage_type']);
        $account->tax_account = $validator->checkTaxAccount($post['tax_account']);

        if ($account->usage_type == InvoiceAccountModel::USAGE_TYPE_NORMAL) {

            if (!empty($post['bank_name'])) {
                $account->bank_name = $validator->checkBankName($post['bank_name']);
            }

            if (!empty($post['bank_account'])) {
                $account->bank_account = $validator->checkBankAccount($post['bank_account']);
            }

            if (!empty($post['company_address'])) {
                $account->company_address = $validator->checkCompanyAddress($post['company_address']);
            }

            if (!empty($post['company_phone'])) {
                $account->company_phone = $validator->checkCompanyPhone($post['company_phone']);
            }

        } elseif ($account->usage_type == InvoiceAccountModel::USAGE_TYPE_SPECIAL) {

            $account->bank_name = $validator->checkBankName($post['bank_name']);
            $account->bank_account = $validator->checkBankAccount($post['bank_account']);
            $account->company_address = $validator->checkCompanyAddress($post['company_address']);
            $account->company_phone = $validator->checkCompanyPhone($post['company_phone']);
        }

        $account->create();

        return $account;
    }

}
