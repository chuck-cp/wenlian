<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Groupon;

use App\Models\GrouponTeam as GrouponTeamModel;
use App\Models\GrouponTeamUser as GrouponTeamUserModel;
use App\Models\KgProduct as KgProductModel;
use App\Models\Order as OrderModel;
use App\Repos\GrouponTeam as GrouponTeamRepo;
use App\Repos\GrouponTeamUser as GrouponTeamUserRepo;
use App\Services\Logic\Order\OrderCreate as OrderCreateService;
use App\Validators\Groupon as GrouponValidator;
use App\Validators\Order as OrderValidator;

class OrderCreate extends OrderCreateService
{

    public function run($id)
    {
        $teamId = $this->request->getPost('team_id', ['trim', 'int']);

        $grouponValidator = new GrouponValidator();

        $groupon = $grouponValidator->checkGroupon($id);

        $grouponValidator->checkIfActiveGroupon($groupon);

        $user = $this->getLoginUser();

        $this->checkUserDailyOrderLimit($user);

        $orderValidator = new OrderValidator();

        if ($teamId > 0) {

            $this->amount = $groupon->member_price;

            $orderValidator->checkAmount($this->amount);

            $team = $grouponValidator->checkTeam($teamId);

            $grouponValidator->checkIfActiveTeam($team);

            $grouponValidator->checkIfAllowJoinTeam($team, $user);

        } else {

            $this->amount = $groupon->leader_price;

            $orderValidator->checkAmount($this->amount);

            $grouponValidator->checkIfAllowPublishTeam($groupon, $user);

            $expireTime = strtotime("+{$groupon->partner_expiry} days");

            $teamRepo = new GrouponTeamRepo();

            $pendingTeam = $teamRepo->findPendingGrouponTeam($groupon->id, $user->id);

            /**
             * 存在新鲜的未支付队伍直接返回（减少队伍记录）
             */
            if ($pendingTeam) {

                $team = $pendingTeam;
                $team->expire_time = $expireTime;
                $team->update();

            } else {

                $team = new GrouponTeamModel();

                $team->groupon_id = $groupon->id;
                $team->leader_id = $user->id;
                $team->target_order_count = $groupon->partner_limit;
                $team->expire_time = $expireTime;

                $team->create();
            }
        }

        $this->promotion_id = $groupon->id;
        $this->promotion_type = OrderModel::PROMOTION_GROUPON;
        $this->promotion_info = [
            'groupon' => [
                'id' => $groupon->id,
                'leader_price' => $groupon->leader_price,
                'member_price' => $groupon->member_price,
            ],
            'team' => [
                'id' => $team->id,
                'leader_id' => $team->leader_id,
                'expire_time' => $team->expire_time,
            ],
        ];

        $order = new OrderModel();

        if ($groupon->item_type == KgProductModel::ITEM_COURSE) {

            $course = $orderValidator->checkCourse($groupon->item_id);

            $orderValidator->checkIfBoughtCourse($user->id, $course->id);

            $order = $this->createCourseOrder($course, $user);

        } elseif ($groupon->item_type == KgProductModel::ITEM_PACKAGE) {

            $package = $orderValidator->checkPackage($groupon->item_id);

            $orderValidator->checkIfBoughtPackage($user->id, $package->id);

            $order = $this->createPackageOrder($package, $user);

        } elseif ($groupon->item_type == KgProductModel::ITEM_VIP) {

            $vip = $orderValidator->checkVip($groupon->item_id);

            $order = $this->createVipOrder($vip, $user);

        } elseif ($groupon->item_type == KgProductModel::ITEM_EXAM_PAPER) {

            $paper = $orderValidator->checkExamPaper($groupon->item_id);

            $orderValidator->checkIfBoughtExamPaper($user->id, $paper->id);

            $order = $this->createExamPaperOrder($paper, $user);

        } elseif ($groupon->item_type == KgProductModel::ITEM_ARTICLE) {

            $article = $orderValidator->checkArticle($groupon->item_id);

            $orderValidator->checkIfBoughtArticle($user->id, $article->id);

            $order = $this->createArticleOrder($article, $user);
        }

        $teamUserRepo = new GrouponTeamUserRepo();

        $teamUser = $teamUserRepo->findPendingTeamUser($team->id, $user->id);

        if (!$teamUser) {

            $teamUser = new GrouponTeamUserModel();

            $teamUser->groupon_id = $groupon->id;
            $teamUser->team_id = $team->id;
            $teamUser->user_id = $user->id;
            $teamUser->order_id = $order->id;

            $teamUser->create();

        } else {

            $teamUser->order_id = $order->id;

            $teamUser->update();
        }


        $this->incrUserDailyOrderCount($user);

        return $order;
    }

}
