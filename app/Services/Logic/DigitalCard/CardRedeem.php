<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\DigitalCard;

use App\Models\Article as ArticleModel;
use App\Models\Course as CourseModel;
use App\Models\ExamPaper as ExamPaperModel;
use App\Models\KgProduct as KgProductModel;
use App\Models\Package as PackageModel;
use App\Models\User as UserModel;
use App\Models\Vip as VipModel;
use App\Services\Logic\Deliver\ArticleDeliver as ArticleDeliverService;
use App\Services\Logic\Deliver\CourseDeliver as CourseDeliverService;
use App\Services\Logic\Deliver\ExamPaperDeliver as ExamPaperDeliverService;
use App\Services\Logic\Deliver\PackageDeliver as PackageDeliverService;
use App\Services\Logic\Deliver\VipDeliver as VipDeliverService;
use App\Services\Logic\Service as LogicService;
use App\Validators\DigitalCard as DigitalCardValidator;

class CardRedeem extends LogicService
{

    public function handle()
    {
        $code = $this->request->getPost('code', ['trim', 'string']);

        $cardValidator = new DigitalCardValidator();

        $card = $cardValidator->checkByCode($code);

        $cardValidator->checkIfActiveCard($card);

        $user = $this->getLoginUser();

        if ($card->item_type == KgProductModel::ITEM_COURSE) {

            $course = $cardValidator->checkCourse($card->item_id);

            $this->handleCourseRedeem($course, $user);

        } elseif ($card->item_type == KgProductModel::ITEM_PACKAGE) {

            $package = $cardValidator->checkPackage($card->item_id);

            $this->handlePackageRedeem($package, $user);

        } elseif ($card->item_type == KgProductModel::ITEM_VIP) {

            $vip = $cardValidator->checkVip($card->item_id);

            $this->handleVipRedeem($vip, $user);

        } elseif ($card->item_type == KgProductModel::ITEM_EXAM_PAPER) {

            $paper = $cardValidator->checkExamPaper($card->item_id);

            $this->handleExamPaperRedeem($paper, $user);

        } elseif ($card->item_type == KgProductModel::ITEM_ARTICLE) {

            $article = $cardValidator->checkArticle($card->item_id);

            $this->handleArticleRedeem($article, $user);
        }

        $card->user_id = $user->id;
        $card->user_name = $user->name;
        $card->redeem_time = time();

        $card->update();

        return $card;
    }

    protected function handleCourseRedeem(CourseModel $course, UserModel $user)
    {
        $service = new CourseDeliverService();

        $service->handle($course, $user);
    }

    protected function handlePackageRedeem(PackageModel $package, UserModel $user)
    {
        $service = new PackageDeliverService();

        $service->handle($package, $user);
    }

    protected function handleVipRedeem(VipModel $vip, UserModel $user)
    {
        $service = new VipDeliverService();

        $service->handle($vip, $user);
    }

    protected function handleExamPaperRedeem(ExamPaperModel $paper, UserModel $user)
    {
        $service = new ExamPaperDeliverService();

        $service->handle($paper, $user);
    }

    protected function handleArticleRedeem(ArticleModel $article, UserModel $user)
    {
        $service = new ArticleDeliverService();

        $service->handle($article, $user);
    }

}
