<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Notice\External;

use App\Models\Invoice as InvoiceModel;
use App\Models\InvoiceAccount as InvoiceAccountModel;
use App\Models\Task as TaskModel;
use App\Repos\Invoice as InvoiceRepo;
use App\Repos\InvoiceAccount as InvoiceAccountRepo;
use App\Repos\User as UserRepo;
use App\Services\Logic\Notice\External\Mail\InvoiceFinish as MailInvoiceFinishNotice;
use App\Services\Logic\Notice\External\Sms\InvoiceFinish as SmsInvoiceFinishNotice;
use App\Services\Logic\Notice\External\WeChat\InvoiceFinish as WeChatInvoiceFinishNotice;
use App\Services\Logic\Service as LogicService;

class InvoiceFinish extends LogicService
{

    public function handleTask(TaskModel $task)
    {
        $wechatNoticeEnabled = $this->wechatNoticeEnabled();
        $smsNoticeEnabled = $this->smsNoticeEnabled();
        $mailNoticeEnabled = $this->mailNoticeEnabled();

        $invoiceId = $task->item_id;

        $invoiceRepo = new InvoiceRepo();

        $invoice = $invoiceRepo->findById($invoiceId);

        $invoiceAccountRepo = new InvoiceAccountRepo();

        $invoiceAccount = $invoiceAccountRepo->findById($invoice->account_id);

        $userRepo = new UserRepo();

        $user = $userRepo->findById($invoice->user_id);

        $params = [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
            ],
            'invoice' => [
                'id' => $invoice->id,
                'amount' => $invoice->amount,
                'voucher' => $invoice->voucher,
                'sort_no' => $invoice->sort_no,
                'serial_no' => $invoice->serial_no,
                'post_email' => $invoice->post_email,
                'create_time' => $invoice->create_time,
            ],
            'invoice_account' => [
                'id' => $invoiceAccount->id,
                'head_name' => $invoiceAccount->head_name,
                'head_type' => $this->getHeadTypeText($invoiceAccount->head_type),
                'usage_type' => $this->getUsageTypeText($invoiceAccount->usage_type),
            ],
        ];

        if ($wechatNoticeEnabled) {
            $notice = new WeChatInvoiceFinishNotice();
            $notice->handle($params);
        }

        if ($smsNoticeEnabled) {
            $notice = new SmsInvoiceFinishNotice();
            $notice->handle($params);
        }

        if ($mailNoticeEnabled) {
            $notice = new MailInvoiceFinishNotice();
            $notice->handle($params);
        }
    }

    public function createTask(InvoiceModel $invoice)
    {
        $wechatNoticeEnabled = $this->wechatNoticeEnabled();
        $smsNoticeEnabled = $this->smsNoticeEnabled();
        $mailNoticeEnabled = $this->mailNoticeEnabled();

        if (!$wechatNoticeEnabled && !$smsNoticeEnabled && !$mailNoticeEnabled) return;

        $task = new TaskModel();

        $task->item_id = $invoice->id;
        $task->item_type = TaskModel::TYPE_NOTICE_INVOICE_FINISH;
        $task->priority = TaskModel::PRIORITY_MIDDLE;
        $task->status = TaskModel::STATUS_PENDING;

        $task->create();
    }

    public function wechatNoticeEnabled()
    {
        $oa = $this->getSettings('wechat.oa');

        if ($oa['enabled'] == 0) return false;

        $template = json_decode($oa['notice_template'], true);

        $result = $template['invoice_finish']['enabled'] ?? 0;

        return $result == 1;
    }

    public function smsNoticeEnabled()
    {
        $sms = $this->getSettings('sms');

        $template = json_decode($sms['template'], true);

        $result = $template['invoice_finish']['enabled'] ?? 0;

        return $result == 1;
    }

    public function mailNoticeEnabled()
    {
        return true;
    }

    protected function getUsageTypeText($type)
    {
        $types = InvoiceAccountModel::usageTypes();

        return $types[$type] ?? 'N/A';
    }

    protected function getHeadTypeText($type)
    {
        $types = InvoiceAccountModel::headTypes();

        return $types[$type] ?? 'N/A';
    }

}
