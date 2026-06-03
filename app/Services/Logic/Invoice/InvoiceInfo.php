<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Invoice;

use App\Models\Invoice as InvoiceModel;
use App\Repos\InvoiceAccount as InvoiceAccountRepo;
use App\Repos\UserContact as UserContactRepo;
use App\Services\Logic\Service as LogicService;
use App\Services\Logic\User\ShallowUserInfo;
use App\Validators\Invoice as InvoiceValidator;

class InvoiceInfo extends LogicService
{

    public function handle($id)
    {
        $validator = new InvoiceValidator();

        $invoice = $validator->checkInvoice($id);

        return $this->handleInvoice($invoice);
    }

    protected function handleInvoice(InvoiceModel $invoice)
    {
        $contact = new \stdClass();

        if ($invoice->contact_id > 0) {
            $contact = $this->handleContactInfo($invoice->contact_id);
        }

        $invoiceAccount = $this->handleInvoiceAccountInfo($invoice->account_id);

        $user = $this->handleUserInfo($invoice->user_id);

        return [
            'id' => $invoice->id,
            'media_type' => $invoice->media_type,
            'amount' => $invoice->amount,
            'voucher' => $invoice->voucher,
            'sort_no' => $invoice->sort_no,
            'serial_no' => $invoice->serial_no,
            'post_email' => $invoice->post_email,
            'apply_note' => $invoice->apply_note,
            'review_note' => $invoice->review_note,
            'status' => $invoice->status,
            'deleted' => $invoice->deleted,
            'create_time' => $invoice->create_time,
            'update_time' => $invoice->update_time,
            'invoice_account' => $invoiceAccount,
            'contact' => $contact,
            'user' => $user,
        ];
    }

    protected function handleUserInfo($userId)
    {
        $service = new ShallowUserInfo();

        return $service->handle($userId);
    }

    protected function handleInvoiceAccountInfo($id)
    {
        $accountRepo = new InvoiceAccountRepo();

        $account = $accountRepo->findById($id);

        return [
            'id' => $account->id,
            'usage_type' => $account->usage_type,
            'head_type' => $account->head_type,
            'head_name' => $account->head_name,
            'tax_account' => $account->tax_account,
            'bank_name' => $account->bank_name,
            'bank_account' => $account->bank_account,
            'company_address' => $account->company_address,
            'company_phone' => $account->company_phone,
        ];
    }

    protected function handleContactInfo($id)
    {
        $contactRepo = new UserContactRepo();

        $contact = $contactRepo->findById($id);

        return [
            'id' => $contact->id,
            'name' => $contact->name,
            'phone' => $contact->phone,
            'add_province' => $contact->add_province,
            'add_city' => $contact->add_city,
            'add_county' => $contact->add_county,
            'add_other' => $contact->add_other,
        ];
    }

}
