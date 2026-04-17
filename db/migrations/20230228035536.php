<?php
/**
 * @copyright Copyright (c) 2023 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

use Phinx\Db\Adapter\MysqlAdapter;
use Phinx\Migration\AbstractMigration;

final class V20230228035536 extends AbstractMigration
{

    public function up()
    {
        $this->alterConsultTable();
    }

    protected function alterConsultTable()
    {
        $table = $this->table('kg_consult');

        if (!$table->hasColumn('sticky')) {
            $table->addColumn('sticky', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '置顶标识',
                'after' => 'private',
            ]);
        }

        $table->save();
    }

}
