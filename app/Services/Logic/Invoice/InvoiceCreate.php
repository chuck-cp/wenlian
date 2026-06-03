<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Invoice;

use App\Models\Invoice as InvoiceModel;
use App\Repos\User as UserRepo;
use App\Services\Logic\Notice\External\InvoiceCreate as InvoiceCreateTask;
use App\Services\Logic\Service as LogicService;
use App\Validators\Invoice as InvoiceValidator;
use App\Validators\InvoiceAccount as InvoiceAccountValidator;

class InvoiceCreate extends LogicService
{

    public function handle()
    {
        $post = $this->request->getPost();

        $user = $this->getLoginUser();

        $validator = new InvoiceValidator();

        $validator->checkIfAllowApply($user->id);
        $validator->checkLoginPassword($user->id, $post['login_password']);

        $account = $validator->checkInvoiceAccount($post['account_id']);
        $postEmail = $validator->checkPostEmail($post['post_email']);
        $amount = $validator->checkAmount($user->id, $post['amount']);

        $validator = new InvoiceAccountValidator();

        $validator->checkUsageType($account->usage_type);

        try {

            $userRepo = new UserRepo();

            $balance = $userRepo->findUserBalance($user->id);

            $this->db->begin();

            $invoice = new InvoiceModel();

            $invoice->user_id = $user->id;
            $invoice->account_id = $account->id;
            $invoice->post_email = $postEmail;
            $invoice->amount = $amount;
            $invoice->status = InvoiceModel::STATUS_PENDING;

            $invoice->create();

            $balance->invoice -= $invoice->amount;

            $balance->update();

            $this->handleInvoiceCreateNotice($invoice);

            $this->db->commit();

            return $invoice;

        } catch (\Exception $e) {

            $this->db->rollback();

            $logger = $this->getLogger('invoice');

            $logger->error('Create Invoice Exception: ' . kg_json_encode([
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'message' => $e->getMessage(),
                    'post' => $post,
                ]));

            throw new \RuntimeException('sys.trans_rollback');
        }
    }

    protected function handleInvoiceCreateNotice(InvoiceModel $invoice)
    {
        $notice = new InvoiceCreateTask();

        $notice->createTask($invoice);
    }

}
