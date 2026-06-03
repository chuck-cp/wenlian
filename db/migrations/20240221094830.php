<?php
/**
 * @copyright Copyright (c) 2024 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

use Phinx\Migration\AbstractMigration;

final class V20240221094830 extends AbstractMigration
{

    public function up()
    {
        $this->alterArticleTable();
        $this->alterChapterReadTable();
        $this->handleChapters();
    }

    protected function alterArticleTable()
    {
        $table = $this->table('kg_article');

        if (!$table->hasColumn('markdown')) {
            $table->addColumn('markdown', 'text', [
                'null' => false,
                'limit' => 65535,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'comment' => 'markdown',
                'after' => 'content',
            ]);
        }

        if (!$table->hasColumn('format')) {
            $table->addColumn('format', 'string', [
                'null' => false,
                'default' => 'html',
                'limit' => 30,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'comment' => '格式类型',
                'after' => 'markdown',
            ]);
        }

        $table->save();
    }

    protected function alterChapterReadTable()
    {
        $table = $this->table('kg_chapter_read');

        if (!$table->hasColumn('markdown')) {
            $table->addColumn('markdown', 'text', [
                'null' => false,
                'limit' => 65535,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'comment' => 'markdown',
                'after' => 'content',
            ]);
        }

        if (!$table->hasColumn('format')) {
            $table->addColumn('format', 'string', [
                'null' => false,
                'default' => 'html',
                'limit' => 30,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'comment' => '格式类型',
                'after' => 'markdown',
            ]);
        }

        $table->save();
    }

    protected function handleChapters()
    {
        $chapters = $this->getQueryBuilder()
            ->select(['id', 'attrs'])
            ->from('kg_chapter')
            ->where(['model' => 3, 'parent_id >' => 0])
            ->execute()->fetchAll(PDO::FETCH_ASSOC);

        if (count($chapters) == 0) return;

        foreach ($chapters as $chapter) {
            $attrs = json_decode($chapter['attrs'], true);
            $attrs['format'] = 'html';
            $attrs = json_encode($attrs);
            $this->getQueryBuilder()
                ->update('kg_chapter')
                ->where(['id' => $chapter['id']])
                ->set('attrs', $attrs)
                ->execute();
        }
    }

}
