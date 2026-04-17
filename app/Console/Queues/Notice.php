<?php
/**
 * @copyright Copyright (c) 2023 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Console\Queues;

use App\Models\Task as TaskModel;
use App\Repos\Task as TaskRepo;
use App\Services\Logic\Notice\External\AccountLogin as AccountLoginNotice;
use App\Services\Logic\Notice\External\ConsultCreate as ConsultCreateNotice;
use App\Services\Logic\Notice\External\ConsultReply as ConsultReplyNotice;
use App\Services\Logic\Notice\External\DistSuccess as DistSuccessNotice;
use App\Services\Logic\Notice\External\InvoiceCreate as InvoiceCreateNotice;
use App\Services\Logic\Notice\External\InvoiceFinish as InvoiceFinishNotice;
use App\Services\Logic\Notice\External\LiveBegin as LiveBeginNotice;
use App\Services\Logic\Notice\External\OrderFinish as OrderFinishNotice;
use App\Services\Logic\Notice\External\PaperGradeFinish as PaperGradeFinishNotice;
use App\Services\Logic\Notice\External\PointGoodsDeliver as PointGoodsDeliverNotice;
use App\Services\Logic\Notice\External\RefundFinish as RefundFinishNotice;
use App\Services\Logic\Notice\External\ServerMonitor as ServerMonitorNotice;
use App\Services\Logic\Notice\External\TeacherLive as TeacherLiveNotice;
use App\Services\Logic\Notice\External\WithdrawFinish as WithdrawFinishNotice;
use App\Traits\Service as ServiceTrait;
use Phalcon\Di\Injectable;

class Notice extends Injectable
{

    use ServiceTrait;

    public function handle($id)
    {
        $taskRepo = new TaskRepo();

        $task = $taskRepo->findById($id);

        try {

            switch ($task->item_type) {
                case TaskModel::TYPE_NOTICE_ACCOUNT_LOGIN:
                    $this->handleAccountLoginNotice($task);
                    break;
                case TaskModel::TYPE_NOTICE_LIVE_BEGIN:
                    $this->handleLiveBeginNotice($task);
                    break;
                case TaskModel::TYPE_NOTICE_ORDER_FINISH:
                    $this->handleOrderFinishNotice($task);
                    break;
                case TaskModel::TYPE_NOTICE_REFUND_FINISH:
                    $this->handleRefundFinishNotice($task);
                    break;
                case TaskModel::TYPE_NOTICE_CONSULT_REPLY:
                    $this->handleConsultReplyNotice($task);
                    break;
                case TaskModel::TYPE_NOTICE_POINT_GOODS_DELIVER:
                    $this->handlePointGoodsDeliverNotice($task);
                    break;
                case TaskModel::TYPE_NOTICE_PAPER_GRADE_FINISH:
                    $this->handlePaperGradeFinishNotice($task);
                    break;
                case TaskModel::TYPE_NOTICE_WITHDRAW_FINISH:
                    $this->handleWithdrawFinishNotice($task);
                    break;
                case TaskModel::TYPE_NOTICE_INVOICE_FINISH:
                    $this->handleInvoiceFinishNotice($task);
                    break;
                case TaskModel::TYPE_NOTICE_DIST_SUCCESS:
                    $this->handleDistSuccessNotice($task);
                    break;
                case TaskModel::TYPE_STAFF_NOTICE_CONSULT_CREATE:
                    $this->handleConsultCreateNotice($task);
                    break;
                case TaskModel::TYPE_STAFF_NOTICE_TEACHER_LIVE:
                    $this->handleTeacherLiveNotice($task);
                    break;
                case TaskModel::TYPE_STAFF_NOTICE_SERVER_MONITOR:
                    $this->handleServerMonitorNotice($task);
                    break;
                case TaskModel::TYPE_STAFF_NOTICE_INVOICE_CREATE:
                    $this->handleInvoiceCreateNotice($task);
                    break;
            }

            $task->status = TaskModel::STATUS_FINISHED;
            $task->update();

        } catch (\Exception $e) {

            $task->status = TaskModel::STATUS_FAILED;
            $task->update();

            $logger = $this->getLogger('queue');

            $logger->error('queue:notice Process Exception: ' . kg_json_encode([
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'message' => $e->getMessage(),
                    'task' => $task,
                ]));
        }
    }

    protected function handleAccountLoginNotice(TaskModel $task)
    {
        $notice = new AccountLoginNotice();

        $notice->handleTask($task);
    }

    protected function handleLiveBeginNotice(TaskModel $task)
    {
        $notice = new LiveBeginNotice();

        $notice->handleTask($task);
    }

    protected function handleOrderFinishNotice(TaskModel $task)
    {
        $notice = new OrderFinishNotice();

        $notice->handleTask($task);
    }

    protected function handleRefundFinishNotice(TaskModel $task)
    {
        $notice = new RefundFinishNotice();

        $notice->handleTask($task);
    }

    protected function handleConsultReplyNotice(TaskModel $task)
    {
        $notice = new ConsultReplyNotice();

        $notice->handleTask($task);
    }

    protected function handlePointGoodsDeliverNotice(TaskModel $task)
    {
        $notice = new PointGoodsDeliverNotice();

        $notice->handleTask($task);
    }

    protected function handlePaperGradeFinishNotice(TaskModel $task)
    {
        $notice = new PaperGradeFinishNotice();

        $notice->handleTask($task);
    }

    protected function handleWithdrawFinishNotice(TaskModel $task)
    {
        $notice = new WithdrawFinishNotice();

        $notice->handleTask($task);
    }

    protected function handleInvoiceFinishNotice(TaskModel $task)
    {
        $notice = new InvoiceFinishNotice();

        $notice->handleTask($task);
    }

    protected function handleDistSuccessNotice(TaskModel $task)
    {
        $notice = new DistSuccessNotice();

        $notice->handleTask($task);
    }

    protected function handleConsultCreateNotice(TaskModel $task)
    {
        $notice = new ConsultCreateNotice();

        $notice->handleTask($task);
    }

    protected function handleTeacherLiveNotice(TaskModel $task)
    {
        $notice = new TeacherLiveNotice();

        $notice->handleTask($task);
    }

    protected function handleServerMonitorNotice(TaskModel $task)
    {
        $notice = new ServerMonitorNotice();

        $notice->handleTask($task);
    }

    protected function handleInvoiceCreateNotice(TaskModel $task)
    {
        $notice = new InvoiceCreateNotice();

        $notice->handleTask($task);
    }

}
