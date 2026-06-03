<?php
/**
 * @copyright Copyright (c) 2022 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

require_once 'SettingTrait.php';

use Phinx\Db\Adapter\MysqlAdapter;
use Phinx\Migration\AbstractMigration;

class V20220420102015 extends AbstractMigration
{

    use SettingTrait;

    public function up()
    {
        $this->createLiveBlockTable();
        $this->alterChapterLiveTable();
        $this->handleChapters();
        $this->handleChapterLives();
        $this->handleLiveSettings();
    }

    protected function createLiveBlockTable()
    {
        $tableName = 'kg_live_block';

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
            ->addColumn('user_id', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '用户编号',
                'after' => 'course_id',
            ])
            ->addColumn('expire_time', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '过期时间',
                'after' => 'user_id',
            ])
            ->addColumn('create_time', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '创建时间',
                'after' => 'expire_time',
            ])
            ->addColumn('update_time', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '更新时间',
                'after' => 'create_time',
            ])
            ->addIndex(['course_id'], [
                'name' => 'course_id',
                'unique' => false,
            ])
            ->addIndex(['user_id'], [
                'name' => 'user_id',
                'unique' => false,
            ])
            ->create();
    }

    protected function alterChapterLiveTable()
    {
        $table = $this->table('kg_chapter_live');

        if (!$table->hasColumn('settings')) {
            $table->addColumn('settings', 'string', [
                'null' => false,
                'default' => '[]',
                'limit' => 1000,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'comment' => '直播设置',
                'after' => 'status',
            ]);
        }

        if ($table->hasColumn('user_limit')) {
            $table->removeColumn('user_limit');
        }

        $table->save();
    }

    protected function handleChapters()
    {
        $rows = $this->getQueryBuilder()
            ->select(['id', 'attrs'])
            ->from('kg_chapter')
            ->where(['model' => 2, 'parent_id >' => 0])
            ->execute()->fetchAll(PDO::FETCH_ASSOC);

        if (empty($rows)) return;

        foreach ($rows as $row) {
            $attrs = json_decode($row['attrs'], true);
            $attrs['playback'] = ['ready' => 0, 'duration' => 0];
            $this->getQueryBuilder()
                ->update('kg_chapter')
                ->set('attrs', json_encode($attrs))
                ->where(['id' => $row['id']])
                ->execute();
        }
    }

    protected function handleChapterLives()
    {
        $settings = [
            'record_enabled' => 0,
            'chat_enabled' => 0,
            'post_interval' => 5,
        ];
        $this->getQueryBuilder()
            ->update('kg_chapter_live')
            ->set('settings', json_encode($settings))
            ->execute();
    }

    protected function handleLiveSettings()
    {
        $rows =
            [
                [
                    'section' => 'live.push',
                    'item_key' => 'record_enabled',
                    'item_value' => '0',
                ],
                [
                    'section' => 'live.push',
                    'item_key' => 'record_tpl_id',
                    'item_value' => '',
                ],
            ];

        $this->insertSettings($rows);
    }

}
