<?php
/**
 * @copyright Copyright (c) 2022 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

require_once 'SettingTrait.php';

use Phinx\Migration\AbstractMigration;

final class V20220608014899 extends AbstractMigration
{

    use SettingTrait;

    public function up()
    {
        $this->handleSecuritySettings();
        $this->handleOAuthLocalSettings();
        $this->handleWeChatMiniProgramSettings();
    }

    protected function handleSecuritySettings()
    {
        $rows = [
            [
                'section' => 'security.throttle',
                'item_key' => 'enabled',
                'item_value' => '1',
            ],
            [
                'section' => 'security.throttle',
                'item_key' => 'interval',
                'item_value' => '300',
            ],
            [
                'section' => 'security.throttle',
                'item_key' => 'rate_limit',
                'item_value' => '300',
            ],
            [
                'section' => 'security.blacklist',
                'item_key' => 'enabled',
                'item_value' => '0',
            ],
            [
                'section' => 'security.blacklist',
                'item_key' => 'content',
                'item_value' => '',
            ],
        ];

        $this->insertSettings($rows);
    }

    protected function handleOAuthLocalSettings()
    {
        $rows = [
            [
                'section' => 'oauth.local',
                'item_key' => 'mutex_login',
                'item_value' => '1',
            ],
            [
                'section' => 'oauth.local',
                'item_key' => 'mutex_client_limit',
                'item_value' => '3',
            ],
        ];

        $this->insertSettings($rows);
    }

    protected function handleWeChatMiniProgramSettings()
    {
        $rows =
            [
                [
                    'section' => 'wechat.mp',
                    'item_key' => 'app_id',
                    'item_value' => '',
                ],
                [
                    'section' => 'wechat.mp',
                    'item_key' => 'app_secret',
                    'item_value' => '',
                ],
            ];

        $this->insertSettings($rows);
    }

}