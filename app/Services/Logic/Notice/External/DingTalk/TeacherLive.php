<?php
/**
 * @copyright Copyright (c) 2023 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Notice\External\DingTalk;

use App\Services\DingTalkNotice;

class TeacherLive extends DingTalkNotice
{

    public function handle(array $params)
    {
        $content = kg_ph_replace("课程：{course.title} 计划于 {live.start_time} 开播，不要错过直播时间哦！", [
            'course.title' => $params['course']['title'],
            'live.start_time' => date('Y-m-d H:i', $params['live']['start_time']),
        ]);

        $this->atCourseTeacher($params['course']['id'], $content);
    }

}