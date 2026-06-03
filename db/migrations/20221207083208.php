<?php
/**
 * @copyright Copyright (c) 2022 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

use Phinx\Migration\AbstractMigration;

final class V20221207083208 extends AbstractMigration
{

    public function up()
    {
        $this->alterArticleTable();
        $this->alterAnswerTable();
        $this->alterQuestionTable();
    }

    protected function alterArticleTable()
    {
        $table = $this->table('kg_article');

        if (!$table->hasColumn('images')) {
            $table->addColumn('images', 'string', [
                'null' => false,
                'default' => '[]',
                'limit' => 1000,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'comment' => '图集',
                'after' => 'content',
            ]);
        }

        $table->save();
    }

    protected function alterAnswerTable()
    {
        $table = $this->table('kg_answer');

        if (!$table->hasColumn('images')) {
            $table->addColumn('images', 'string', [
                'null' => false,
                'default' => '[]',
                'limit' => 1000,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'comment' => '图集',
                'after' => 'content',
            ]);
        }

        $table->save();
    }

    protected function alterQuestionTable()
    {
        $table = $this->table('kg_question');

        if (!$table->hasColumn('images')) {
            $table->addColumn('images', 'string', [
                'null' => false,
                'default' => '[]',
                'limit' => 1000,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'comment' => '图集',
                'after' => 'content',
            ]);
        }

        $table->save();
    }

}
