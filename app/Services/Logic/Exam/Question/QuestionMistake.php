<?php
/**
 * @copyright Copyright (c) 2023 深圳市酷瓜软件有限公司
 * @license https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Exam\Question;

use App\Models\ExamQuestionMistake as ExamQuestionMistakeModel;
use App\Repos\ExamQuestionMistake as ExamQuestionMistakeRepo;
use App\Services\Logic\ExamQuestionTrait;
use App\Services\Logic\Service as LogicService;

class QuestionMistake extends LogicService
{

    use ExamQuestionTrait;

    public function handle($id)
    {
        $question = $this->checkExamQuestion($id);

        $user = $this->getLoginUser();

        $mistakeRepo = new ExamQuestionMistakeRepo();

        $mistake = $mistakeRepo->findExamQuestionMistake($question->id, $user->id);

        if (!$mistake) {

            $mistake = new ExamQuestionMistakeModel();

            $mistake->question_id = $question->id;
            $mistake->user_id = $user->id;

            $mistake->create();

        } else {

            $mistake->deleted = $mistake->deleted == 1 ? 0 : 1;

            $mistake->update();
        }

        $action = $mistake->deleted == 0 ? 'do' : 'undo';

        return ['action' => $action];
    }

}
