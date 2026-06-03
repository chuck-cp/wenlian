<?php
/**
 * @copyright Copyright (c) 2022 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

require_once 'SettingTrait.php';

use Phinx\Migration\AbstractMigration;

final class V20221031102952 extends AbstractMigration
{

    use SettingTrait;

    public function up()
    {
        $this->alterInvoiceTable();
        $this->handleSiteSettings();
    }

    protected function alterInvoiceTable()
    {
        $table = $this->table('kg_invoice');

        if (!$table->hasColumn('post_email')) {
            $table->addColumn('post_email', 'string', [
                'null' => false,
                'default' => '',
                'limit' => 30,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'comment' => '投递邮箱',
                'after' => 'serial_no',
            ]);
        }

        $table->save();
    }

    protected function handleSiteSettings()
    {
        $rows = [
            [
                'section' => 'site',
                'item_key' => 'private',
                'item_value' => '0',
            ],
        ];

        $this->insertSettings($rows);
    }

}
