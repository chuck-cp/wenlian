<?php
/**
 * @copyright Copyright (c) 2023 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Notice\External\DingTalk;

use App\Services\DingTalkNotice;

class PointGiftRedeem extends DingTalkNotice
{

    public function handle(array $params)
    {
        $content = kg_ph_replace("用户：{user.name}（{user.id}），手机：{account.phone}，兑换了商品： {gift.name}，不要忘记发货哦！", [
            'account.phone' => $params['account']['phone'] ?: 'N/A',
            'user.name' => $params['user']['name'],
            'user.id' => $params['user']['id'],
            'gift.name' => $params['gift']['name'],
        ]);

        $this->atCustomService($content);
    }

}