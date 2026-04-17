<?php
/**
 * @copyright Copyright (c) 2022 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

require_once 'SettingTrait.php';

use Phinx\Migration\AbstractMigration;

final class V20220815102519 extends AbstractMigration
{

    use SettingTrait;

    public function up()
    {
        $this->handleSecuritySettings();
    }

    protected function handleSecuritySettings()
    {
        $rows = [
            [
                'section' => 'security.audit',
                'item_key' => 'review_enabled',
                'item_value' => '1',
            ],
            [
                'section' => 'security.audit',
                'item_key' => 'consult_enabled',
                'item_value' => '1',
            ],
            [
                'section' => 'security.audit',
                'item_key' => 'article_enabled',
                'item_value' => '1',
            ],
            [
                'section' => 'security.audit',
                'item_key' => 'question_enabled',
                'item_value' => '1',
            ],
            [
                'section' => 'security.audit',
                'item_key' => 'answer_enabled',
                'item_value' => '1',
            ],
            [
                'section' => 'security.audit',
                'item_key' => 'comment_enabled',
                'item_value' => '1',
            ],
        ];

        $this->insertSettings($rows);
    }

}