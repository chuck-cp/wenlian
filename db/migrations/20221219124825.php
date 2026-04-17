<?php
/**
 * @copyright Copyright (c) 2022 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

use Phinx\Db\Adapter\MysqlAdapter;
use Phinx\Migration\AbstractMigration;

final class V20221219124825 extends AbstractMigration
{

    public function up()
    {
        $this->alterAnswerTable();
    }

    protected function alterAnswerTable()
    {
        $table = $this->table('kg_answer');

        if (!$table->hasColumn('closed')) {
            $table->addColumn('closed', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '关闭标识',
                'after' => 'accepted',
            ]);
        }

        $table->save();
    }

}
