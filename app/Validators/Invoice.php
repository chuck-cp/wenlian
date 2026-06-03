<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Validators;

use App\Exceptions\BadRequest as BadRequestException;
use App\Library\Validators\Common as CommonValidator;
use App\Models\Invoice as InvoiceModel;
use App\Repos\Account as AccountRepo;
use App\Repos\Invoice as InvoiceRepo;
use App\Repos\User as UserRepo;

class Invoice extends Validator
{

    public function checkInvoice($id)
    {
        $invoiceRepo = new InvoiceRepo();

        $invoice = $invoiceRepo->findById($id);

        if (!$invoice) {
            throw new BadRequestException('invoice.not_found');
        }

        return $invoice;
    }

    public function checkInvoiceAccount($id)
    {
        $validator = new InvoiceAccount();

        return $validator->checkInvoiceAccount($id);
    }

    public function checkFullNo($no)
    {
        $value = $this->filter->sanitize($no, ['trim', 'int']);

        if (strlen($value) != 20) {
            throw new BadRequestException('invoice.invalid_full_no');
        }

        return $value;
    }

    public function checkSortNo($no)
    {
        $value = $this->filter->sanitize($no, ['trim', 'int']);

        if (strlen($value) != 12) {
            throw new BadRequestException('invoice.invalid_sort_no');
        }

        return $value;
    }

    public function checkSerialNo($no)
    {
        $value = $this->filter->sanitize($no, ['trim', 'int']);

        if (strlen($value) != 8) {
            throw new BadRequestException('invoice.invalid_serial_no');
        }

        return $value;
    }

    public function checkVoucher($voucher)
    {
        $value = $this->filter->sanitize($voucher, ['trim', 'string']);

        $ext = pathinfo($value, PATHINFO_EXTENSION);

        if (!in_array($ext, ['pdf', 'ofd'])) {
            throw new BadRequestException('invoice.invalid_voucher');
        }

        return $value;
    }

    public function checkAmount($userId, $amount)
    {
        $amount = $this->filter->sanitize($amount, ['trim', 'float']);

        $userRepo = new UserRepo();

        $balance = $userRepo->findUserBalance($userId);

        $settings = $this->getSettings('invoice');

        if ($amount < 0) {
            throw new BadRequestException('invoice.invalid_amount');
        }

        if ($amount > $balance->invoice) {
            throw new BadRequestException('invoice.balance_not_enough');
        }

        if ($amount < $settings['min_amount'] || $amount > $settings['max_amount']) {
            throw new BadRequestException('invoice.invalid_amount_range');
        }

        return $amount;
    }

    public function checkPostEmail($email)
    {
        $value = $this->filter->sanitize($email, ['trim', 'email']);

        if (!CommonValidator::email($email)) {
            throw new BadRequestException('invoice.invalid_post_email');
        }

        return $value;
    }

    public function checkLoginPassword($userId, $password)
    {
        $accountRepo = new AccountRepo();

        $account = $accountRepo->findById($userId);

        $validator = new Account();

        $validator->checkLoginPassword($account, $password);
    }

    public function checkReviewStatus($status)
    {
        $list = [
            InvoiceModel::STATUS_APPROVED,
            InvoiceModel::STATUS_REFUSED,
        ];

        if (!in_array($status, $list)) {
            throw new BadRequestException('invoice.invalid_status');
        }

        return $status;
    }

    public function checkReviewNote($note)
    {
        $value = $this->filter->sanitize($note, ['trim', 'string']);

        $length = kg_strlen($value);

        if ($length < 2) {
            throw new BadRequestException('invoice.review_note_too_short');
        }

        if ($length > 255) {
            throw new BadRequestException('invoice.review_note_too_long');
        }

        return $value;
    }

    public function checkIfAllowApply($userId)
    {
        $invoiceRepo = new InvoiceRepo();

        $settings = $this->getSettings('invoice');

        $monthlyCount = $invoiceRepo->countUserMonthlyInvoices($userId);

        if ($monthlyCount >= $settings['monthly_limit']) {
            throw new BadRequestException('invoice.reach_monthly_limit');
        }
    }

    public function checkIfAllowCancel(InvoiceModel $invoice)
    {
        $scopes = [
            InvoiceModel::STATUS_PENDING,
        ];

        if (!in_array($invoice->status, $scopes)) {
            throw new BadRequestException('invoice.cancel_not_allowed');
        }
    }

    public function checkIfAllowReview(InvoiceModel $invoice)
    {
        if ($invoice->status != InvoiceModel::STATUS_PENDING) {
            throw new BadRequestException('invoice.review_not_allowed');
        }
    }

}
