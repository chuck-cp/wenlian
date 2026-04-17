<?php
/**
 * @copyright Copyright (c) 2024 深圳市文联软件有限公司
 * @license https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @link https://www.koogua.com
 */

namespace App\Console\Migrations;

use App\Models\ChapterVod as ChapterVodModel;
use Phalcon\Mvc\Model\Resultset;

class V20240728205033 extends Migration
{

    public function run()
    {
        $this->handleChapterVodSettings();
        $this->handleLocalOauthSettings();
        $this->handleVodSettings();
    }

    protected function handleChapterVodSettings()
    {
        /**
         * @var $rows Resultset|ChapterVodModel[]
         */
        $rows = ChapterVodModel::query()->execute();

        if ($rows->count() == 0) return;

        foreach ($rows as $row) {
            $settings = $row->settings;
            if (!isset($settings['verify_enabled'])) {
                $settings['verify_enabled'] = 0;
            }
            if (isset($settings['copy_enabled'])) {
                unset($settings['copy_enabled']);
            }
            $row->settings = $settings;
            $row->update();
        }
    }

    protected function handleLocalOauthSettings()
    {
        $setting = [
            'section' => 'oauth.local',
            'item_key' => 'failed_login_limit',
            'item_value' => '3',
        ];

        $this->saveSetting($setting);

        $setting = [
            'section' => 'oauth.local',
            'item_key' => 'failed_login_lock',
            'item_value' => '600',
        ];

        $this->saveSetting($setting);
    }

    protected function handleVodSettings()
    {
        $setting = [
            'section' => 'vod',
            'item_key' => 'danmu_enabled',
            'item_value' => '1',
        ];

        $this->saveSetting($setting);

        $setting = [
            'section' => 'vod',
            'item_key' => 'fast_forward_enabled',
            'item_value' => '1',
        ];

        $this->saveSetting($setting);

        $setting = [
            'section' => 'vod',
            'item_key' => 'human_verify_enabled',
            'item_value' => '0',
        ];

        $this->saveSetting($setting);
    }

}