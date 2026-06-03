<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Notice\External\Sms;

use App\Repos\Account as AccountRepo;
use App\Services\Smser;

class DistSuccess extends Smser
{

    protected $templateCode = 'dist_success';

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
         * 分销成功，订单名称：{1}，订单金额：{2}元，奖励金额：{3}元
         */
        $params = [
            $params['order']['subject'],
            $params['order']['amount'],
            $params['reward']['amount'],
        ];

        return $this->send($account->phone, $templateId, $params);
    }

}
