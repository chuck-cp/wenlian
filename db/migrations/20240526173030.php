<?php
/**
 * @copyright Copyright (c) 2024 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

use Phinx\Db\Adapter\MysqlAdapter;
use Phinx\Migration\AbstractMigration;

final class V20240526173030 extends AbstractMigration
{

    public function up()
    {
        $this->createChapterDocTable();
    }

    protected function createChapterDocTable()
    {
        $tableName = 'kg_chapter_doc';

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
            ->addColumn('course_id', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '课程编号',
                'after' => 'id',
            ])
            ->addColumn('chapter_id', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '章节编号',
                'after' => 'course_id',
            ])
            ->addColumn('upload_id', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '上传编号',
                'after' => 'chapter_id',
            ])
            ->addColumn('settings', 'string', [
                'null' => false,
                'default' => '[]',
                'limit' => 1000,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'comment' => '文档设置',
                'after' => 'upload_id',
            ])
            ->addColumn('create_time', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '创建时间',
                'after' => 'settings',
            ])
            ->addColumn('update_time', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '更新时间',
                'after' => 'create_time',
            ])
            ->addIndex(['chapter_id'], [
                'name' => 'chapter_id',
                'unique' => false,
            ])
            ->addIndex(['course_id'], [
                'name' => 'course_id',
                'unique' => false,
            ])
            ->create();
    }

}
