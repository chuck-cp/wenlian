<?php
/**
 * @copyright Copyright (c) 2022 深圳市酷瓜软件有限公司
 * @license https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Exam\Paper;

use App\Models\ExamPaper as ExamPaperModel;
use App\Models\ExamPaperFavorite as ExamPaperFavoriteModel;
use App\Models\User as UserModel;
use App\Repos\ExamPaperFavorite as ExamPaperFavoriteRepo;
use App\Services\Logic\ExamPaperTrait;
use App\Services\Logic\Service as LogicService;
use App\Validators\UserLimit as UserLimitValidator;

class PaperFavorite extends LogicService
{

    use ExamPaperTrait;

    public function handle($id)
    {
        $paper = $this->checkExamPaper($id);

        $user = $this->getLoginUser();

        $validator = new UserLimitValidator();

        $validator->checkFavoriteLimit($user);

        $favoriteRepo = new ExamPaperFavoriteRepo();

        $favorite = $favoriteRepo->findExamPaperFavorite($paper->id, $user->id);

        if (!$favorite) {

            $favorite = new ExamPaperFavoriteModel();

            $favorite->paper_id = $paper->id;
            $favorite->user_id = $user->id;

            $favorite->create();

        } else {

            $favorite->deleted = $favorite->deleted == 1 ? 0 : 1;

            $favorite->update();
        }

        if ($favorite->deleted == 0) {

            $action = 'do';

            $this->incrExamPaperFavoriteCount($paper);
            $this->incrUserFavoriteCount($user);

        } else {

            $action = 'undo';

            $this->decrExamPaperFavoriteCount($paper);
            $this->decrUserFavoriteCount($user);
        }

        return [
            'action' => $action,
            'count' => $paper->favorite_count,
        ];
    }

    protected function incrExamPaperFavoriteCount(ExamPaperModel $paper)
    {
        $paper->favorite_count += 1;

        $paper->update();
    }

    protected function decrExamPaperFavoriteCount(ExamPaperModel $paper)
    {
        if ($paper->favorite_count > 0) {
            $paper->favorite_count -= 1;
            $paper->update();
        }
    }

    protected function incrUserFavoriteCount(UserModel $user)
    {
        $user->favorite_count += 1;

        $user->update();
    }

    protected function decrUserFavoriteCount(UserModel $user)
    {
        if ($user->favorite_count > 0) {
            $user->favorite_count -= 1;
            $user->update();
        }
    }

}
