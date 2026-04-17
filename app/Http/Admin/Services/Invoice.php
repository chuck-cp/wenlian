<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Admin\Services;

use App\Builders\InvoiceList as InvoiceListBuilder;
use App\Http\Admin\Services\Traits\AccountSearchTrait;
use App\Library\Paginator\Query as PaginateQuery;
use App\Models\Invoice as InvoiceModel;
use App\Models\InvoiceAccount as InvoiceAccountModel;
use App\Repos\Account as AccountRepo;
use App\Repos\Invoice as InvoiceRepo;
use App\Repos\InvoiceAccount as InvoiceAccountRepo;
use App\Repos\User as UserRepo;
use App\Services\Logic\Notice\External\InvoiceFinish as InvoiceFinishNotice;
use App\Validators\Invoice as InvoiceValidator;

class Invoice extends Service
{

    use AccountSearchTrait;

    public function getStatusTypes()
    {
        return InvoiceModel::statusTypes();
    }

    public function getHeadTypes()
    {
        return InvoiceAccountModel::headTypes();
    }

    public function getUsageTypes()
    {
        return InvoiceAccountModel::usageTypes();
    }

    public function getMediaTypes()
    {
        return InvoiceModel::mediaTypes();
    }

    public function getInvoices()
    {
        $pageQuery = new PaginateQuery();

        $params = $pageQuery->getParams();

        $params = $this->handleAccountSearchParams($params);

        $params['deleted'] = $params['deleted'] ?? 0;

        $sort = $pageQuery->getSort();
        $page = $pageQuery->getPage();
        $limit = $pageQuery->getLimit();

        $invoiceRepo = new InvoiceRepo();

        $pager = $invoiceRepo->paginate($params, $sort, $page, $limit);

        return $this->handleInvoices($pager);
    }

    public function getInvoice($id)
    {
        return $this->findOrFail($id);
    }

    public function getStatusHistory($id)
    {
        $invoiceRepo = new InvoiceRepo();

        return $invoiceRepo->findStatusHistory($id);
    }

    public function getInvoiceAccount($id)
    {
        $accountRepo = new InvoiceAccountRepo();

        return $accountRepo->findById($id);
    }

    public function getUser($id)
    {
        $userRepo = new UserRepo();

        return $userRepo->findById($id);
    }

    public function getAccount($id)
    {
        $accountRepo = new AccountRepo();

        return $accountRepo->findById($id);
    }

    public function reviewInvoice($id)
    {
        $invoice = $this->findOrFail($id);

        $post = $this->request->getPost();

        $validator = new InvoiceValidator();

        $validator->checkIfAllowReview($invoice);

        $invoice->status = $validator->checkReviewStatus($post['review_status']);
        $invoice->review_note = $validator->checkReviewNote($post['review_note']);

        try {

            $this->db->begin();

            $invoice->update();

            if ($invoice->status == InvoiceModel::STATUS_APPROVED) {

                $this->handleInvoiceApprovedNotice($invoice);

            } else {

                $userRepo = new UserRepo();

                $balance = $userRepo->findUserBalance($invoice->user_id);

                $balance->invoice += $invoice->amount;

                $balance->update();

                $this->handleInvoiceRefusedNotice($invoice);
            }

            $this->db->commit();

        } catch (\Exception $e) {

            $this->db->rollback();

            $logger = $this->getLogger('invoice');

            $logger->error('Invoice Review Exception: ' . kg_json_encode([
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'message' => $e->getMessage(),
                    'invoice' => $invoice,
                ]));

            throw new \RuntimeException('sys.rollback');
        }

        return $invoice;
    }

    public function saveVoucher($id)
    {
        $invoice = $this->findOrFail($id);

        $post = $this->request->getPost();

        $validator = new InvoiceValidator();

        $fullNo = $validator->checkFullNo($post['full_no']);

        $invoice->voucher = $validator->checkVoucher($post['voucher']);
        $invoice->sort_no = substr($fullNo, 0,12);
        $invoice->serial_no = substr($fullNo, 12, 8);

        try {

            $this->db->begin();

            if ($invoice->status == InvoiceModel::STATUS_APPROVED) {

                $invoice->status = InvoiceModel::STATUS_FINISHED;

                $notice = new InvoiceFinishNotice();

                $notice->createTask($invoice);
            }

            $invoice->update();

            $this->db->commit();

        } catch (\Exception $e) {

            $this->db->rollback();

            $logger = $this->getLogger('invoice');

            $logger->error('Save Invoice Voucher Exception: ' . kg_json_encode([
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'message' => $e->getMessage(),
                    'invoice' => $invoice->toArray(),
                    'post' => $post,
                ]));

            throw new \RuntimeException('sys.rollback');
        }

        return $invoice;
    }

    protected function handleInvoiceApprovedNotice(InvoiceModel $invoice)
    {
        /**
         * @todo 开票过审通知
         */
    }

    protected function handleInvoiceRefusedNotice(InvoiceModel $invoice)
    {
        /**
         * @todo 开票拒审通知
         */
    }

    protected function findOrFail($id)
    {
        $validator = new InvoiceValidator();

        return $validator->checkInvoice($id);
    }

    protected function handleInvoices($pager)
    {
        if ($pager->total_items > 0) {

            $builder = new InvoiceListBuilder();

            $pipeA = $pager->items->toArray();
            $pipeB = $builder->handleUsers($pipeA);
            $pipeC = $builder->handleAccounts($pipeB);
            $pipeD = $builder->objects($pipeC);

            $pager->items = $pipeD;
        }

        return $pager;
    }

}
