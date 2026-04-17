<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Withdraw;

use App\Models\Withdraw as WithdrawModel;
use App\Repos\WithdrawAccount as WithdrawAccountRepo;
use App\Services\Logic\Service as LogicService;
use App\Services\Logic\User\ShallowUserInfo;
use App\Validators\Withdraw as WithdrawValidator;

class WithdrawInfo extends LogicService
{

    public function handle($id)
    {
        $validator = new WithdrawValidator();

        $withdraw = $validator->checkById($id);

        return $this->handleWithdraw($withdraw);
    }

    protected function handleWithdraw(WithdrawModel $withdraw)
    {
        $withdrawAccount = $this->handleWithdrawAccountInfo($withdraw->account_id);
        $user = $this->handleUserInfo($withdraw->user_id);

        return [
            'id' => $withdraw->id,
            'sn' => $withdraw->sn,
            'apply_amount' => $withdraw->apply_amount,
            'trans_amount' => $withdraw->trans_amount,
            'service_fee' => $withdraw->service_fee,
            'tax_fee' => $withdraw->tax_fee,
            'apply_note' => $withdraw->apply_note,
            'review_note' => $withdraw->review_note,
            'status' => $withdraw->status,
            'transferred' => $withdraw->transferred,
            'deleted' => $withdraw->deleted,
            'create_time' => $withdraw->create_time,
            'update_time' => $withdraw->update_time,
            'withdraw_account' => $withdrawAccount,
            'user' => $user,
        ];
    }

    protected function handleWithdrawAccountInfo($id)
    {
        $accountRepo = new WithdrawAccountRepo();

        $account = $accountRepo->findById($id);

        return [
            'id' => $account->id,
            'name' => $account->name,
            'channel' => $account->channel,
            'account' => $account->account,
        ];
    }

    protected function handleUserInfo($userId)
    {
        $service = new ShallowUserInfo();

        return $service->handle($userId);
    }

}
