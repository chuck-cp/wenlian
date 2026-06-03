<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Withdraw;

use App\Models\CashHistory as CashHistoryModel;
use App\Models\Task as TaskModel;
use App\Models\Withdraw as WithdrawModel;
use App\Repos\User as UserRepo;
use App\Services\Logic\Service as LogicService;
use App\Validators\Withdraw as WithdrawValidator;

class WithdrawCreate extends LogicService
{

    public function handle()
    {
        $post = $this->request->getPost();

        $user = $this->getLoginUser();

        $validator = new WithdrawValidator();

        $account = $validator->checkAccount($post['account_id']);

        $validator->checkIfAllowApply($user->id, $account->id);

        $applyAmount = $validator->checkAmount($user->id, $post['apply_amount']);

        $validator->checkLoginPassword($user->id, $post['login_password']);

        try {

            $this->db->begin();

            $withdraw = new WithdrawModel();

            $serviceFee = $this->getServiceFee($applyAmount);

            $transAmount = round($applyAmount - $serviceFee, 2);

            $withdraw->user_id = $user->id;
            $withdraw->account_id = $account->id;
            $withdraw->apply_amount = $applyAmount;
            $withdraw->trans_amount = $transAmount;
            $withdraw->service_fee = $serviceFee;
            $withdraw->status = $this->getWithdrawStatus();
            $withdraw->create();

            $cashHistory = new CashHistoryModel();

            $eventAmount = 0 - $withdraw->apply_amount;

            $eventInfo = [
                'withdraw' => ['sn' => $withdraw->sn],
            ];

            $cashHistory->user_id = $user->id;
            $cashHistory->user_name = $user->name;
            $cashHistory->event_id = $withdraw->id;
            $cashHistory->event_type = CashHistoryModel::EVENT_WITHDRAW_APPLY;
            $cashHistory->event_info = $eventInfo;
            $cashHistory->event_amount = $eventAmount;
            $cashHistory->create();

            $userRepo = new UserRepo();

            $balance = $userRepo->findUserBalance($user->id);

            $balance->cash -= $withdraw->apply_amount;
            $balance->update();

            if ($withdraw->status == WithdrawModel::STATUS_APPROVED) {
                $this->createWithdrawSettleTask($withdraw);
            }

            $this->db->commit();

            return $withdraw;

        } catch (\Exception $e) {

            $this->db->rollback();

            $logger = $this->getLogger('withdraw');

            $logger->error('Create Withdraw Exception: ' . kg_json_encode([
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'message' => $e->getMessage(),
                    'post' => $post,
                ]));

            throw new \RuntimeException('sys.trans_rollback');
        }
    }

    protected function createWithdrawSettleTask(WithdrawModel $withdraw)
    {
        $task = new TaskModel();

        $task->item_id = $withdraw->id;
        $task->item_type = TaskModel::TYPE_WITHDRAW_SETTLE;
        $task->priority = TaskModel::PRIORITY_MIDDLE;
        $task->status = TaskModel::STATUS_PENDING;
        $task->create();

        return $task;
    }

    protected function getServiceFee($applyAmount)
    {
        $settings = $this->getSettings('withdraw');

        return round($applyAmount * $settings['service_rate'] / 100, 2);
    }

    protected function getWithdrawStatus()
    {
        $settings = $this->getSettings('withdraw');

        $status = WithdrawModel::STATUS_APPROVED;

        if ($settings['review_type'] == WithdrawModel::REVIEW_TYPE_MANUAL) {
            $status = WithdrawModel::STATUS_PENDING;
        }

        return $status;
    }

}
