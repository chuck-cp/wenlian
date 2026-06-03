<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Notice\External\WeChat;

use App\Services\WeChatNotice;

class WithdrawFinish extends WeChatNotice
{

    protected $templateCode = 'withdraw_finish';

    /**
     * @param array $params
     * @return bool|null
     */
    public function handle(array $params)
    {
        $subscribe = $this->getConnect($params['user']['id']);

        if (!$subscribe) return null;

        $first = '提现已处理完成！';
        $remark = '感谢您的支持，有疑问请联系客服哦！';

        $params = [
            'first' => $first,
            'remark' => $remark,
            'time7' => date('Y-m-d H:i', $params['withdraw']['create_time']),
            'amount1' => sprintf('%s元', $params['withdraw']['apply_amount']),
            'amount10' => sprintf('%s元', $params['withdraw']['service_fee']),
            'amount11' => sprintf('%s元', $params['withdraw']['trans_amount']),
        ];

        $templateId = $this->getTemplateId($this->templateCode);

        return $this->send($subscribe->open_id, $templateId, $params);
    }

}
