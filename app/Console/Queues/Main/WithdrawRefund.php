<?php
/**
 * @copyright Copyright (c) 2023 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Console\Queues\Main;

use App\Models\CashHistory as CashHistoryModel;
use App\Models\Task as TaskModel;
use App\Models\Withdraw as WithdrawModel;
use App\Repos\User as UserRepo;
use App\Repos\Withdraw as WithdrawRepo;
use App\Traits\Service as ServiceTrait;
use Phalcon\Di\Injectable;

class WithdrawRefund extends Injectable
{

    use ServiceTrait;

    public function handle(TaskModel $task)
    {
        echo '------ start withdraw refund task ------' . PHP_EOL;

        $withdrawRepo = new WithdrawRepo();
        $userRepo = new UserRepo();

        $withdraw = $withdrawRepo->findById($task->item_id);
        $user = $userRepo->findById($withdraw->user_id);
        $balance = $userRepo->findUserBalance($user->id);

        try {

            $this->db->begin();

            $cashHistory = new CashHistoryModel();

            $eventAmount = $withdraw->apply_amount;

            $eventInfo = [
                'withdraw' => ['sn' => $withdraw->sn],
            ];

            $cashHistory->user_id = $user->id;
            $cashHistory->user_name = $user->name;
            $cashHistory->event_id = $withdraw->id;
            $cashHistory->event_type = CashHistoryModel::EVENT_WITHDRAW_REFUND;
            $cashHistory->event_info = $eventInfo;
            $cashHistory->event_amount = $eventAmount;
            $cashHistory->create();

            $balance->cash += $withdraw->apply_amount;
            $balance->update();

            $withdraw->status = WithdrawModel::STATUS_REFUNDED;
            $withdraw->update();

            $task->status = TaskModel::STATUS_FINISHED;
            $task->update();

            $this->handleWithdrawRefundFinishNotice($withdraw);

            $this->db->commit();

        } catch (\Exception $e) {

            $this->db->rollback();

            $task->status = TaskModel::STATUS_FAILED;
            $task->update();

            $logger = $this->getLogger('refund');

            $logger->error('Withdraw Refund Exception: ' . kg_json_encode([
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'message' => $e->getMessage(),
                    'task' => $task->toArray(),
                ]));
        }

        echo '------ end withdraw refund task ------' . PHP_EOL;
    }

    /**
     * @param WithdrawModel $withdraw
     */
    protected function handleWithdrawRefundFinishNotice(WithdrawModel $withdraw)
    {
        /**
         * @todo 提现退款通知
         */
    }

}
