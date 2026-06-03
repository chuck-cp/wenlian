<?php
/**
 * @copyright Copyright (c) 2025 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

require_once 'SettingTrait.php';

use Phinx\Migration\AbstractMigration;

final class V20251201193030 extends AbstractMigration
{

    use SettingTrait;

    public function up()
    {
        $this->handleVodSettings();
        $this->handleExamSettings();
    }

    protected function handleVodSettings()
    {
        $rows = [
            [
                'section' => 'vod',
                'item_key' => 'switch_anti_enabled',
                'item_value' => 0,
            ],
        ];

        $this->insertSettings($rows);
    }

    protected function handleExamSettings()
    {
        $rows = [
            [
                'section' => 'exam',
                'item_key' => 'switch_anti_enabled',
                'item_value' => 0,
            ],
        ];

        $this->insertSettings($rows);
    }

}
