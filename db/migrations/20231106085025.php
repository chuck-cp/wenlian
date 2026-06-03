<?php
/**
 * @copyright Copyright (c) 2023 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

use Phinx\Db\Adapter\MysqlAdapter;
use Phinx\Migration\AbstractMigration;

final class V20231106085025 extends AbstractMigration
{

    public function up()
    {
        $this->alterUserTable();
        $this->alterCourseTable();
        $this->alterQuestionTable();
        $this->alterConsultTable();
        $this->alterResourceTable();
        $this->alterCourseUserTable();
        $this->alterChapterUserTable();
        $this->dropCourseCategoryTable();
        $this->handleArticles();
    }

    protected function alterUserTable()
    {
        $table = $this->table('kg_user');

        if ($table->hasColumn('course_count')) {
            $table->renameColumn('course_count', 'study_course_count');
        }

        if ($table->hasColumn('exam_count')) {
            $table->renameColumn('exam_count', 'study_paper_count');
        }

        if ($table->hasColumn('article_count')) {
            $table->renameColumn('article_count', 'study_article_count');
        }

        $table->save();
    }

    protected function alterQuestionTable()
    {
        $table = $this->table('kg_question');

        if (!$table->hasColumn('featured')) {
            $table->addColumn('featured', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '推荐标识',
                'after' => 'published',
            ]);
        }

        $table->save();
    }

    protected function alterCourseTable()
    {
        $table = $this->table('kg_course');

        if ($table->hasColumn('exam_count')) {
            $table->renameColumn('exam_count', 'paper_count');
        }

        $table->save();
    }

    protected function alterConsultTable()
    {
        $table = $this->table('kg_consult');

        if ($table->hasColumn('chapter_id')) {
            $table->removeColumn('chapter_id');
        }

        $table->save();
    }

    protected function alterResourceTable()
    {
        $table = $this->table('kg_resource');

        if ($table->hasColumn('chapter_id')) {
            $table->removeColumn('chapter_id');
        }

        $table->save();
    }

    protected function alterCourseUserTable()
    {
        $table = $this->table('kg_course_user');

        if ($table->hasColumn('role_type')) {
            $this->deleteCourseTeachers();
        }

        if ($table->hasColumn('role_type')) {
            $table->removeColumn('role_type');
        }

        if ($table->hasIndexByName('course_user')) {
            $table->removeIndexByName('course_user');
        }

        $table->save();
    }

    protected function alterChapterUserTable()
    {
        $table = $this->table('kg_chapter_user');

        if (!$table->hasColumn('deleted')) {
            $table->addColumn('deleted', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'signed' => false,
                'comment' => '删除标识',
                'after' => 'consumed',
            ]);
        }

        $table->save();
    }

    protected function dropCourseCategoryTable()
    {
        $table = $this->table('kg_course_category');

        if ($table->exists()) {
            $table->drop()->save();
        }
    }

    protected function deleteCourseTeachers()
    {
        $this->getQueryBuilder()
            ->delete('kg_course_user')
            ->where(['role_type' => 2])
            ->execute();
    }

    protected function handleArticles()
    {
        /**
         * 处理封面为空的记录
         */
        $this->getQueryBuilder()
            ->update('kg_article')
            ->set('cover', '/img/default/article_cover.png')
            ->where(['cover' => ''])
            ->execute();

        $articles = $this->getQueryBuilder()
            ->select('*')
            ->from('kg_article')
            ->where(['cover LIKE' => 'http%'])
            ->execute()->fetchAll(PDO::FETCH_ASSOC);

        if (count($articles) == 0) return;

        /**
         * 去除封面URL中的域名
         */
        foreach ($articles as $article) {
            $matched = preg_match('/\/img\/content\/(.*?)$/', $article['cover'], $matches);
            if ($matched) {
                $cover = sprintf('/img/content/%s', $matches[1]);
                $this->getQueryBuilder()
                    ->update('kg_article')
                    ->where(['id' => $article['id']])
                    ->set('cover', $cover)
                    ->execute();
            }
        }
    }

}
