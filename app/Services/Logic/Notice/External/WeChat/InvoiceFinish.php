<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Notice\External\WeChat;

use App\Services\WeChatNotice;

class InvoiceFinish extends WeChatNotice
{

    protected $templateCode = 'invoice_finish';

    /**
     * @param array $params
     * @return bool|null
     */
    public function handle(array $params)
    {
        $subscribe = $this->getConnect($params['user']['id']);

        if (!$subscribe) return null;

        $first = '开票已处理完成！';
        $remark = '感谢您的支持，有疑问请联系客服哦！';

        $params = [
            'first' => $first,
            'remark' => $remark,
            'thing2' => $params['invoice_account']['head_name'],
            'amount3' => sprintf('%s元', $params['invoice']['amount']),
            'thing4' => '课程服务',
        ];

        $templateId = $this->getTemplateId($this->templateCode);

        return $this->send($subscribe->open_id, $templateId, $params);
    }

}
