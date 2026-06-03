<?php
/**
 * @copyright Copyright (c) 2023 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Console\Queues\Main;

use App\Models\Task as TaskModel;
use App\Models\Trade as TradeModel;
use App\Models\Withdraw as WithdrawModel;
use App\Repos\Withdraw as WithdrawRepo;
use App\Repos\WithdrawAccount as WithdrawAccountRepo;
use App\Services\Logic\Notice\External\WithdrawFinish as WithdrawFinishNotice;
use App\Services\Pay\Alipay as AlipayService;
use App\Services\Pay\Wxpay as WxpayService;
use App\Traits\Service as ServiceTrait;
use Phalcon\Di\Injectable;

class WithdrawSettle extends Injectable
{

    use ServiceTrait;

    public function handle(TaskModel $task)
    {
        echo '------ start withdraw settle task ------' . PHP_EOL;

        $accountRepo = new WithdrawAccountRepo();
        $withdrawRepo = new WithdrawRepo();

        $withdraw = $withdrawRepo->findById($task->item_id);
        $account = $accountRepo->findById($withdraw->account_id);

        if ($withdraw->status != WithdrawModel::STATUS_APPROVED) {
            $task->status = TaskModel::STATUS_CANCELED;
            $task->update();
            return;
        }

        try {

            $this->db->begin();

            $this->handleWithdrawChannelTransfer($withdraw, $account->channel);

            $withdraw->status = WithdrawModel::STATUS_FINISHED;
            $withdraw->transferred = 1;
            $withdraw->update();

            $task->status = TaskModel::STATUS_FINISHED;
            $task->update();

            $this->handleWithdrawFinishNotice($withdraw);

            $this->db->commit();

        } catch (\Exception $e) {

            $this->db->rollback();

            $task->status = TaskModel::STATUS_FAILED;
            $task->update();

            $withdraw->status = WithdrawModel::STATUS_FAILED;
            $withdraw->update();

            $this->handleWithdrawRefund($withdraw);

            $logger = $this->getLogger('withdraw');

            $logger->error('Withdraw Settle Task Exception: ' . kg_json_encode([
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'message' => $e->getMessage(),
                    'task' => $task->toArray(),
                ]));
        }

        echo '------ end withdraw settle task ------' . PHP_EOL;
    }

    /**
     * 处理平台转账
     *
     * @param WithdrawModel $withdraw
     * @param int $channel
     */
    protected function handleWithdrawChannelTransfer(WithdrawModel $withdraw, $channel)
    {
        if ($withdraw->status == WithdrawModel::STATUS_FINISHED) return;

        $response = false;

        if ($channel == TradeModel::CHANNEL_ALIPAY) {

            $alipay = new AlipayService();

            $response = $alipay->withdraw($withdraw);

        } elseif ($channel == TradeModel::CHANNEL_WXPAY) {

            $wxpay = new WxpayService();

            $response = $wxpay->withdraw($withdraw);
        }

        if (!$response) {
            throw new \RuntimeException('Channel Transfer Failed');
        }
    }

    protected function handleWithdrawRefund(WithdrawModel $withdraw)
    {
        $task = new TaskModel();

        $task->item_id = $withdraw->id;
        $task->item_type = TaskModel::TYPE_WITHDRAW_REFUND;
        $task->priority = TaskModel::PRIORITY_HIGH;
        $task->status = TaskModel::STATUS_PENDING;

        $task->create();
    }

    protected function handleWithdrawFinishNotice(WithdrawModel $withdraw)
    {
        $notice = new WithdrawFinishNotice();

        $notice->createTask($withdraw);
    }

}
