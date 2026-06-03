<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Admin\Controllers;

use App\Http\Admin\Services\ExamPaperQuestion as ExamPaperQuestionService;

/**
 * @RoutePrefix("/admin/exam/paper/question")
 */
class ExamPaperQuestionController extends Controller
{

    /**
     * @Post("/create", name="admin.exam_paper_question.create")
     */
    public function createAction()
    {
        $service = new ExamPaperQuestionService();

        $service->createPaperQuestion();

        return $this->jsonSuccess(['msg' => '添加试题成功']);
    }

    /**
     * @Post("/delete", name="admin.exam_paper_question.delete")
     */
    public function deleteAction()
    {
        $service = new ExamPaperQuestionService();

        $service->deletePaperQuestion();

        return $this->jsonSuccess(['msg' => '删除试题成功']);
    }

    /**
     * @Post("/random", name="admin.exam_paper_question.random")
     */
    public function randomAction()
    {
        $service = new ExamPaperQuestionService();

        $data = $service->packByRandom();

        return $this->jsonSuccess(['data' => $data]);
    }

}
