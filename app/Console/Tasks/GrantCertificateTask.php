<?php
/**
 * @copyright Copyright (c) 2023 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Console\Tasks;

use App\Models\Certificate as CertificateModel;
use App\Repos\Certificate as CertificateRepo;
use App\Models\CertificateUser as CertificateUserModel;
use App\Models\CourseUser as CourseUserModel;
use App\Models\ExamPaperUser as ExamPaperUserModel;
use App\Models\KgSale as KgSaleModel;
use App\Repos\Topic as TopicRepo;
use Phalcon\Mvc\Model\Resultset;
use Phalcon\Mvc\Model\ResultsetInterface;

class GrantCertificateTask extends Task
{

    public function mainAction()
    {
        $certs = $this->findAllCerts();

        echo sprintf('total certs: %s', $certs->count()) . PHP_EOL;

        if ($certs->count() == 0) return;

        echo '------ start grant certs task ------' . PHP_EOL;

        foreach ($certs as $cert) {
            if ($cert->item_type == KgSaleModel::ITEM_COURSE) {
                $this->handleCourseCert($cert);
            } elseif ($cert->item_type == KgSaleModel::ITEM_EXAM_PAPER) {
                $this->handleExamPaperCert($cert);
            } elseif ($cert->item_type == KgSaleModel::ITEM_TOPIC) {
                $this->handleTopicCert($cert);
            }
            $this->handleGrantCount($cert);
        }

        echo '------ end grant certs task ------' . PHP_EOL;
    }

    protected function handleCourseCert(CertificateModel $cert)
    {
        $courseUserIds = $this->findCourseUserIds($cert->item_id);

        $certUserIds = $this->findCertUserIds($cert->id);

        $userIds = array_diff($courseUserIds, $certUserIds);

        $grantCount = count($userIds);

        echo "------ course certs: {$grantCount} ------" . PHP_EOL;

        if ($grantCount == 0) return;

        foreach ($userIds as $userId) {
            $certUser = new CertificateUserModel();
            $certUser->cert_id = $cert->id;
            $certUser->user_id = $userId;
            $certUser->create();
        }
    }

    protected function handleExamPaperCert(CertificateModel $cert)
    {
        $paperUserIds = $this->findPaperUserIds($cert->item_id);

        $certUserIds = $this->findCertUserIds($cert->id);

        if (count($paperUserIds) == 0) return;

        $userIds = array_diff($paperUserIds, $certUserIds);

        $grantCount = count($userIds);

        echo "------ exam certs: {$grantCount} ------" . PHP_EOL;

        if ($grantCount == 0) return;

        foreach ($userIds as $userId) {
            $certUser = new CertificateUserModel();
            $certUser->cert_id = $cert->id;
            $certUser->user_id = $userId;
            $certUser->create();
        }
    }

    protected function handleTopicCert(CertificateModel $cert)
    {
        $topicRepo = new TopicRepo();

        $courses = $topicRepo->findCourses($cert->item_id);

        $courseIds = [];

        foreach ($courses as $course) {
            $courseIds[] = $course->id;
        }

        if (count($courseIds) === 0) {
            return;
        }

        $topicUserIds = null;

        foreach ($courseIds as $courseId) {
            $uids = $this->findCourseUserIds($courseId);

            if (count($uids) === 0) {
                return;
            }

            if ($topicUserIds === null) {
                $topicUserIds = $uids;
            } else {
                $topicUserIds = array_intersect($topicUserIds, $uids);
            }

            if (count($topicUserIds) === 0) {
                return;
            }
        }

        $topicUserIds = array_values($topicUserIds);

        $certUserIds = $this->findCertUserIds($cert->id);

        $userIds = array_diff($topicUserIds, $certUserIds);

        $grantCount = count($userIds);

        echo "------ topic certs: {$grantCount} ------" . PHP_EOL;

        if ($grantCount === 0) {
            return;
        }

        foreach ($userIds as $userId) {
            $certUser = new CertificateUserModel();
            $certUser->cert_id = $cert->id;
            $certUser->user_id = $userId;
            $certUser->create();
        }
    }

    protected function handleGrantCount(CertificateModel $cert)
    {
        $certRepo = new CertificateRepo();

        $grantCount = $certRepo->countGrants($cert->id);

        $cert->grant_count = $grantCount;

        $cert->update();
    }

    /**
     * @param int $certId
     * @return ResultsetInterface|Resultset|CourseUserModel[]
     */
    protected function findCertUserIds($certId)
    {
        $result = [];

        $rows = CertificateUserModel::query()
            ->where('cert_id = :cert_id:', ['cert_id' => $certId])
            ->andWhere('deleted = 0')
            ->execute();

        if ($rows->count() > 0) {
            return array_column($rows->toArray(), 'user_id');
        }

        return $result;
    }

    /**
     * @param int $courseId
     * @return array
     */
    protected function findCourseUserIds($courseId)
    {
        $result = [];

        $rows = CourseUserModel::query()
            ->where('course_id = :course_id:', ['course_id' => $courseId])
            ->andWhere('progress >= 95')
            ->andWhere('deleted = 0')
            ->execute();

        if ($rows->count() > 0) {
            return array_column($rows->toArray(), 'user_id');
        }

        return $result;
    }

    /**
     * @param int $paperId
     * @return array
     */
    protected function findPaperUserIds($paperId)
    {
        $result = [];

        $status = ExamPaperUserModel::STATUS_FINISHED;

        $rows = ExamPaperUserModel::query()
            ->where('paper_id = :paper_id:', ['paper_id' => $paperId])
            ->andWhere('status = :status:', ['status' => $status])
            ->andWhere('debut = 1')
            ->andWhere('passed = 1')
            ->andWhere('deleted = 0')
            ->execute();

        if ($rows->count() > 0) {
            return array_column($rows->toArray(), 'user_id');
        }

        return $result;
    }

    /**
     * @return ResultsetInterface|Resultset|CertificateModel[]
     */
    protected function findAllCerts()
    {
        return CertificateModel::query()
            ->where('grant_type = :grant_type:', ['grant_type' => CertificateModel::GRANT_TYPE_AUTO])
            ->andWhere('published = 1')
            ->andWhere('deleted = 0')
            ->execute();
    }

}
