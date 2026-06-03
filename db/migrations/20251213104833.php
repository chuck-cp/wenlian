<?php
/**
 * @copyright Copyright (c) 2025 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

use Phinx\Migration\AbstractMigration;

final class V20251213104833 extends AbstractMigration
{

    public function up()
    {
        $this->alterUserTable();
    }

    protected function alterUserTable()
    {
        $table = $this->table('kg_user');

        if (!$table->hasColumn('profile')) {
            $table->addColumn('profile', 'text', [
                'null' => false,
                'limit' => 65535,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'comment' => '个人资料',
                'after' => 'about',
            ]);
        }

        $table->save();
    }

}
