<?php
/**
 * @copyright Copyright (c) 2023 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Console\Queues\Main;

use App\Models\KgProduct as KgProductModel;
use App\Models\PointGiftRedeem as PointGiftRedeemModel;
use App\Models\Task as TaskModel;
use App\Repos\Article as ArticleRepo;
use App\Repos\Course as CourseRepo;
use App\Repos\ExamPaper as ExamPaperRepo;
use App\Repos\PointGift as PointGiftRepo;
use App\Repos\PointGiftRedeem as PointGiftRedeemRepo;
use App\Repos\User as UserRepo;
use App\Repos\Vip as VipRepo;
use App\Services\Logic\Deliver\ArticleDeliver as ArticleDeliverService;
use App\Services\Logic\Deliver\CourseDeliver as CourseDeliverService;
use App\Services\Logic\Deliver\ExamPaperDeliver as ExamPaperDeliverService;
use App\Services\Logic\Deliver\VipDeliver as VipDeliverService;
use App\Services\Logic\Notice\External\PointGiftRedeem as PointGiftRedeemNotice;
use App\Services\Logic\Point\History\PointGiftRefund as PointGiftRefundPointHistory;
use App\Traits\Service as ServiceTrait;
use Phalcon\Di\Injectable;

class PointGiftDeliver extends Injectable
{

    use ServiceTrait;

    public function handle(TaskModel $task)
    {
        echo '------ start deliver task ------' . PHP_EOL;

        $redeemRepo = new PointGiftRedeemRepo();

        $redeem = $redeemRepo->findById($task->item_id);

        try {

            $this->db->begin();

            switch ($redeem->gift_type) {
                case KgProductModel::ITEM_COURSE:
                    $this->handleCourseRedeem($redeem);
                    break;
                case KgProductModel::ITEM_VIP:
                    $this->handleVipRedeem($redeem);
                    break;
                case KgProductModel::ITEM_EXAM_PAPER:
                    $this->handleExamPaperRedeem($redeem);
                    break;
                case KgProductModel::ITEM_ARTICLE:
                    $this->handleArticleRedeem($redeem);
                    break;
                case KgProductModel::ITEM_GOODS:
                    $this->handleGoodsRedeem($redeem);
                    break;
            }

            $task->status = TaskModel::STATUS_FINISHED;
            $task->update();

            $this->db->commit();

        } catch (\Exception $e) {

            $this->db->rollback();

            $task->status = TaskModel::STATUS_FAILED;
            $task->update();

            $this->handlePointRefund($redeem);

            $logger = $this->getLogger('deliver');

            $logger->error('Point Gift Deliver Task Exception: ' . kg_json_encode([
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'message' => $e->getMessage(),
                    'task' => $task,
                ]));
        }

        echo '------ end deliver task ------' . PHP_EOL;
    }

    protected function handleCourseRedeem(PointGiftRedeemModel $redeem)
    {
        $giftRepo = new PointGiftRepo();

        $gift = $giftRepo->findById($redeem->gift_id);

        $courseRepo = new CourseRepo();

        $course = $courseRepo->findById($gift->attrs['id']);

        $redeem->status = PointGiftRedeemModel::STATUS_FINISHED;

        $redeem->update();

        $userRepo = new UserRepo();

        $user = $userRepo->findById($redeem->user_id);

        $deliverService = new CourseDeliverService();

        $deliverService->handle($course, $user);
    }

    protected function handleVipRedeem(PointGiftRedeemModel $redeem)
    {
        $giftRepo = new PointGiftRepo();

        $gift = $giftRepo->findById($redeem->gift_id);

        $vipRepo = new VipRepo();

        $vip = $vipRepo->findById($gift->attrs['id']);

        $redeem->status = PointGiftRedeemModel::STATUS_FINISHED;

        $redeem->update();

        $userRepo = new UserRepo();

        $user = $userRepo->findById($redeem->user_id);

        $deliverService = new VipDeliverService();

        $deliverService->handle($vip, $user);
    }

    protected function handleExamPaperRedeem(PointGiftRedeemModel $redeem)
    {
        $giftRepo = new PointGiftRepo();

        $gift = $giftRepo->findById($redeem->gift_id);

        $paperRepo = new ExamPaperRepo();

        $paper = $paperRepo->findById($gift->attrs['id']);

        $redeem->status = PointGiftRedeemModel::STATUS_FINISHED;

        $redeem->update();

        $userRepo = new UserRepo();

        $user = $userRepo->findById($redeem->user_id);

        $deliverService = new ExamPaperDeliverService();

        $deliverService->handle($paper, $user);
    }

    protected function handleArticleRedeem(PointGiftRedeemModel $redeem)
    {
        $giftRepo = new PointGiftRepo();

        $gift = $giftRepo->findById($redeem->gift_id);

        $articleRepo = new ArticleRepo();

        $article = $articleRepo->findById($gift->attrs['id']);

        $redeem->status = PointGiftRedeemModel::STATUS_FINISHED;

        $redeem->update();

        $userRepo = new UserRepo();

        $user = $userRepo->findById($redeem->user_id);

        $deliverService = new ArticleDeliverService();

        $deliverService->handle($article, $user);
    }

    protected function handleGoodsRedeem(PointGiftRedeemModel $redeem)
    {
        $notice = new PointGiftRedeemNotice();

        $notice->createTask($redeem);
    }

    protected function handlePointRefund(PointGiftRedeemModel $redeem)
    {
        $service = new PointGiftRefundPointHistory();

        $service->handle($redeem);
    }

}
