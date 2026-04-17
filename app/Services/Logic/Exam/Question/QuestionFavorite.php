<?php
/**
 * @copyright Copyright (c) 2022 深圳市文联软件有限公司
 * @license https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Exam\Question;

use App\Models\ExamQuestion as ExamQuestionModel;
use App\Models\ExamQuestionFavorite as ExamQuestionFavoriteModel;
use App\Repos\ExamQuestionFavorite as ExamQuestionFavoriteRepo;
use App\Services\Logic\ExamQuestionTrait;
use App\Services\Logic\Service as LogicService;
use App\Validators\UserLimit as UserLimitValidator;

class QuestionFavorite extends LogicService
{

    use ExamQuestionTrait;

    public function handle($id)
    {
        $question = $this->checkExamQuestion($id);

        $user = $this->getLoginUser();

        $validator = new UserLimitValidator();

        $validator->checkFavoriteLimit($user);

        $favoriteRepo = new ExamQuestionFavoriteRepo();

        $favorite = $favoriteRepo->findExamQuestionFavorite($question->id, $user->id);

        if (!$favorite) {

            $favorite = new ExamQuestionFavoriteModel();

            $favorite->question_id = $question->id;
            $favorite->user_id = $user->id;

            $favorite->create();

        } else {

            $favorite->deleted = $favorite->deleted == 1 ? 0 : 1;

            $favorite->update();
        }

        if ($favorite->deleted == 0) {

            $action = 'do';

            $this->incrExamQuestionFavoriteCount($question);

        } else {

            $action = 'undo';

            $this->decrExamQuestionFavoriteCount($question);
        }

        return [
            'action' => $action,
            'count' => $question->favorite_count,
        ];
    }

    protected function incrExamQuestionFavoriteCount(ExamQuestionModel $question)
    {
        $question->favorite_count += 1;

        $question->update();
    }

    protected function decrExamQuestionFavoriteCount(ExamQuestionModel $question)
    {
        if ($question->favorite_count > 0) {
            $question->favorite_count -= 1;
            $question->update();
        }
    }

}
