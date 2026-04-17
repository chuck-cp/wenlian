<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Api\Controllers;

use App\Services\Logic\User\Console\AccountInfo as AccountInfoService;
use App\Services\Logic\User\Console\AffiliateSaleTrend as AffiliateSaleStatService;
use App\Services\Logic\User\Console\AffiliateTeamList as AffiliateTeamListService;
use App\Services\Logic\User\Console\AnswerList as AnswerListService;
use App\Services\Logic\User\Console\BalanceInfo as BalanceInfoService;
use App\Services\Logic\User\Console\CashHistory as CashHistoryService;
use App\Services\Logic\User\Console\CertificateList as CertificateListService;
use App\Services\Logic\User\Console\ConsultList as ConsultListService;
use App\Services\Logic\User\Console\ContactList as ContactListService;
use App\Services\Logic\User\Console\CouponList as CouponListService;
use App\Services\Logic\User\Console\DigitalRedeemList as DigitalRedeemListService;
use App\Services\Logic\User\Console\FavoriteList as FavoriteListService;
use App\Services\Logic\User\Console\InvoiceAccountList as InvoiceAccountListService;
use App\Services\Logic\User\Console\InvoiceList as InvoiceListService;
use App\Services\Logic\User\Console\NotificationList as NotificationListService;
use App\Services\Logic\User\Console\NotificationRead as NotificationReadService;
use App\Services\Logic\User\Console\NotifyStats as NotifyStatsService;
use App\Services\Logic\User\Console\Online as OnlineService;
use App\Services\Logic\User\Console\OrderList as OrderListService;
use App\Services\Logic\User\Console\PointGiftRedeemList as PointGiftRedeemListService;
use App\Services\Logic\User\Console\PointHistory as PointHistoryService;
use App\Services\Logic\User\Console\ProfileInfo as ProfileInfoService;
use App\Services\Logic\User\Console\ProfileUpdate as ProfileUpdateService;
use App\Services\Logic\User\Console\QuestionList as QuestionListService;
use App\Services\Logic\User\Console\RefundList as RefundListService;
use App\Services\Logic\User\Console\ReviewList as ReviewListService;
use App\Services\Logic\User\Console\StudyArticleList as StudyArticleListService;
use App\Services\Logic\User\Console\StudyCourseList as StudyCourseListService;
use App\Services\Logic\User\Console\StudyExamPaperList as StudyExamPaperListService;
use App\Services\Logic\User\Console\WithdrawAccountList as WithdrawAccountListService;
use App\Services\Logic\User\Console\WithdrawList as WithdrawListService;

/**
 * @RoutePrefix("/api/uc")
 */
class UserConsoleController extends Controller
{

    /**
     * @Get("/profile", name="api.uc.profile")
     */
    public function profileAction()
    {
        $service = new ProfileInfoService();

        $profile = $service->handle();

        return $this->jsonSuccess(['profile' => $profile]);
    }

    /**
     * @Get("/account", name="api.uc.account")
     */
    public function accountAction()
    {
        $service = new AccountInfoService();

        $account = $service->handle();

        return $this->jsonSuccess(['account' => $account]);
    }

    /**
     * @Get("/balance", name="api.uc.balance")
     */
    public function balanceAction()
    {
        $service = new BalanceInfoService();

        $balance = $service->handle();

        return $this->jsonSuccess(['balance' => $balance]);
    }

    /**
     * @Get("/study/courses", name="api.uc.study_courses")
     */
    public function studyCoursesAction()
    {
        $service = new StudyCourseListService();

        $pager = $service->handle();

        return $this->jsonPaginate($pager);
    }

    /**
     * @Get("/study/exam/papers", name="api.uc.study_exam_papers")
     */
    public function studyExamPapersAction()
    {
        $service = new StudyExamPaperListService();

        $pager = $service->handle();

        return $this->jsonPaginate($pager);
    }

    /**
     * @Get("/study/articles", name="api.uc.study_articles")
     */
    public function studyArticlesAction()
    {
        $service = new StudyArticleListService();

        $pager = $service->handle();

        return $this->jsonPaginate($pager);
    }

    /**
     * @Get("/questions", name="api.uc.questions")
     */
    public function questionsAction()
    {
        $service = new QuestionListService();

        $pager = $service->handle();

        return $this->jsonPaginate($pager);
    }

    /**
     * @Get("/answers", name="api.uc.answers")
     */
    public function answersAction()
    {
        $service = new AnswerListService();

        $pager = $service->handle();

        return $this->jsonPaginate($pager);
    }

    /**
     * @Get("/certificates", name="api.uc.certificates")
     */
    public function certificatesAction()
    {
        $service = new CertificateListService();

        $pager = $service->handle();

        return $this->jsonPaginate($pager);
    }

    /**
     * @Get("/favorites", name="api.uc.favorites")
     */
    public function favoritesAction()
    {
        $service = new FavoriteListService();

        $pager = $service->handle();

        return $this->jsonPaginate($pager);
    }

