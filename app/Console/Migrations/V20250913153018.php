<?php
/**
 * @copyright Copyright (c) 2025 深圳市酷瓜软件有限公司
 * @license https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @link https://www.koogua.com
 */

namespace App\Console\Migrations;

class V20250913153018 extends Migration
{

    public function run()
    {
        $this->handleVodSettings();
    }

    protected function handleVodSettings()
    {
        $settings = [
            [
                'section' => 'vod',
                'item_key' => 'transcode_type',
                'item_value' => 'normal',
            ],
        ];

        $this->saveSettings($settings);
    }

}
