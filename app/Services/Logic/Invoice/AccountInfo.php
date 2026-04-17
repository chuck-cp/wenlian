<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Invoice;

use App\Models\InvoiceAccount as InvoiceAccountModel;
use App\Services\Logic\Service as LogicService;
use App\Services\Logic\User\ShallowUserInfo;
use App\Validators\InvoiceAccount as InvoiceAccountValidator;

class AccountInfo extends LogicService
{

    public function handle($id)
    {
        $validator = new InvoiceAccountValidator();

        $account = $validator->checkInvoiceAccount($id);

        return $this->handleAccount($account);
    }

    protected function handleAccount(InvoiceAccountModel $account)
    {
        $user = $this->handleUserInfo($account->user_id);

        return [
            'id' => $account->id,
            'usage_type' => $account->usage_type,
            'head_type' => $account->head_type,
            'head_name' => $account->head_name,
            'tax_account' => $account->tax_account,
            'bank_name' => $account->bank_name,
            'bank_account' => $account->bank_account,
            'company_address' => $account->company_address,
            'company_phone' => $account->company_phone,
            'deleted' => $account->deleted,
            'create_time' => $account->create_time,
            'update_time' => $account->update_time,
            'user' => $user,
        ];
    }

    protected function handleUserInfo($userId)
    {
        $service = new ShallowUserInfo();

        return $service->handle($userId);
    }

}
