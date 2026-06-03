<?php
/**
 * @copyright Copyright (c) 2023 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Console\Queues;

use App\Console\Queues\Main\AffiliateSettle as AffiliateSettleService;
use App\Console\Queues\Main\Deliver as DeliverService;
use App\Console\Queues\Main\GrouponDeliver as GrouponDeliverService;
use App\Console\Queues\Main\PointGiftDeliver as PointGiftDeliverService;
use App\Console\Queues\Main\Refund as RefundService;
use App\Console\Queues\Main\WithdrawRefund as WithdrawRefundService;
use App\Console\Queues\Main\WithdrawSettle as WithdrawSettleService;
use App\Models\Task as TaskModel;
use App\Repos\Task as TaskRepo;
use App\Traits\Service as ServiceTrait;
use Phalcon\Di\Injectable;

class Main extends Injectable
{

    use ServiceTrait;

    public function handle($id)
    {
        $taskRepo = new TaskRepo();

        $task = $taskRepo->findById($id);

        try {

            switch ($task->item_type) {
                case TaskModel::TYPE_DELIVER:
                    $this->handleDeliver($task);
                    break;
                case TaskModel::TYPE_REFUND:
                    $this->handleRefund($task);
                    break;
                case TaskModel::TYPE_POINT_GIFT_DELIVER:
                    $this->handlePointGiftDeliver($task);
                    break;
                case TaskModel::TYPE_LUCKY_GIFT_DELIVER:
                    $this->handleLuckyGiftDeliver($task);
                    break;
                case TaskModel::TYPE_AFFILIATE_SETTLE:
                    $this->handleAffiliateSettle($task);
                    break;
                case TaskModel::TYPE_WITHDRAW_SETTLE:
                    $this->handleWithdrawSettle($task);
                    break;
                case TaskModel::TYPE_GROUPON_DELIVER:
                    $this->handleGrouponDeliver($task);
                    break;
                case TaskModel::TYPE_WITHDRAW_REFUND:
                    $this->handleWithdrawRefund($task);
                    break;
            }

            $task->status = TaskModel::STATUS_FINISHED;
            $task->update();

        } catch (\Exception $e) {

            $task->status = TaskModel::STATUS_FAILED;
            $task->update();

            $logger = $this->getLogger('queue');

            $logger->error('queue:main Process Exception: ' . kg_json_encode([
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'message' => $e->getMessage(),
                    'task' => $task,
                ]));
        }
    }

    protected function handleDeliver(TaskModel $task)
    {
        $service = new DeliverService();

        $service->handle($task);
    }

    protected function handleRefund(TaskModel $task)
    {
        $service = new RefundService();

        $service->handle($task);
    }

    protected function handlePointGiftDeliver(TaskModel $task)
    {
        $service = new PointGiftDeliverService();

        $service->handle($task);
    }

    protected function handleLuckyGiftDeliver(TaskModel $task)
    {

    }

    protected function handleAffiliateSettle(TaskModel $task)
    {
        $service = new AffiliateSettleService();

        $service->handle($task);
    }

    protected function handleGrouponDeliver(TaskModel $task)
    {
        $service = new GrouponDeliverService();

        $service->handle($task);
    }

    protected function handleWithdrawSettle(TaskModel $task)
    {
        $service = new WithdrawSettleService();

        $service->handle($task);
    }

    protected function handleWithdrawRefund(TaskModel $task)
    {
        $service = new WithdrawRefundService();

        $service->handle($task);
    }

}
