<?php
/**
 * @copyright Copyright (c) 2023 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

require_once 'SettingTrait.php';

use Phinx\Db\Adapter\MysqlAdapter;
use Phinx\Migration\AbstractMigration;

final class V20230320075731 extends AbstractMigration
{

    use SettingTrait;

    public function up()
    {
        $this->createExamQuestionMistakeTable();
        $this->alterExamPaperTable();
        $this->handleMobileSettings();
    }

    protected function createExamQuestionMistakeTable()
    {
        $tableName = 'kg_exam_question_mistake';

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
            'row_format' => 'COMPACT',
        ])
            ->addColumn('id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'identity' => 'enable',
                'comment' => '主键编号',
            ])
            ->addColumn('question_id', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '问题编号',
                'after' => 'id',
            ])
            ->addColumn('user_id', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '用户编号',
                'after' => 'question_id',
            ])
            ->addColumn('deleted', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '删除标识',
                'after' => 'user_id',
            ])
            ->addColumn('create_time', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '创建时间',
                'after' => 'deleted',
            ])
            ->addColumn('update_time', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '更新时间',
                'after' => 'create_time',
            ])
            ->addIndex(['question_id'], [
                'name' => 'question_id',
                'unique' => false,
            ])
            ->addIndex(['user_id'], [
                'name' => 'user_id',
                'unique' => false,
            ])
            ->create();
    }

    protected function alterExamPaperTable()
    {
        $table = $this->table('kg_exam_paper');

        if (!$table->hasColumn('summary')) {
            $table->addColumn('summary', 'string', [
                'null' => false,
                'default' => '',
                'limit' => 255,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'comment' => '简介',
                'after' => 'cover',
            ]);
        }

        if (!$table->hasColumn('tags')) {
            $table->addColumn('tags', 'string', [
                'null' => false,
                'default' => '[]',
                'limit' => 255,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'comment' => '标签',
                'after' => 'summary',
            ]);
        }

        if (!$table->hasColumn('keywords')) {
            $table->addColumn('keywords', 'string', [
                'null' => false,
                'default' => '',
                'limit' => 100,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'comment' => '关键字',
                'after' => 'tags',
            ]);
        }

        if (!$table->hasColumn('details')) {
            $table->addColumn('details', 'text', [
                'null' => false,
                'limit' => 65535,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'comment' => '详情',
                'after' => 'keywords',
            ]);
        }

        if (!$table->hasColumn('exam_type')) {
            $table->addColumn('exam_type', 'integer', [
                'null' => false,
                'default' => '1',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '测评类型',
                'after' => 'pack_type',
            ]);
        }

        $table->save();
    }

    protected function handleMobileSettings()
    {
        $indexModule = [
            'top' => [
                'search',
                'slide',
            ],
            'nav' => [
                'exam',
                'article',
                'question',
                'point_gift',
                'flash_sale',
                'distribution',
                'groupon',
                'coupon',
            ],
            'content' => [
                'live_preview',
                'featured_course',
                'new_course',
                'free_course',
                'top_teacher',
                'new_article',
                'new_question',
            ],
        ];

        $rows = [
            [
                'section' => 'mobile',
                'item_key' => 'status',
                'item_value' => 'normal',
            ],
            [
                'section' => 'mobile',
                'item_key' => 'private',
                'item_value' => '0',
            ],
            [
                'section' => 'mobile',
                'item_key' => 'closed_tips',
                'item_value' => '系统维护中，请稍后再访问！',
            ],
            [
                'section' => 'mobile',
                'item_key' => 'index_module',
                'item_value' => json_encode($indexModule),
            ],
        ];

        $this->insertSettings($rows);
    }

}
