<?php
/**
 * @copyright Copyright (c) 2023 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Notice\External;

use App\Models\Invoice as InvoiceModel;
use App\Models\Task as TaskModel;
use App\Repos\Account as AccountRepo;
use App\Repos\Invoice as InvoiceRepo;
use App\Repos\User as UserRepo;
use App\Services\Logic\Notice\External\DingTalk\InvoiceCreate as DingTalkInvoiceCreateNotice;
use App\Services\Logic\Notice\External\WeWork\InvoiceCreate as WeWorkInvoiceCreateNotice;
use App\Services\Logic\Service as LogicService;

class InvoiceCreate extends LogicService
{

    use RobotTrait;

    public function handleTask(TaskModel $task)
    {
        $weworkNoticeEnabled = $this->weworkNoticeEnabled();
        $dingtalkNoticeEnabled = $this->dingtalkNoticeEnabled();

        $invoiceRepo = new InvoiceRepo();

        $invoice = $invoiceRepo->findById($task->item_id);

        $userRepo = new UserRepo();

        $user = $userRepo->findById($invoice->user_id);

        $accountRepo = new AccountRepo();

        $account = $accountRepo->findById($invoice->user_id);

        $params = [
            'account' => [
                'email' => $account->email,
                'phone' => $account->phone,
            ],
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
            ],
            'invoice' => [
                'id' => $invoice->id,
                'amount' => $invoice->amount,
            ],
        ];

        if ($weworkNoticeEnabled) {
            $notice = new WeWorkInvoiceCreateNotice();
            $notice->handle($params);
        }

        if ($dingtalkNoticeEnabled) {
            $notice = new DingTalkInvoiceCreateNotice();
            $notice->handle($params);
        }
    }

    public function createTask(InvoiceModel $invoice)
    {
        $weworkNoticeEnabled = $this->weworkNoticeEnabled();
        $dingtalkNoticeEnabled = $this->dingtalkNoticeEnabled();

        if (!$weworkNoticeEnabled && !$dingtalkNoticeEnabled) return;

        $task = new TaskModel();

        $task->item_id = $invoice->id;
        $task->item_type = TaskModel::TYPE_STAFF_NOTICE_INVOICE_CREATE;
        $task->priority = TaskModel::PRIORITY_LOW;
        $task->status = TaskModel::STATUS_PENDING;

        $task->create();
    }

}
