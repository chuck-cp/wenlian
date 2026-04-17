<?php
/**
 * @copyright Copyright (c) 2023 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

require_once 'SettingTrait.php';

use Phinx\Db\Adapter\MysqlAdapter;
use Phinx\Migration\AbstractMigration;

final class V20230513190136 extends AbstractMigration
{

    use SettingTrait;

    public function up()
    {
        $this->dropRewardTable();
        $this->createArticleUserTable();
        $this->alterExamPaperTable();
        $this->alterReviewLikeTable();
        $this->alterArticleTable();
        $this->handleLiveSettings();
        $this->handleArticles();
        $this->handleUsers();
    }

    protected function dropRewardTable()
    {
        $table = $this->table('kg_reward');

        if ($table->exists()) {
            $table->drop()->save();
        }
    }

    protected function createArticleUserTable()
    {
        $tableName = 'kg_article_user';

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
            ->addColumn('article_id', 'integer', [
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
                'after' => 'article_id',
            ])
            ->addColumn('source_type', 'integer', [
                'null' => false,
                'default' => '1',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '来源类型',
                'after' => 'user_id',
            ])
            ->addColumn('deleted', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '删除标识',
                'after' => 'user_id',
            ])
            ->addColumn('expiry_time', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '过期时间',
                'after' => 'deleted',
            ])
            ->addColumn('create_time', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '创建时间',
                'after' => 'expiry_time',
            ])
            ->addColumn('update_time', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '更新时间',
                'after' => 'create_time',
            ])
            ->addIndex(['article_id'], [
                'name' => 'article_id',
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

        if (!$table->hasColumn('fake_join_count')) {
            $table->addColumn('fake_join_count', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '虚拟参与数',
                'after' => 'join_count',
            ]);
        }

        $table->save();
    }

    protected function alterReviewLikeTable()
    {
        $table = $this->table('kg_review_like');

        if (!$table->hasColumn('update_time')) {
            $table->addColumn('update_time', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '更新时间',
                'after' => 'create_time',
            ]);
        }

        $table->save();
    }

    protected function alterArticleTable()
    {
        $table = $this->table('kg_article');

        if ($table->hasColumn('publish_time')) {
            $table->removeColumn('publish_time');
        }

        if ($table->hasColumn('settings')) {
            $table->removeColumn('settings');
        }

        if (!$table->hasColumn('market_price')) {
            $table->addColumn('market_price', 'decimal', [
                'null' => false,
                'default' => '0.00',
                'precision' => '10',
                'scale' => '2',
                'signed' => false,
                'comment' => '市场价格',
                'after' => 'category_id',
            ]);
        }

        if (!$table->hasColumn('vip_price')) {
            $table->addColumn('vip_price', 'decimal', [
                'null' => false,
                'default' => '0.00',
                'precision' => '10',
                'scale' => '2',
                'signed' => false,
                'comment' => '会员价格',
                'after' => 'market_price',
            ]);
        }

        if (!$table->hasColumn('study_expiry')) {
            $table->addColumn('study_expiry', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '学习期限',
                'after' => 'vip_price',
            ]);
        }

        if (!$table->hasColumn('user_count')) {
            $table->addColumn('user_count', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '真实用户数',
                'after' => 'deleted',
            ]);
        }

        if (!$table->hasColumn('fake_user_count')) {
            $table->addColumn('fake_user_count', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '虚拟用户数',
                'after' => 'user_count',
            ]);
        }

        $table->save();
    }

    protected function handleArticles()
    {
        $this->getQueryBuilder()
            ->update('kg_article')
            ->set('published', 0)
            ->whereInList('published', [1, 3])
            ->execute();

        $this->getQueryBuilder()
            ->update('kg_article')
            ->set('published', 1)
            ->where(['published' => 2])
            ->execute();

        $this->getQueryBuilder()
            ->update('kg_article')
            ->set('study_expiry', 360)
            ->execute();

        $this->getQueryBuilder()
            ->update('kg_article')
            ->set('cover', '/img/default/article_cover.png')
            ->where(['cover' => ''])
            ->execute();
    }

    protected function handleUsers()
    {
        $this->getQueryBuilder()
            ->update('kg_user')
            ->set('article_count', 0)
            ->execute();
    }

    protected function handleLiveSettings()
    {
        $rows = [
            [
                'section' => 'live.pull',
                'item_key' => 'webrtc_enabled',
                'item_value' => '1',
            ],
        ];

        $this->insertSettings($rows);
    }

}
