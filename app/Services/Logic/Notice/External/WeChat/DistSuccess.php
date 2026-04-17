<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Notice\External\WeChat;

use App\Services\WeChatNotice;

class DistSuccess extends WeChatNotice
{

    protected $templateCode = 'dist_success';

    /**
     * @param array $params
     * @return bool|null
     */
    public function handle(array $params)
    {
        $subscribe = $this->getConnect($params['user']['id']);

        if (!$subscribe) return null;

        $first = '恭喜您，您的分享又带来了新订单！';
        $remark = '感谢您的支持，有疑问请联系客服哦！';

        $params = [
            'first' => $first,
            'remark' => $remark,
            'keyword1' => $params['order']['subject'],
            'keyword2' => sprintf('%s元', $params['order']['amount']),
            'keyword3' => sprintf('%s元', $params['reward']['amount']),
        ];

        $templateId = $this->getTemplateId($this->templateCode);

        return $this->send($subscribe->open_id, $templateId, $params);
    }

}