    /**
     * @Get("/consults", name="api.uc.consults")
     */
    public function consultsAction()
    {
        $service = new ConsultListService();

        $pager = $service->handle();

        return $this->jsonPaginate($pager);
    }

    /**
     * @Get("/reviews", name="api.uc.reviews")
     */
    public function reviewsAction()
    {
        $service = new ReviewListService();

        $pager = $service->handle();

        return $this->jsonPaginate($pager);
    }

    /**
     * @Get("/orders", name="api.uc.orders")
     */
    public function ordersAction()
    {
        $service = new OrderListService();

        $pager = $service->handle();

        return $this->jsonPaginate($pager);
    }

    /**
     * @Get("/refunds", name="api.uc.refunds")
     */
    public function refundsAction()
    {
        $service = new RefundListService();

        $pager = $service->handle();

        return $this->jsonPaginate($pager);
    }

    /**
     * @Get("/coupons", name="api.uc.coupons")
     */
    public function couponsAction()
    {
        $service = new CouponListService();

        $pager = $service->handle();

        return $this->jsonPaginate($pager);
    }

    /**
     * @Get("/cash/history", name="api.uc.cash_history")
     */
    public function cashHistoryAction()
    {
        $service = new CashHistoryService();

        $pager = $service->handle();

        return $this->jsonPaginate($pager);
    }

    /**
     * @Get("/point/history", name="api.uc.point_history")
     */
    public function pointHistoryAction()
    {
        $service = new PointHistoryService();

        $pager = $service->handle();

        return $this->jsonPaginate($pager);
    }

    /**
     * @Get("/digital/redeems", name="api.uc.digital_redeems")
     */
    public function digitalRedeemsAction()
    {
        $service = new DigitalRedeemListService();

        $pager = $service->handle();

        return $this->jsonPaginate($pager);
    }

    /**
     * @Get("/point/redeems", name="api.uc.point_redeems")
     */
    public function pointRedeemsAction()
    {
        $service = new PointGiftRedeemListService();

        $pager = $service->handle();

        return $this->jsonPaginate($pager);
    }

    /**
     * @Get("/invoices", name="api.uc.invoices")
     */
    public function invoicesAction()
    {
        $service = new InvoiceListService();

        $pager = $service->handle();

        return $this->jsonPaginate($pager);
    }

    /**
     * @Get("/invoice/accounts", name="api.uc.invoice_accounts")
     */
    public function invoiceAccountsAction()
    {
        $service = new InvoiceAccountListService();

        $items = $service->handle();

        return $this->jsonSuccess(['items' => $items]);
    }

    /**
     * @Get("/withdraws", name="api.uc.withdraws")
     */
    public function withdrawsAction()
    {
        $service = new WithdrawListService();

        $pager = $service->handle();

        return $this->jsonPaginate($pager);
    }

    /**
     * @Get("/withdraw/accounts", name="api.uc.withdraw_accounts")
     */
    public function withdrawAccountsAction()
    {
        $service = new WithdrawAccountListService();

        $items = $service->handle();

        return $this->jsonSuccess(['items' => $items]);
    }

    /**
     * @Get("/affiliate/sale/teams", name="api.uc.affiliate_sale_teams")
     */
    public function affiliateSaleTeamsAction()
    {
        $service = new AffiliateTeamListService();

        $pager = $service->handle();

        return $this->jsonPaginate($pager);
    }

    /**
     * @Get("/affiliate/sale/trend", name="api.uc.affiliate_sale_trend")
     */
    public function affiliateSaleTrendAction()
    {
        $service = new AffiliateSaleStatService();

        $saleTrend = $service->handle();

        return $this->jsonSuccess(['sale_trend' => $saleTrend]);
    }

    /**
     * @Get("/contacts", name="api.uc.contacts")
     */
    public function contactsAction()
    {
        $service = new ContactListService();

        $contacts = $service->handle();

        return $this->jsonSuccess(['contacts' => $contacts]);
    }

    /**
     * @Get("/notifications", name="api.uc.notifications")
     */
    public function notificationsAction()
    {
        $service = new NotificationListService();

        $pager = $service->handle();

        $service = new NotificationReadService();

        $service->handle();

        return $this->jsonPaginate($pager);
    }

    /**
     * @Get("/notify/stats", name="api.uc.notify_stats")
     */
    public function notifyStatsAction()
    {
        $service = new NotifyStatsService();

        $stats = $service->handle();

        return $this->jsonSuccess(['stats' => $stats]);
    }

    /**
     * @Post("/profile/update", name="api.uc.update_profile")
     */
    public function updateProfileAction()
    {
        $service = new ProfileUpdateService();

        $service->handle();

        return $this->jsonSuccess();
    }

    /**
     * @Post("/online", name="api.uc.online")
     */
    public function onlineAction()
    {
        $service = new OnlineService();

        $service->handle();

        return $this->jsonSuccess();
    }

}
