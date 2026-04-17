<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Builders;

use App\Models\Invoice as InvoiceModel;
use App\Repos\InvoiceAccount as InvoiceAccountRepo;
use App\Repos\UserContact as UserContactRepo;

class InvoiceList extends Builder
{

    public function handleAccounts(array $invoices)
    {
        $accounts = $this->getAccounts($invoices);

        foreach ($invoices as $key => $invoice) {
            $invoices[$key]['account'] = $accounts[$invoice['account_id']] ?? null;
        }

        return $invoices;
    }

    public function handleContacts(array $invoices)
    {
        $accounts = $this->getContacts($invoices);

        foreach ($invoices as $key => $invoice) {
            $invoices[$key]['contact'] = $accounts[$invoice['contact_id']] ?? null;
        }

        return $invoices;
    }

    public function handleUsers(array $invoices)
    {
        $users = $this->getUsers($invoices);

        foreach ($invoices as $key => $invoice) {
            $invoices[$key]['user'] = $users[$invoice['user_id']] ?? null;
        }

        return $invoices;
    }

    public function handleMeInfo(array $invoice)
    {
        $me = [
            'allow_cancel' => 0,
        ];

        $scopes = [
            InvoiceModel::STATUS_PENDING,
        ];

        if (in_array($invoice['status'], $scopes)) {
            $me['allow_cancel'] = 1;
        }

        return $me;
    }

    public function getAccounts(array $invoices)
    {
        $ids = kg_array_column($invoices, 'account_id');

        $accountRepo = new InvoiceAccountRepo();

        $columns = [
            'id', 'usage_type', 'head_type', 'head_name', 'tax_account',
            'bank_name', 'bank_account', 'company_address', 'company_phone',
        ];

        $accounts = $accountRepo->findByIds($ids, $columns);

        $result = [];

        foreach ($accounts->toArray() as $account) {
            $result[$account['id']] = $account;
        }

        return $result;
    }

    public function getContacts(array $invoices)
    {
        $ids = kg_array_column($invoices, 'contact_id');

        $contactRepo = new UserContactRepo();

        $columns = [
            'id', 'name', 'phone',
            'add_province', 'add_city', 'add_county', 'add_other',
        ];

        $contacts = $contactRepo->findByIds($ids, $columns);

        $result = [];

        foreach ($contacts->toArray() as $contact) {
            $result[$contact['id']] = $contact;
        }

        return $result;
    }

    public function getUsers(array $invoices)
    {
        $ids = kg_array_column($invoices, 'user_id');

        return $this->getShallowUserByIds($ids);
    }

}
