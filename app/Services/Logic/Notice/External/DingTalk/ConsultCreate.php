<?php
/**
 * @copyright Copyright (c) 2023 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Notice\External\DingTalk;

use App\Services\DingTalkNotice;

class ConsultCreate extends DingTalkNotice
{

    public function handle(array $params)
    {
        $content = kg_ph_replace("用户：{user.name}（{user.id}），手机：{account.phone}，对课程：{course.title} 发起了咨询：\n{consult.question}", [
            'account.phone' => $params['account']['phone'] ?: 'N/A',
            'user.name' => $params['user']['name'],
            'user.id' => $params['user']['id'],
            'course.title' => $params['course']['title'],
            'consult.question' => $params['consult']['question'],
        ]);

        $this->atCourseTeacher($params['course']['id'], $content);
    }

}