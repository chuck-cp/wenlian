<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Notice\External\Sms;

use App\Repos\Account as AccountRepo;
use App\Services\Smser;

class InvoiceFinish extends Smser
{

    protected $templateCode = 'invoice_finish';

    /**
     * @param array $params
     * @return bool|null
     */
    public function handle(array $params)
    {
        $accountRepo = new AccountRepo();

        $account = $accountRepo->findById($params['user']['id']);

        if (!$account->phone) return null;

        $templateId = $this->getTemplateId($this->templateCode);

        /**
         * 开票成功，发票抬头：{1}，发票金额：{2}元，发票类型：{3}
         */
        $params = [
            $params['invoice_account']['head_name'],
            $params['invoice']['amount'],
            $params['invoice_account']['usage_type'],
        ];

        return $this->send($account->phone, $templateId, $params);
    }

}
