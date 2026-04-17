<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

use Phinx\Migration\AbstractMigration;

final class V20220315034506 extends AbstractMigration
{

    public function up()
    {
        $this->alterArticleTable();
        $this->alterChapterVodTable();
        $this->alterHelpTable();
        $this->alterPageTable();
        $this->alterQuestionTable();
        $this->alterTopicTable();
    }

    protected function alterArticleTable()
    {
        $table = $this->table('kg_article');

        if (!$table->hasColumn('keywords')) {
            $table->addColumn('keywords', 'string', [
                'null' => false,
                'default' => '',
                'limit' => 100,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'comment' => '关键字',
                'after' => 'summary',
            ]);
        }

        $table->save();
    }

    protected function alterChapterVodTable()
    {
        $table = $this->table('kg_chapter_vod');

        if ($table->hasColumn('file_transcode')) {
            $table->changeColumn('file_transcode', 'string', [
                'null' => false,
                'default' => '[]',
                'limit' => 1000,
            ]);
        }

        if ($table->hasColumn('file_remote')) {
            $table->changeColumn('file_remote', 'string', [
                'null' => false,
                'default' => '[]',
                'limit' => 500,
            ]);
        }

        $table->save();
    }

    protected function alterQuestionTable()
    {
        $table = $this->table('kg_question');

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

        $table->save();
    }

    protected function alterHelpTable()
    {
        $table = $this->table('kg_help');

        if (!$table->hasColumn('keywords')) {
            $table->addColumn('keywords', 'string', [
                'null' => false,
                'default' => '',
                'limit' => 100,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'comment' => '关键字',
                'after' => 'title',
            ]);
        }

        $table->save();
    }

    protected function alterPageTable()
    {
        $table = $this->table('kg_page');

        if (!$table->hasColumn('keywords')) {
            $table->addColumn('keywords', 'string', [
                'null' => false,
                'default' => '',
                'limit' => 100,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'comment' => '关键字',
                'after' => 'alias',
            ]);
        }

        $table->save();
    }

    protected function alterTopicTable()
    {
        $table = $this->table('kg_topic');

        if (!$table->hasColumn('cover')) {
            $table->addColumn('cover', 'string', [
                'null' => false,
                'default' => '',
                'limit' => 100,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'comment' => '封面',
                'after' => 'title',
            ]);
        }

        $table->save();
    }

}
