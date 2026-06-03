<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Notice\External\Sms;

use App\Repos\Account as AccountRepo;
use App\Services\Smser;

class WithdrawFinish extends Smser
{

    protected $templateCode = 'withdraw_finish';

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
         * 提现成功，提现平台：{1}，提现金额：{2}元，到帐金额：{3}元，服务费用：{4}元
         */
        $params = [
            $params['withdraw']['channel'],
            $params['withdraw']['apply_amount'],
            $params['withdraw']['trans_amount'],
            $params['withdraw']['service_fee'],
        ];

        return $this->send($account->phone, $templateId, $params);
    }

}
