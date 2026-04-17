<?php
/**
 * @copyright Copyright (c) 2022 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

use Phinx\Db\Adapter\MysqlAdapter;

class V20220729034759 extends Phinx\Migration\AbstractMigration
{

    public function up()
    {
        $this->createMigrationTaskTable();
    }

    protected function createMigrationTaskTable()
    {
        $tableName = 'kg_migration_task';

        if ($this->table($tableName)->exists()) {
            return;
        }

        $this->table($tableName, [
            'id' => false,
            'primary_key' => ['id'],
            'engine' => 'InnoDB',
            'encoding' => 'utf8mb4',
            'collation' => 'utf8mb4_general_ci',
            'comment' => '',
            'row_format' => 'DYNAMIC',
        ])
            ->addColumn('id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'identity' => 'enable',
                'comment' => '主键编号',
            ])
            ->addColumn('version', 'string', [
                'null' => false,
                'default' => '',
                'limit' => 100,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'comment' => '版本',
                'after' => 'id',
            ])
            ->addColumn('start_time', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '开始时间',
                'after' => 'version',
            ])
            ->addColumn('end_time', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '结束时间',
                'after' => 'start_time',
            ])
            ->addIndex(['version'], [
                'name' => 'version',
                'unique' => true,
            ])
            ->create();
    }

}
