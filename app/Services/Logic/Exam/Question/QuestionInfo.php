<?php
/**
 * @copyright Copyright (c) 2023 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Exam\Question;

use App\Models\ExamQuestion as ExamQuestionModel;
use App\Models\User as UserModel;
use App\Repos\ExamQuestion as ExamQuestionRepo;
use App\Repos\ExamQuestionFavorite as ExamQuestionFavoriteRepo;
use App\Repos\ExamQuestionMistake as ExamQuestionMistakeRepo;
use App\Services\Logic\ExamQuestionTrait;
use App\Services\Logic\Service as LogicService;

class QuestionInfo extends LogicService
{

    use ExamQuestionTrait;

    public function handle($id)
    {
        $question = $this->checkExamQuestion($id);

        $user = $this->getCurrentUser();

        return $this->handleQuestion($question, $user);
    }

    protected function handleQuestion(ExamQuestionModel $question, UserModel $user)
    {
        $me = $this->handleMeInfo($question, $user);

        $passRate = $this->getQuestionPassRate($question->join_count, $question->pass_count);

        $result = [
            'id' => $question->id,
            'parent_id' => $question->parent_id,
            'topic' => $question->topic,
            'answer' => $question->answer,
            'model' => $question->model,
            'level' => $question->level,
            'score' => $question->score,
            'duration' => $question->duration,
            'solution' => $question->solution,
            'attrs' => $question->attrs,
            'join_count' => $question->join_count,
            'pass_count' => $question->pass_count,
            'pass_rate' => $passRate,
        ];

        if ($question->model == ExamQuestionModel::MODEL_COMPLEX_QUESTION) {
            $result['children'] = $this->getChildQuestions($question->id);
        }

        $result['me'] = $me;

        return $result;
    }

    protected function getChildQuestions($id)
    {
        $repo = new ExamQuestionRepo();

        $questions = $repo->findChildQuestions($id);

        $result = [];

        if ($questions->count() == 0) {
            return $result;
        }

        foreach ($questions as $question) {

            $passRate = $this->getQuestionPassRate($question->join_count, $question->pass_count);

            $result[] = [
                'id' => $question->id,
                'parent_id' => $question->parent_id,
                'topic' => $question->topic,
                'answer' => $question->answer,
                'model' => $question->model,
                'level' => $question->level,
                'score' => $question->score,
                'duration' => $question->duration,
                'solution' => $question->solution,
                'attrs' => $question->attrs,
                'join_count' => $question->join_count,
                'pass_count' => $question->pass_count,
                'pass_rate' => $passRate,
                'me' => [
                    'logged' => 0,
                    'favorited' => 0,
                    'missed' => 0,
                ],
            ];
        }

        return $result;
    }

    protected function handleMeInfo(ExamQuestionModel $question, UserModel $user)
    {
        $me = [
            'logged' => 0,
            'favorited' => 0,
            'missed' => 0,
        ];

        if ($user->id > 0) {

            $me['logged'] = 1;

            $favoriteRepo = new ExamQuestionFavoriteRepo();

            $favorite = $favoriteRepo->findExamQuestionFavorite($question->id, $user->id);

            if ($favorite && $favorite->deleted == 0) {
                $me['favorited'] = 1;
            }

            $mistakeRepo = new ExamQuestionMistakeRepo();

            $mistake = $mistakeRepo->findExamQuestionMistake($question->id, $user->id);

            if ($mistake && $mistake->deleted == 0) {
                $me['missed'] = 1;
            }
        }

        return $me;
    }

}
