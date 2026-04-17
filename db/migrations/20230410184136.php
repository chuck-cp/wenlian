<?php
/**
 * @copyright Copyright (c) 2023 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

require_once 'SettingTrait.php';

use Phinx\Db\Adapter\MysqlAdapter;
use Phinx\Migration\AbstractMigration;

final class V20230410184136 extends AbstractMigration
{

    use SettingTrait;

    public function up()
    {
        $this->createDanmuTable();
        $this->alterArticleTable();
        $this->alterCourseTable();
        $this->alterChapterVodTable();
        $this->alterChapterReadTable();
        $this->handleChapterVod();
        $this->handleChapterLive();
        $this->handleChapterRead();
        $this->handleSiteSettings();
        $this->handleAuditSettings();
    }

    protected function createDanmuTable()
    {
        /**
         * 删除原来未使用的danmu表
         */
        if ($this->table('kg_danmu')->exists()) {
            $this->table('kg_danmu')->drop()->save();
        }

        $this->table('kg_danmu', [
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
            ->addColumn('chapter_id', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '章节编号',
                'after' => 'course_id',
            ])
            ->addColumn('owner_id', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '用户编号',
                'after' => 'chapter_id',
            ])
            ->addColumn('time', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '时间轴',
                'after' => 'owner_id',
            ])
            ->addColumn('text', 'string', [
                'null' => false,
                'default' => '',
                'limit' => 255,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'comment' => '内容',
                'after' => 'time',
            ])
            ->addColumn('color', 'string', [
                'null' => false,
                'default' => '#ffffff',
                'limit' => 30,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'comment' => '颜色',
                'after' => 'text',
            ])
            ->addColumn('size', 'integer', [
                'null' => false,
                'default' => '12',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '大小',
                'after' => 'color',
            ])
            ->addColumn('type', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '类型',
                'after' => 'size',
            ])
            ->addColumn('published', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '发布标识',
                'after' => 'type',
            ])
            ->addColumn('deleted', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '删除标识',
                'after' => 'published',
            ])
            ->addColumn('client_type', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '终端类型',
                'after' => 'deleted',
            ])
            ->addColumn('client_ip', 'string', [
                'null' => false,
                'default' => '',
                'limit' => 64,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'comment' => '终端IP',
                'after' => 'client_type',
            ])
            ->addColumn('create_time', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '创建时间',
                'after' => 'client_ip',
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
            ->addIndex(['owner_id'], [
                'name' => 'owner_id',
                'unique' => false,
            ])
            ->create();
    }

    protected function alterCourseTable()
    {
        $table = $this->table('kg_course');

        if ($table->hasColumn('origin_price')) {
            $table->removeColumn('origin_price');
        }

        $table->save();
    }

    protected function alterArticleTable()
    {
        $table = $this->table('kg_article');

        if ($table->hasColumn('private')) {
            $table->removeColumn('private');
        }

        if (!$table->hasColumn('publish_time')) {
            $table->addColumn('publish_time', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '发布时间',
                'after' => 'update_time',
            ]);
        }

        $table->save();
    }

    protected function alterChapterVodTable()
    {
        $table = $this->table('kg_chapter_vod');

        if (!$table->hasColumn('settings')) {
            $table->addColumn('settings', 'string', [
                'null' => false,
                'default' => '[]',
                'limit' => 1000,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'comment' => '点播设置',
                'after' => 'file_remote',
            ]);
        }

        $table->save();
    }

    protected function alterChapterReadTable()
    {
        $table = $this->table('kg_chapter_read');

        if (!$table->hasColumn('settings')) {
            $table->addColumn('settings', 'string', [
                'null' => false,
                'default' => '[]',
                'limit' => 1000,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'comment' => '图文设置',
                'after' => 'content',
            ]);
        }

        $table->save();
    }

    protected function handleChapterVod()
    {
        $settings = [
            'comment_enabled' => '1',
            'danmu_enabled' => '1',
            'speed_enabled' => '1',
        ];

        $this->getQueryBuilder()
            ->update('kg_chapter_vod')
            ->set('settings', json_encode($settings))
            ->execute();
    }

    protected function handleChapterLive()
    {
        $rows = $this->getQueryBuilder()
            ->select(['id', 'settings'])
            ->from('kg_chapter_live')
            ->execute()->fetchAll(PDO::FETCH_ASSOC);

        if (empty($rows)) return;

        foreach ($rows as $row) {
            if (empty($row['settings'])) break;
            $settings = json_decode($row['settings'], true);
            $settings['comment_enabled'] = 1;
            $settings['danmu_enabled'] = 1;
            $this->getQueryBuilder()
                ->update('kg_chapter_live')
                ->set('settings', json_encode($settings))
                ->where(['id' => $row['id']])
                ->execute();
        }
    }

    protected function handleChapterRead()
    {
        $settings = [
            'comment_enabled' => '1',
        ];

        $this->getQueryBuilder()
            ->update('kg_chapter_read')
            ->set('settings', json_encode($settings))
            ->execute();
    }

    protected function handleAuditSettings()
    {
        $rows = [
            [
                'section' => 'security.audit',
                'item_key' => 'danmu_enabled',
                'item_value' => '1',
            ],
        ];

        $this->insertSettings($rows);
    }

    protected function handleSiteSettings()
    {
        $rows = [
            [
                'section' => 'site',
                'item_key' => 'copy_enabled',
                'item_value' => '0',
            ],
        ];

        $this->insertSettings($rows);
    }

}
