<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Invoice;

use App\Models\Invoice as InvoiceModel;
use App\Repos\User as UserRepo;
use App\Services\Logic\Service as LogicService;
use App\Validators\Invoice as InvoiceValidator;

class InvoiceCancel extends LogicService
{

    public function handle($id)
    {
        $user = $this->getLoginUser();

        $validator = new InvoiceValidator();

        $invoice = $validator->checkInvoice($id);

        $validator->checkIfAllowCancel($invoice);

        $validator->checkOwner($user->id, $invoice->user_id);

        try {

            $this->db->begin();

            $userRepo = new UserRepo();

            $balance = $userRepo->findUserBalance($user->id);

            $balance->invoice += $invoice->amount;
            $balance->update();

            $invoice->status = InvoiceModel::STATUS_CANCELED;
            $invoice->update();

            $this->db->commit();

            return $invoice;

        } catch (\Exception $e) {

            $this->db->rollback();

            $logger = $this->getLogger('invoice');

            $logger->error('Cancel Invoice Exception: ' . kg_json_encode([
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'message' => $e->getMessage(),
                    'invoice' => $invoice->toArray(),
                ]));

            throw new \RuntimeException('sys.rollback');
        }
    }

}
