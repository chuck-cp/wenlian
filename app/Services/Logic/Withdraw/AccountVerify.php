<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Withdraw;

use App\Models\Trade as TradeModel;
use App\Repos\Order as OrderRepo;
use App\Services\Logic\Service as LogicService;
use App\Validators\WithdrawAccount as WithdrawAccountValidator;

class AccountVerify extends LogicService
{

    public function handle($id)
    {
        $validator = new WithdrawAccountValidator();

        $account = $validator->checkWithdrawAccount($id);

        if ($account->verified == 1) return $account;

        $orderRepo = new OrderRepo();

        $trades = $orderRepo->findTrades($account->order_id);

        foreach ($trades as $trade) {

            $finished = $trade->status == TradeModel::STATUS_FINISHED;
            $hasIdentity = !empty($trade->channel_identity);

            if ($finished && $hasIdentity) {
                $account->channel = $trade->channel;
                $account->identity = $trade->channel_identity;
                $account->verified = 1;
                $account->update();
            }
        }

        return $account;
    }

}
