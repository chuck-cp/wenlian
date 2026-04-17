<?php
/**
 * @copyright Copyright (c) 2022 深圳市文联软件有限公司
 * @license https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Url;

use App\Models\Course as CourseModel;
use App\Models\ExamPaper as ExamPaperModel;
use App\Repos\Chapter as ChapterRepo;
use App\Repos\ExamPaper as ExamPaperRepo;
use App\Services\Service as AppService;
use App\Traits\Client as ClientTrait;

class FullH5Url extends AppService
{

    /**
     * 基准地址
     *
     * @var string
     */
    protected $baseUrl;

    /**
     * 跳转来源
     *
     * @var string
     */
    protected $source = 'pc';

    use ClientTrait;

    public function __construct()
    {
        $this->baseUrl = $this->getBaseUrl();
    }

    public function getHomeUrl()
    {
        return $this->getFullUrl('/index/index');
    }

    public function getAccountRegisterUrl()
    {
        return $this->getFullUrl('/account/register');
    }

    public function getAccountLoginUrl()
    {
        return $this->getFullUrl('/account/login');
    }

    public function getAccountForgetUrl()
    {
        return $this->getFullUrl('/account/forget');
    }

    public function getVipIndexUrl()
    {
        return $this->getFullUrl('/vip/index');
    }

    public function getHelpIndexUrl()
    {
        return $this->getFullUrl('/help/index');
    }

    public function getCourseListUrl()
    {
        return $this->getFullUrl('/course/list');
    }

    public function getArticleListUrl()
    {
        return $this->getFullUrl('/article/list');
    }

    public function getQuestionListUrl()
    {
        return $this->getFullUrl('/question/list');
    }

    public function getLiveListUrl()
    {
        return $this->getFullUrl('/live/list');
    }

    public function getTeacherListUrl()
    {
        return $this->getFullUrl('/teacher/list');
    }

    public function getCouponListUrl()
    {
        return $this->getFullUrl('/coupon/list');
    }

    public function getGrouponListUrl()
    {
        return $this->getFullUrl('/groupon/list');
    }

    public function getDistributionListUrl()
    {
        return $this->getFullUrl('/distribution/list');
    }

    public function getFlashSaleListUrl()
    {
        return $this->getFullUrl('/flash-sale/list');
    }

    public function getPointGiftListUrl()
    {
        return $this->getFullUrl('/point/gift/list');
    }

    public function getExamPaperListUrl()
    {
        return $this->getFullUrl('/exam/paper/list');
    }

    public function getPageInfoUrl($id)
    {
        return $this->getFullUrl('/page/info', ['id' => $id]);
    }

    public function getHelpInfoUrl($id)
    {
        return $this->getFullUrl('/help/info', ['id' => $id]);
    }

    public function getArticleInfoUrl($id)
    {
        return $this->getFullUrl('/article/info', ['id' => $id]);
    }

    public function getQuestionInfoUrl($id)
    {
        return $this->getFullUrl('/question/info', ['id' => $id]);
    }

    public function getAnswerInfoUrl($id)
    {
        return $this->getFullUrl('/answer/info', ['id' => $id]);
    }

    public function getTopicInfoUrl($id)
    {
        return $this->getFullUrl('/topic/info', ['id' => $id]);
    }

    public function getPackageInfoUrl($id)
    {
        return $this->getFullUrl('/package/info', ['id' => $id]);
    }

    public function getGrouponInfoUrl($id)
    {
        return $this->getFullUrl('/groupon/info', ['id' => $id]);
    }

    public function getCourseInfoUrl($id)
    {
        return $this->getFullUrl('/course/info', ['id' => $id]);
    }

    public function getChapterInfoUrl($id)
    {
        $chapterRepo = new ChapterRepo();

        $chapter = $chapterRepo->findById($id);

        if ($chapter->model == CourseModel::MODEL_VOD) {
            return $this->getFullUrl('/chapter/vod', ['id' => $id]);
        } elseif ($chapter->model == CourseModel::MODEL_LIVE) {
            return $this->getFullUrl('/chapter/live', ['id' => $id]);
        } elseif ($chapter->model == CourseModel::MODEL_READ) {
            return $this->getFullUrl('/chapter/read', ['id' => $id]);
        } else {
            return $this->getHomeUrl();
        }
    }

    public function getUserIndexUrl($id)
    {
        return $this->getFullUrl('/user/index', ['id' => $id]);
    }

    public function getTeacherIndexUrl($id)
    {
        return $this->getFullUrl('/teacher/index', ['id' => $id]);
    }

    public function getPointGiftInfoUrl($id)
    {
        return $this->getFullUrl('/point/gift/info', ['id' => $id]);
    }

    public function getExamPaperInfoUrl($id)
    {
        $paperRepo = new ExamPaperRepo();

        $paper = $paperRepo->findById($id);

        if ($paper->exam_type == ExamPaperModel::EXAM_TYPE_MOCK) {
            return $this->getFullUrl('/exam/paper/mock-info', ['id' => $id]);
        } elseif ($paper->exam_type == ExamPaperModel::EXAM_TYPE_UNIT) {
            return $this->getFullUrl('/exam/paper/unit-info', ['id' => $id]);
        }

        return $this->getHomeUrl();
    }

    public function getMockExamExploreUrl($id)
    {
        return $this->getFullUrl('/exam/mock-explore', ['id' => $id]);
    }

    public function getUnitExamExploreUrl($id)
    {
        return $this->getFullUrl('/exam/unit-explore', ['id' => $id]);
    }

    public function getMistakeExamExploreUrl()
    {
        return $this->getFullUrl('/exam/mistake-explore');
    }

    public function getFavoriteExamExploreUrl()
    {
        return $this->getFullUrl('/exam/favorite-explore');
    }

    public function getExamImageUploadUrl($params)
    {
        return $this->getFullUrl('/exam/upload-image', $params);
    }

    protected function getFullUrl($path, $params = [])
    {
        $extra = ['source' => $this->source];

        $data = array_merge($params, $extra);

        $query = http_build_query($data);

        return sprintf('%s%s?%s', $this->baseUrl, $path, $query);
    }

    protected function getBaseUrl()
    {
        return sprintf('%s/h5/#/pages', kg_site_url());
    }

}
