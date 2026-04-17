<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Admin\Services;

use App\Caches\AppInfo as AppInfoCache;
use App\Caches\SiteGlobalStat as SiteGlobalStatCache;
use App\Caches\SiteTodayStat as SiteTodayStatCache;
use App\Library\AppInfo as AppInfo;
use App\Library\Utils\ServerInfo as ServerInfo;
use App\Repos\Stat as StatRepo;
use App\Services\Service as AppService;
use GuzzleHttp\Client as HttpClient;

class Index extends Service
{

    public function getTopMenus()
    {
        $authMenu = new AuthMenu();

        return $authMenu->getTopMenus();
    }

    public function getLeftMenus()
    {
        $authMenu = new AuthMenu();

        return $authMenu->getLeftMenus();
    }

    public function getAppInfo()
    {
        $cache = new AppInfoCache();

        $content = $cache->get();

        $appInfo = new AppInfo();

        if (empty($content) || $appInfo->get('version') != $content['version']) {
            $cache->rebuild();
        }

        return $appInfo;
    }

    public function getSiteInfo()
    {
        return $this->getSettings('site');
    }

    public function getServerInfo()
    {
        return [
            'cpu' => ServerInfo::cpu(),
            'memory' => ServerInfo::memory(),
            'disk' => ServerInfo::disk(),
        ];
    }

    public function getGlobalStat()
    {
        $cache = new SiteGlobalStatCache();

        return $cache->get();
    }

    public function getTodayStat()
    {
        $cache = new SiteTodayStatCache();

        return $cache->get();
    }

    public function getModerationStat()
    {
        $statRepo = new StatRepo();

        $reviewCount = $statRepo->countPendingReviews();
        $consultCount = $statRepo->countPendingConsults();
        $questionCount = $statRepo->countPendingQuestions();
        $answerCount = $statRepo->countPendingAnswers();
        $commentCount = $statRepo->countPendingComments();
        $danmuCount = $statRepo->countPendingDanmus();

        return [
            'review_count' => $reviewCount,
            'consult_count' => $consultCount,
            'question_count' => $questionCount,
            'answer_count' => $answerCount,
            'comment_count' => $commentCount,
            'danmu_count' => $danmuCount,
        ];
    }

    public function getBizReviewStat()
    {
        $statRepo = new StatRepo();

        $refundCount = $statRepo->countPendingRefunds();
        $invoiceCount = $statRepo->countPendingInvoices();
        $pointRedeemCount = $statRepo->countPendingPointGiftRedeems();

        return [
            'refund_count' => $refundCount,
            'invoice_count' => $invoiceCount,
            'point_redeem_count' => $pointRedeemCount,
        ];
    }

    public function getReportStat()
    {
        $statRepo = new StatRepo();

        $examQuestionCount = $statRepo->countReportedExamQuestions();
        $articleCount = $statRepo->countReportedArticles();
        $questionCount = $statRepo->countReportedQuestions();
        $answerCount = $statRepo->countReportedAnswers();
        $commentCount = $statRepo->countReportedComments();

        return [
            'exam_question_count' => $examQuestionCount,
            'article_count' => $articleCount,
            'question_count' => $questionCount,
            'answer_count' => $answerCount,
            'comment_count' => $commentCount,
        ];
    }

    public function getReleases()
    {
        $url = 'https://www.koogua.com/api/releases';

        $client = new HttpClient();

        $response = $client->get($url);

        $content = json_decode($response->getBody()->getContents(), true);

        return $content['releases'] ?? [];
    }

}
