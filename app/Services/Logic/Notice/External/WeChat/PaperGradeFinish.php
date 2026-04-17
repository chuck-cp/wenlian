<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Notice\External\WeChat;

use App\Services\WeChatNotice;

class PaperGradeFinish extends WeChatNotice
{

    protected $templateCode = 'paper_grade_finish';

    /**
     * @param array $params
     * @return bool|null
     */
    public function handle($params)
    {
        $subscribe = $this->getConnect($params['user']['id']);

        if (!$subscribe) return null;

        $first = '试卷已批阅完成！';
        $remark = '感谢您的支持，有疑问请联系客服哦！';

        $params = [
            'first' => $first,
            'remark' => $remark,
            'thing3' => $params['user']['name'],
            'character_string5' => $params['paper_user']['paper_score'],
            'character_string4' => $params['paper_user']['user_score'],
        ];

        $templateId = $this->getTemplateId($this->templateCode);

        return $this->send($subscribe->open_id, $templateId, $params);
    }

}
