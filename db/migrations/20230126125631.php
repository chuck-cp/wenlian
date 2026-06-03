<?php
/**
 * @copyright Copyright (c) 2023 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

require_once 'SettingTrait.php';

use Phinx\Migration\AbstractMigration;

final class V20230126125631 extends AbstractMigration
{

    use SettingTrait;

    public function up()
    {
        $this->handleVodSettings();
    }

    protected function handleVodSettings()
    {
        $rows = [
            [
                'section' => 'vod',
                'item_key' => 'encrypt_play_key',
                'item_value' => '',
            ],
        ];

        $this->insertSettings($rows);
    }

}
