<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Deliver;

use App\Models\WithdrawAccount as WithdrawAccountModel;
use App\Services\Logic\Service as LogicService;
use App\Services\Logic\Withdraw\AccountVerify as WithdrawAccountVerifyService;

class WithdrawAccountDeliver extends LogicService
{

    public function handle(WithdrawAccountModel $account)
    {
        $this->handleWithdrawAccountUser($account);
    }

    protected function handleWithdrawAccountUser(WithdrawAccountModel $account)
    {
        $service = new WithdrawAccountVerifyService();

        $service->handle($account->id);
    }

}
