<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Invoice;

use App\Services\Logic\Service as LogicService;
use App\Validators\InvoiceAccount as InvoiceAccountValidator;

class AccountDelete extends LogicService
{

    public function handle($id)
    {
        $user = $this->getLoginUser();

        $validator = new InvoiceAccountValidator();

        $account = $validator->checkInvoiceAccount($id);

        $validator->checkOwner($user->id, $account->user_id);

        $account->deleted = 1;

        $account->update();

        return $account;
    }

}
