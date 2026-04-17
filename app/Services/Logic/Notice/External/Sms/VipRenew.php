<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Notice\External\Sms;

use App\Repos\Account as AccountRepo;
use App\Services\Smser;

class VipRenew extends Smser
{

    protected $templateCode = 'vip_renew';

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

        $vipExpiryTime = date('Y-m-d H:i', $params['user']['vip_expiry_time']);

        /**
         * 您的会员即将到期，到期时间：{1}，请登录系统及时续费。拒收请回复R
         */
        $params = [$vipExpiryTime];

        return $this->send($account->phone, $templateId, $params);
    }

}
