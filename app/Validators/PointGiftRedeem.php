<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Validators;

use App\Exceptions\BadRequest as BadRequestException;
use App\Models\Article as ArticleModel;
use App\Models\Course as CourseModel;
use App\Models\ExamPaper as ExamPaperModel;
use App\Models\KgSale as KgSaleModel;
use App\Models\PointGift as PointGiftModel;
use App\Models\User as UserModel;
use App\Models\Vip as VipModel;
use App\Repos\ArticleUser as ArticleUserRepo;
use App\Repos\CourseUser as CourseUserRepo;
use App\Repos\ExamPaperUser as ExamPaperUserRepo;
use App\Repos\PointGiftRedeem as PointGiftRedeemRepo;
use App\Repos\User as UserRepo;
use App\Repos\UserContact as UserContactRepo;

class PointGiftRedeem extends Validator
{

    public function checkRedeem($id)
    {
        $redeemRepo = new PointGiftRedeemRepo();

        $redeem = $redeemRepo->findById($id);

        if (!$redeem) {
            throw new BadRequestException('point_gift_redeem.not_found');
        }

        return $redeem;
    }

    public function checkUserContact($contactId)
    {
        $validator = new UserContact();

        return $validator->checkUserContact($contactId);
    }

    public function checkIfAllowRedeem(PointGiftModel $gift, UserModel $user)
    {
        $this->checkStock($gift);

        $this->checkRedeemLimit($gift, $user);

        $this->checkPointBalance($gift, $user);

        if ($gift->type == KgSaleModel::ITEM_COURSE) {

            $validator = new Course();

            $course = $validator->checkCourse($gift->attrs['id']);

            $this->checkIfAllowRedeemCourse($course, $user);

        } elseif ($gift->type == KgSaleModel::ITEM_VIP) {

            $validator = new Vip();

            $vip = $validator->checkVip($gift->attrs['id']);

            $this->checkIfAllowRedeemVip($vip, $user);

        } elseif ($gift->type == KgSaleModel::ITEM_EXAM_PAPER) {

            $validator = new ExamPaper();

            $paper = $validator->checkExamPaper($gift->attrs['id']);

            $this->checkIfAllowRedeemExamPaper($paper, $user);

        } elseif ($gift->type == KgSaleModel::ITEM_ARTICLE) {

            $validator = new Article();

            $article = $validator->checkArticle($gift->attrs['id']);

            $this->checkIfAllowRedeemArticle($article, $user);

        } elseif ($gift->type == KgSaleModel::ITEM_GOODS) {

            $this->checkIfAllowRedeemGoods($user);
        }
    }

    protected function checkIfAllowRedeemCourse(CourseModel $course, UserModel $user)
    {
        if ($course->published == 0) {
            throw new BadRequestException('point_gift_redeem.course_not_published');
        }

        if ($course->market_price == 0) {
            throw new BadRequestException('point_gift_redeem.course_free');
        }

        $courseUserRepo = new CourseUserRepo();

        $courseUser = $courseUserRepo->findCourseUser($course->id, $user->id);

        if ($courseUser && $courseUser->expiry_time > time()) {
            throw new BadRequestException('point_gift_redeem.course_owned');
        }
    }

    protected function checkIfAllowRedeemVip(VipModel $vip, UserModel $user)
    {
        if ($vip->published == 0) {
            throw new BadRequestException('point_gift_redeem.vip_not_published');
        }

        if ($vip->price == 0) {
            throw new BadRequestException('point_gift_redeem.vip_free');
        }
    }

    protected function checkIfAllowRedeemExamPaper(ExamPaperModel $paper, UserModel $user)
    {
        if ($paper->published == 0) {
            throw new BadRequestException('point_gift_redeem.exam_paper_not_published');
        }

        if ($paper->market_price == 0) {
            throw new BadRequestException('point_gift_redeem.exam_paper_free');
        }

        $paperUserRepo = new ExamPaperUserRepo();

        $paperUser = $paperUserRepo->findDebutPaperUser($paper->id, $user->id);

        if ($paperUser && $paperUser->expiry_time > time()) {
            throw new BadRequestException('point_gift_redeem.exam_paper_owned');
        }
    }

    protected function checkIfAllowRedeemArticle(ArticleModel $article, UserModel $user)
    {
        if ($article->published == 0) {
            throw new BadRequestException('point_gift_redeem.article_not_published');
        }

        if ($article->market_price == 0) {
            throw new BadRequestException('point_gift_redeem.article_free');
        }

        $articleUserRepo = new ArticleUserRepo();

        $articleUser = $articleUserRepo->findArticleUser($article->id, $user->id);

        if ($articleUser && $articleUser->expiry_time > time()) {
            throw new BadRequestException('point_gift_redeem.article_owned');
        }
    }

    protected function checkIfAllowRedeemGoods(UserModel $user)
    {
        $userContactRepo = new UserContactRepo();

        $contacts = $userContactRepo->findByUserId($user->id);

        if ($contacts->count() == 0) {
            throw new BadRequestException('point_gift_redeem.no_user_contact');
        }
    }

    protected function checkStock(PointGiftModel $gift)
    {
        if ($gift->stock < 1) {
            throw new BadRequestException('point_gift_redeem.no_enough_stock');
        }
    }

    protected function checkRedeemLimit(PointGiftModel $gift, UserModel $user)
    {
        $redeemRepo = new PointGiftRedeemRepo();

        $count = $redeemRepo->countUserGiftRedeems($user->id, $gift->id);

        if ($count >= $gift->redeem_limit) {
            throw new BadRequestException('point_gift_redeem.reach_redeem_limit');
        }
    }

    protected function checkPointBalance(PointGiftModel $gift, UserModel $user)
    {
        $userRepo = new UserRepo();

        $balance = $userRepo->findUserBalance($user->id);

        if (!$balance || $balance->point < $gift->point) {
            throw new BadRequestException('point_gift_redeem.no_enough_point');
        }
    }

}
