<?php
/**
 * @copyright Copyright (c) 2023 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Notice\External;

trait RobotTrait
{

    public function weworkNoticeEnabled()
    {
        $robot = kg_setting('wework.robot');

        return $robot['enabled'] == 1;
    }

    public function dingtalkNoticeEnabled()
    {
        $robot = kg_setting('dingtalk.robot');

        return $robot['enabled'] == 1;
    }

}