<?php
/**
 * @copyright Copyright (c) 2023 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Console\Tasks;

use App\Models\ExamPaper as ExamPaperModel;
use App\Services\Search\ExamPaperDocument;
use App\Services\Search\ExamPaperSearcher;
use Phalcon\Mvc\Model\Resultset;
use Phalcon\Mvc\Model\ResultsetInterface;

class ExamPaperIndexTask extends Task
{

    /**
     * 搜索测试
     *
     * @command: php console.php exam_paper_index search {query}
     */
    public function searchAction($params)
    {
        $query = $params[0] ?? null;

        if (!$query) {
            exit('please special a query word' . PHP_EOL);
        }

        $handler = new ExamPaperSearcher();

        $result = $handler->search($query);

        var_export($result);
    }

    /**
     * 清空索引
     *
     * @command: php console.php exam_paper_index clean
     */
    public function cleanAction()
    {
        $handler = new ExamPaperSearcher();

        $index = $handler->getXS()->getIndex();

        echo '------ start clean exam paper index ------' . PHP_EOL;

        $index->clean();

        echo '------ end clean exam paper index ------' . PHP_EOL;
    }

    /**
     * 重建索引
     *
     * @command: php console.php exam_paper_index rebuild
     */
    public function rebuildAction()
    {
        $papers = $this->findExamPapers();

        if ($papers->count() == 0) return;

        $handler = new ExamPaperSearcher();

        $doc = new ExamPaperDocument();

        $index = $handler->getXS()->getIndex();

        echo '------ start rebuild exam paper index ------' . PHP_EOL;

        $index->beginRebuild();

        foreach ($papers as $paper) {
            $document = $doc->setDocument($paper);
            $index->add($document);
        }

        $index->endRebuild();

        echo '------ end rebuild exam paper index ------' . PHP_EOL;
    }

    /**
     * 刷新索引缓存
     *
     * @command: php console.php exam_paper_index flush_index
     */
    public function flushIndexAction()
    {
        $handler = new ExamPaperSearcher();

        $index = $handler->getXS()->getIndex();

        echo '------ start flush exam_paper index ------' . PHP_EOL;

        $index->flushIndex();

        echo '------ end flush exam_paper index ------' . PHP_EOL;
    }

    /**
     * 刷新搜索日志
     *
     * @command: php console.php exam_paper_index flush_logging
     */
    public function flushLoggingAction()
    {
        $handler = new ExamPaperSearcher();

        $index = $handler->getXS()->getIndex();

        echo '------ start flush exam_paper logging ------' . PHP_EOL;

        $index->flushLogging();

        echo '------ end flush exam_paper logging ------' . PHP_EOL;
    }

    /**
     * 查找试卷
     *
     * @return ResultsetInterface|Resultset|ExamPaperModel[]
     */
    protected function findExamPapers()
    {
        return ExamPaperModel::query()
            ->where('published = 1')
            ->andWhere('deleted = 0')
            ->execute();
    }

}
