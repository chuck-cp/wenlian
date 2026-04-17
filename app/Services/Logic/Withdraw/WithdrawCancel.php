<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Withdraw;

use App\Models\Task as TaskModel;
use App\Models\Withdraw as WithdrawModel;
use App\Services\Logic\Service as LogicService;
use App\Validators\Withdraw as WithdrawValidator;

class WithdrawCancel extends LogicService
{

    public function handle($id)
    {
        $user = $this->getLoginUser();

        $validator = new WithdrawValidator();

        $withdraw = $validator->checkById($id);

        $validator->checkIfAllowCancel($withdraw);

        $validator->checkOwner($user->id, $withdraw->user_id);

        try {

            $this->db->begin();

            $task = new TaskModel();

            $itemInfo = [
                'withdraw' => ['id' => $withdraw->id],
            ];

            $task->item_id = $withdraw->id;
            $task->item_info = $itemInfo;
            $task->item_type = TaskModel::TYPE_WITHDRAW_REFUND;
            $task->priority = TaskModel::PRIORITY_HIGH;
            $task->status = TaskModel::STATUS_PENDING;
            $task->create();

            $withdraw->status = WithdrawModel::STATUS_CANCELED;
            $withdraw->update();

            $this->db->commit();

            return $withdraw;

        } catch (\Exception $e) {

            $this->db->rollback();

            $logger = $this->getLogger('withdraw');

            $logger->error('Cancel Withdraw Exception: ' . kg_json_encode([
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'message' => $e->getMessage(),
                    'withdraw' => $withdraw->toArray(),
                ]));

            throw new \RuntimeException('sys.rollback');
        }
    }

}
