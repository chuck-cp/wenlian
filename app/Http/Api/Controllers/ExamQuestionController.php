<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Api\Controllers;

use App\Services\Logic\Exam\Question\AnswerCheck as ExamAnswerCheckService;
use App\Services\Logic\Exam\Question\QuestionFavorite as ExamQuestionFavoriteService;
use App\Services\Logic\Exam\Question\QuestionInfo as ExamQuestionInfoService;
use App\Services\Logic\Exam\Question\QuestionMistake as ExamQuestionMistakeService;

/**
 * @RoutePrefix("/api/exam/question")
 */
class ExamQuestionController extends Controller
{

    /**
     * @Get("/{id:[0-9]+}/info", name="api.exam_question.info")
     */
    public function infoAction($id)
    {
        $service = new ExamQuestionInfoService();

        $question = $service->handle($id);

        return $this->jsonSuccess(['question' => $question]);
    }

    /**
     * @Post("/{id:[0-9]+}/favorite", name="api.exam_question.favorite")
     */
    public function favoriteAction($id)
    {
        $service = new ExamQuestionFavoriteService();

        $data = $service->handle($id);

        $msg = $data['action'] == 'do' ? '收藏成功' : '取消收藏成功';

        return $this->jsonSuccess(['data' => $data, 'msg' => $msg]);
    }

    /**
     * @Post("/{id:[0-9]+}/mistake", name="api.exam_question.mistake")
     */
    public function mistakeAction($id)
    {
        $service = new ExamQuestionMistakeService();

        $data = $service->handle($id);

        $msg = $data['action'] == 'do' ? '添加错题成功' : '移除错题成功';

        return $this->jsonSuccess(['data' => $data, 'msg' => $msg]);
    }

    /**
     * @Post("/{id:[0-9]+}/answer/check", name="api.exam_question.check_answer")
     */
    public function checkAnswerAction($id)
    {
        $service = new ExamAnswerCheckService();

        $userScore = $service->handle($id);

        return $this->jsonSuccess(['user_score' => $userScore]);
    }

}
