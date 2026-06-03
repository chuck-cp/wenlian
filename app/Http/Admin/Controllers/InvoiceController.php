<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Admin\Controllers;

use App\Http\Admin\Services\Invoice as InvoiceService;
use App\Http\Admin\Services\InvoiceExport as InvoiceExportService;

/**
 * @RoutePrefix("/admin/invoice")
 */
class InvoiceController extends Controller
{

    /**
     * @Get("/search", name="admin.invoice.search")
     */
    public function searchAction()
    {
        $invoiceService = new InvoiceService();

        $statusTypes = $invoiceService->getStatusTypes();
        $usageTypes = $invoiceService->getUsageTypes();
        $headTypes = $invoiceService->getHeadTypes();
        $mediaTypes = $invoiceService->getMediaTypes();

        $this->view->setVar('status_types', $statusTypes);
        $this->view->setVar('usage_types', $usageTypes);
        $this->view->setVar('head_types', $headTypes);
        $this->view->setVar('media_types', $mediaTypes);
    }

    /**
     * @Get("/list", name="admin.invoice.list")
     */
    public function listAction()
    {
        $invoiceService = new InvoiceService();

        $pager = $invoiceService->getInvoices();

        $this->view->setVar('pager', $pager);
    }

    /**
     * @Get("/export", name="admin.invoice.export")
     */
    public function exportAction()
    {
        $exportService = new InvoiceExportService();

        $result = $exportService->handle();

        if (is_null($result)) {
            $location = $this->url->get(
                ['for' => 'admin.invoice.search'],
                ['target' => 'export', 'count' => 0]
            );
            return $this->response->redirect($location);
        }

        exit();
    }

    /**
     * @Get("/{id:[0-9]+}/show", name="admin.invoice.show")
     */
    public function showAction($id)
    {
        $invoiceService = new InvoiceService();

        $invoice = $invoiceService->getInvoice($id);
        $invoiceAccount = $invoiceService->getInvoiceAccount($invoice->account_id);

        $this->view->setVar('invoice', $invoice);
        $this->view->setVar('invoice_account', $invoiceAccount);
    }

    /**
     * @Get("/{id:[0-9]+}/status/history", name="admin.invoice.status_history")
     */
    public function statusHistoryAction($id)
    {
        $invoiceService = new InvoiceService();

        $statusHistory = $invoiceService->getStatusHistory($id);

        $this->view->pick('invoice/status_history');
        $this->view->setVar('status_history', $statusHistory);
    }

    /**
     * @Post("/{id:[0-9]+}/review", name="admin.invoice.review")
     */
    public function reviewAction($id)
    {
        $invoiceService = new InvoiceService();

        $invoiceService->reviewInvoice($id);

        $location = $this->request->getHTTPReferer();

        $content = [
            'location' => $location,
            'msg' => '审核开票成功',
        ];

        return $this->jsonSuccess($content);
    }

    /**
     * @Route("/{id:[0-9]+}/voucher", name="admin.invoice.voucher")
     */
    public function voucherAction($id)
    {
        $invoiceService = new InvoiceService();

        $invoiceService->saveVoucher($id);

        $location = $this->request->getHTTPReferer();

        $content = [
            'location' => $location,
            'msg' => '保存开票成功',
        ];

        return $this->jsonSuccess($content);
    }

}
