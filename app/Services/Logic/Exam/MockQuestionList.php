<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Exam;

use App\Models\ExamPaperUser as ExamPaperUserModel;
use App\Models\ExamQuestion as ExamQuestionModel;
use App\Repos\ExamQuestion as ExamQuestionRepo;
use App\Repos\ExamQuestionFavorite as ExamQuestionFavoriteRepo;
use App\Repos\ExamQuestionUser as ExamQuestionUserRepo;
use App\Services\Logic\ExamQuestionTrait;
use App\Services\Logic\Service as LogicService;
use App\Validators\ExamPaperUser as ExamPaperUserValidator;

class MockQuestionList extends LogicService
{

    use ExamQuestionTrait;

    public function handle($id)
    {
        $validator = new ExamPaperUserValidator();

        $paperUser = $validator->checkById($id);

        $authCode = $this->request->getQuery('auth_code', ['trim', 'string']);

        if (!empty($authCode)) {

            $validator->checkAuthCode($paperUser->id, $authCode);

        } else {

            $user = $this->getLoginUser(true);

            $validator->checkOwner($user->id, $paperUser->user_id);
        }

        return $this->handleTreeList($paperUser);
    }

    protected function handleTreeList(ExamPaperUserModel $paperUser)
    {
        $questionUserRepo = new ExamQuestionUserRepo();

        $relations = $questionUserRepo->findByPaperUserId($paperUser->id);

        if ($relations->count() == 0) return [];

        $questionIds = kg_array_column($relations->toArray(), 'question_id');

        $questionRepo = new ExamQuestionRepo();

        $questions = $questionRepo->findByIds($questionIds);

        $questionMappings = [];

        foreach ($questions as $question) {

            $passRate = $this->getQuestionPassRate($question->join_count, $question->pass_count);

            $questionMappings[$question->id] = [
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
        }

        $favoritedQuestionIds = $this->getFavoritedQuestionIds($paperUser->user_id);

        $relationMappings = [];

        foreach ($relations as $key => $relation) {
            $id = $relation->question_id;
            $question = $questionMappings[$id] ?? new \stdClass();
            $favorited = in_array($id, $favoritedQuestionIds) ? 1 : 0;
            $relationMappings[$id] = [
                'id' => $relation->id,
                'question_parent_id' => $relation->question_parent_id,
                'question_id' => $relation->question_id,
                'question_model' => $relation->question_model,
                'question_score' => $relation->question_score,
                'question_duration' => $relation->question_duration,
                'user_answer_files' => $relation->user_answer_files,
                'user_answer' => $relation->user_answer,
                'user_score' => $relation->user_score,
                'user_duration' => $relation->user_duration,
                'question' => $question,
                'me' => ['favorited' => $favorited],
                'sn' => $key + 1,
            ];
        }

        $items = [];

        foreach ($relations as $relation) {
            $questionId = $relation->question_id;
            $parentQuestionId = $relation->question_parent_id;
            $item = $relationMappings[$questionId];
            if ($parentQuestionId == 0) {
                if ($relation->question_model == ExamQuestionModel::MODEL_COMPLEX_QUESTION) {
                    $item['children'] = [];
                }
                $items[$questionId] = $item;
            }
        }

        foreach ($relations as $relation) {
            $questionId = $relation->question_id;
            $parentQuestionId = $relation->question_parent_id;
            $item = $relationMappings[$questionId];
            if ($parentQuestionId > 0) {
                $items[$parentQuestionId]['children'][] = $item;
            }
        }

        return array_values($items);
    }

    protected function getFavoritedQuestionIds($userId)
    {
        $favoriteRepo = new ExamQuestionFavoriteRepo();

        $relations = $favoriteRepo->findByUserId($userId);

        $result = [];

        if ($relations->count() > 0) {
            $result = kg_array_column($relations->toArray(), 'question_id');
        }

        return $result;
    }

}
