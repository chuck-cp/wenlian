<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Home\Controllers;

use App\Services\Logic\Exam\Question\AnswerCheck as ExamAnswerCheckService;
use App\Services\Logic\Exam\Question\QuestionFavorite as ExamQuestionFavoriteService;
use App\Services\Logic\Exam\Question\QuestionInfo as ExamQuestionInfoService;
use App\Services\Logic\Exam\Question\QuestionMistake as ExamQuestionMistakeService;
use App\Services\Logic\Report\ReportCreate as ReportCreateService;
use Phalcon\Mvc\View;

/**
 * @RoutePrefix("/exam/question")
 */
class ExamQuestionController extends Controller
{

    /**
     * @Get("/{id:[0-9]+}/info", name="home.exam_question.info")
     */
    public function infoAction($id)
    {
        $type = $this->request->getQuery('type', 'string', 'unit');
        $sn = $this->request->getQuery('sn', 'int', 1);

        $service = new ExamQuestionInfoService();

        $question = $service->handle($id);

        if ($type == 'unit') {
            $this->view->pick('exam_question/unit_info');
        } elseif ($type == 'mistake') {
            $this->view->pick('exam_question/mistake_info');
        } elseif ($type == 'favorite') {
            $this->view->pick('exam_question/favorite_info');
        }

        $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
        $this->view->setVar('question', $question);
        $this->view->setVar('sn', $sn);
    }

    /**
     * @Post("/{id:[0-9]+}/answer/check", name="home.exam_question.check_answer")
     */
    public function checkAnswerAction($id)
    {
        $service = new ExamAnswerCheckService();

        $userScore = $service->handle($id);

        return $this->jsonSuccess(['user_score' => $userScore]);
    }

    /**
     * @Post("/{id:[0-9]+}/favorite", name="home.exam_question.favorite")
     */
    public function favoriteAction($id)
    {
        $service = new ExamQuestionFavoriteService();

        $data = $service->handle($id);

        $msg = $data['action'] == 'do' ? '收藏成功' : '取消收藏成功';

        return $this->jsonSuccess(['data' => $data, 'msg' => $msg]);
    }

    /**
     * @Post("/{id:[0-9]+}/mistake", name="home.exam_question.mistake")
     */
    public function mistakeAction($id)
    {
        $service = new ExamQuestionMistakeService();

        $data = $service->handle($id);

        $msg = $data['action'] == 'do' ? '添加错题成功' : '移除错题成功';

        return $this->jsonSuccess(['data' => $data, 'msg' => $msg]);
    }

    /**
     * @Route("/{id:[0-9]+}/report", name="home.exam_question.report")
     */
    public function reportAction($id)
    {
        if ($this->request->isPost()) {

            $service = new ReportCreateService();

            $service->handle();

            return $this->jsonSuccess(['msg' => '发布挑错成功，等待管理审核']);

        } else {

            $service = new ExamQuestionInfoService();

            $question = $service->handle($id);

            $this->view->setVar('question', $question);
        }
    }

}
