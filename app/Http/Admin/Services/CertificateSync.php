<?php
/**
 * @copyright Copyright (c) 2023 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Admin\Services;

use App\Models\Course as CourseModel;
use App\Models\ExamPaper as ExamPaperModel;
use App\Models\KgSale as KgSaleModel;
use App\Models\Topic as TopicModel;
use App\Repos\Certificate as CertificateRepo;

class CertificateSync extends Service
{

    public function syncCourseInfo(CourseModel $course)
    {
        $certRepo = new CertificateRepo();

        $cert = $certRepo->findItemCertificate($course->id, KgSaleModel::ITEM_COURSE);

        if ($cert) {
            $cert->item_info = $this->getOriginCourseInfo($course);
            $cert->update();
        }
    }

    public function syncExamPaperInfo(ExamPaperModel $paper)
    {
        $certRepo = new CertificateRepo();

        $cert = $certRepo->findItemCertificate($paper->id, KgSaleModel::ITEM_EXAM_PAPER);

        if ($cert) {
            $cert->item_info = $this->getOriginExamPaperInfo($paper);
            $cert->update();
        }
    }

    public function syncTopicInfo(TopicModel $topic)
    {
        $certRepo = new CertificateRepo();

        $cert = $certRepo->findItemCertificate($topic->id, KgSaleModel::ITEM_TOPIC);

        if ($cert) {
            $cert->item_info = $this->getOriginTopicInfo($topic);
            $cert->update();
        }
    }

    public function getOriginCourseInfo(CourseModel $course)
    {
        return [
            'course' => [
                'id' => $course->id,
                'title' => $course->title,
                'cover' => CourseModel::getCoverPath($course->cover),
            ]
        ];
    }

    public function getOriginExamPaperInfo(ExamPaperModel $paper)
    {
        return [
            'exam_paper' => [
                'id' => $paper->id,
                'title' => $paper->title,
                'cover' => ExamPaperModel::getCoverPath($paper->cover),
            ]
        ];
    }

    public function getOriginTopicInfo(TopicModel $topic)
    {
        return [
            'topic' => [
                'id' => $topic->id,
                'title' => $topic->title,
                'cover' => TopicModel::getCoverPath($topic->cover),
            ]
        ];
    }

}
