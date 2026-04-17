<?php
/**
 * @copyright Copyright (c) 2024 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

use Phinx\Migration\AbstractMigration;

final class V20240522104833 extends AbstractMigration
{

    public function up()
    {
        $this->alterExamQuestionUserTable();
    }

    protected function alterExamQuestionUserTable()
    {
        $table = $this->table('kg_exam_question_user');

        if (!$table->hasColumn('user_answer_files')) {
            $table->addColumn('user_answer_files', 'string', [
                'null' => false,
                'default' => '[]',
                'limit' => 1000,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'comment' => '用户答案附件',
                'after' => 'user_answer',
            ]);
        }

        $table->save();
    }

}
