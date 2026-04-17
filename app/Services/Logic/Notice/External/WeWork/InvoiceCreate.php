<?php
/**
 * @copyright Copyright (c) 2023 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Notice\External\WeWork;

use App\Services\WeWorkNotice;

class InvoiceCreate extends WeWorkNotice
{

    public function handle(array $params)
    {
        $content = kg_ph_replace("用户：{user.name}（{user.id}），手机：{account.phone}，申请开票，开票金额：￥{invoice.amount}", [
            'account.phone' => $params['account']['phone'] ?: 'N/A',
            'user.name' => $params['user']['name'],
            'user.id' => $params['user']['id'],
            'invoice.amount' => $params['invoice']['amount'],
        ]);

        $this->atFinanceService($content);
    }

}