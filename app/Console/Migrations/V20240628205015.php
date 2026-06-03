<?php
/**
 * @copyright Copyright (c) 2024 深圳市酷瓜软件有限公司
 * @license https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @link https://www.koogua.com
 */

namespace App\Console\Migrations;

use App\Repos\Setting as SettingRepo;

class V20240628205015 extends Migration
{

    public function run()
    {
        $this->handleSmsNoticeSettings();
        $this->handleWechatNoticeSettings();
    }

    protected function handleSmsNoticeSettings()
    {
        $settingRepo = new SettingRepo();

        $item = $settingRepo->findItem('sms', 'template');

        $template = json_decode($item->item_value, true);

        $template['vip_renew'] = ['enabled' => 0, 'id' => 0];
        $template['paper_grade_finish'] = ['enabled' => 0, 'id' => 0];

        $item->item_value = json_encode($template);

        $item->update();
    }

    protected function handleWechatNoticeSettings()
    {
        $settingRepo = new SettingRepo();

        $item = $settingRepo->findItem('wechat.oa', 'notice_template');

        $template = json_decode($item->item_value, true);

        $template['vip_renew'] = ['enabled' => 0, 'id' => 0];
        $template['paper_grade_finish'] = ['enabled' => 0, 'id' => 0];

        $item->item_value = json_encode($template);

        $item->update();
    }

}