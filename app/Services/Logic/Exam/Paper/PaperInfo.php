<?php
/**
 * @copyright Copyright (c) 2022 深圳市文联软件有限公司
 * @license https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Exam\Paper;

use App\Models\ExamPaper as ExamPaperModel;
use App\Models\ExamPaperUser as ExamPaperUserModel;
use App\Models\User as UserModel;
use App\Repos\ExamPaperFavorite as ExamPaperFavoriteRepo;
use App\Services\Logic\ExamPaperTrait;
use App\Services\Logic\Service as LogicService;

class PaperInfo extends LogicService
{

    use ExamPaperTrait;
    use PaperUserTrait;

    public function handle($id)
    {
        $paper = $this->checkExamPaper($id);

        $user = $this->getCurrentUser();

        $this->setPaperUser($paper, $user);

        return $this->handleExamPaper($paper, $user);
    }

    protected function handleExamPaper(ExamPaperModel $paper, UserModel $user)
    {
        $service = new BasicInfo();

        $result = $service->handleBasicInfo($paper);

        $result['me'] = $this->handleMeInfo($paper, $user);

        return $result;
    }

    protected function handleMeInfo(ExamPaperModel $paper, UserModel $user)
    {
        $me = [
            'allow_order' => 0,
            'logged' => 0,
            'favorited' => 0,
            'owned' => 0,
        ];

        if ($user->id > 0) {

            $me['logged'] = 1;

            $me['owned'] = $this->ownedPaper ? 1 : 0;

            if (!$this->ownedPaper && $paper->market_price > 0) {
                $me['allow_order'] = 1;
            }

            /**
             * 存在待阅试卷，不能再次考试
             */
            if ($this->debutPaperUser && $this->debutPaperUser->status == ExamPaperUserModel::STATUS_WAITING) {
                $me['owned'] = 0;
            }

            $favoriteRepo = new ExamPaperFavoriteRepo();

            $favorite = $favoriteRepo->findExamPaperFavorite($paper->id, $user->id);

            if ($favorite && $favorite->deleted == 0) {
                $me['favorited'] = 1;
            }
        }

        return $me;
    }

}
