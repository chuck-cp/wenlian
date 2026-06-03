<?php
/**
 * @copyright Copyright (c) 2023 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Admin\Services;

use App\Models\DigitalCard as DigitalCardModel;
use App\Models\KgSale as KgSaleModel;
use App\Repos\ArticleUser as ArticleUserRepo;
use App\Repos\CourseUser as CourseUserRepo;
use App\Repos\DigitalCard as DigitalCardRepo;
use App\Repos\ExamPaperUser as ExamPaperUserRepo;
use App\Repos\Package as PackageRepo;
use App\Repos\User as UserRepo;
use App\Repos\Vip as VipRepo;

class DigitalCardCancel extends Service
{

    public function handle($id)
    {
        $card = $this->findDigitalCard($id);

        $card->deleted = 1;

        $card->update();

        if ($card->user_id == 0) return;

        switch ($card->item_type) {
            case KgSaleModel::ITEM_COURSE:
                $this->handleCourse($card);
                break;
            case KgSaleModel::ITEM_PACKAGE:
                $this->handlePackage($card);
                break;
            case KgSaleModel::ITEM_VIP:
                $this->handleVip($card);
                break;
            case KgSaleModel::ITEM_EXAM_PAPER:
                $this->handleExamPaper($card);
                break;
            case KgSaleModel::ITEM_ARTICLE:
                $this->handleArticle($card);
                break;
        }
    }

    protected function handleCourse(DigitalCardModel $card)
    {
        $courseUserRepo = new CourseUserRepo();

        $courseUser = $courseUserRepo->findCourseUser($card->item_id, $card->user_id);

        if (!$courseUser) return;

        $courseUser->deleted = 1;

        $courseUser->update();
    }

    protected function handlePackage(DigitalCardModel $card)
    {
        $packageRepo = new PackageRepo();

        $package = $packageRepo->findById($card->item_id);

        $courses = $packageRepo->findCourses($package->id);

        $courseUserRepo = new CourseUserRepo();

        foreach ($courses as $course) {

            $courseUser = $courseUserRepo->findCourseUser($course->id, $card->user_id);

            if (!$courseUser) continue;

            $courseUser->deleted = 1;

            $courseUser->update();
        }
    }

    protected function handleVip(DigitalCardModel $card)
    {
        $userRepo = new UserRepo();

        $user = $userRepo->findById($card->user_id);

        if ($user->vip_expiry_time == 0) return;

        $vipRepo = new VipRepo();

        $vip = $vipRepo->findById($card->item_id);

        $diffTime = "-{$vip->expiry} months";
        $baseTime = $user->vip_expiry_time;

        $user->vip_expiry_time = strtotime($diffTime, $baseTime);

        if ($user->vip_expiry_time < time()) {
            $user->vip = 0;
        }

        $user->update();
    }

    protected function handleExamPaper(DigitalCardModel $card)
    {
        $paperUserRepo = new ExamPaperUserRepo();

        $paperUser = $paperUserRepo->findDebutPaperUser($card->item_id, $card->user_id);

        if (!$paperUser) return;

        $paperUser->deleted = 1;

        $paperUser->update();
    }

    protected function handleArticle(DigitalCardModel $card)
    {
        $articleUserRepo = new ArticleUserRepo();

        $articleUser = $articleUserRepo->findArticleUser($card->item_id, $card->user_id);

        if (!$articleUser) return;

        $articleUser->deleted = 1;

        $articleUser->update();
    }

    protected function findDigitalCard($id)
    {
        $repo = new DigitalCardRepo();

        return $repo->findById($id);
    }

}
