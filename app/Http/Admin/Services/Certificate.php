<?php
/**
 * @copyright Copyright (c) 2023 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Admin\Services;

use App\Library\Paginator\Query as PagerQuery;
use App\Models\Certificate as CertificateModel;
use App\Models\KgSale as KgSaleModel;
use App\Repos\Certificate as CertificateRepo;
use App\Repos\Course as CourseRepo;
use App\Repos\ExamPaper as ExamPaperRepo;
use App\Repos\Topic as TopicRepo;
use App\Validators\Certificate as CertificateValidator;

class Certificate extends Service
{

    public function getGrantTypes()
    {
        return CertificateModel::grantTypes();
    }

    public function getItemTypes()
    {
        return CertificateModel::itemTypes();
    }

    public function getXmCourses(CertificateModel $cert)
    {
        $courseRepo = new CourseRepo();

        $items = $courseRepo->findAll([
            'published' => 1,
            'deleted' => 0,
        ]);

        if ($items->count() == 0) return [];

        $result = [];

        foreach ($items as $item) {
            $result[] = [
                'name' => sprintf('%s - %s（¥%0.2f）', $item->id, $item->title, $item->market_price),
                'value' => $item->id,
                'selected' => $item->id == $cert->item_id,
            ];
        }

        return $result;
    }

    public function getXmExamPapers(CertificateModel $cert)
    {
        $paperRepo = new ExamPaperRepo();

        $items = $paperRepo->findAll([
            'published' => 1,
            'deleted' => 0,
        ]);

        if ($items->count() == 0) return [];

        $result = [];

        foreach ($items as $item) {
            $result[] = [
                'name' => sprintf('%s - %s（¥%0.2f）', $item->id, $item->title, $item->market_price),
                'value' => $item->id,
                'selected' => $item->id == $cert->item_id,
            ];
        }

        return $result;
    }

    public function getXmTopics(CertificateModel $cert)
    {
        $topicRepo = new TopicRepo();

        $topics = $topicRepo->findForCertXm();

        if ($topics->count() == 0) {
            return [];
        }

        $result = [];

        foreach ($topics as $item) {
            $result[] = [
                'name' => sprintf('%s - %s', $item->id, $item->title),
                'value' => $item->id,
                'selected' => $item->id == $cert->item_id,
            ];
        }

        return $result;
    }

    public function getCertificates()
    {
        $pagerQuery = new PagerQuery();

        $params = $pagerQuery->getParams();

        $params['deleted'] = $params['deleted'] ?? 0;

        $sort = $pagerQuery->getSort();
        $page = $pagerQuery->getPage();
        $limit = $pagerQuery->getLimit();

        $certRepo = new CertificateRepo();

        return $certRepo->paginate($params, $sort, $page, $limit);
    }

    public function createCertificate()
    {
        $post = $this->request->getPost();
        $grantType = $this->request->getPost('grant_type', 'int', CertificateModel::GRANT_TYPE_MANUAL);

        $validator = new CertificateValidator();

        $cert = new CertificateModel();

        $cert->name = $validator->checkName($post['name']);
        $cert->grant_type = $validator->checkGrantType($grantType);
        $cert->item_type = $validator->checkItemType($post['item_type']);

        $cert->create();

        return $cert;
    }

    public function getCertificate($id)
    {
        return $this->findOrFail($id);
    }

    public function updateCertificate($id)
    {
        $cert = $this->findOrFail($id);

        $post = $this->request->getPost();

        $validator = new CertificateValidator();

        $data = [];

        if (isset($post['name'])) {
            $data['name'] = $validator->checkName($post['name']);
        }

        if (isset($post['item_type'])) {
            $sync = new CertificateSync();
            $data['item_type'] = $validator->checkItemType($post['item_type']);
            if ($post['item_type'] == KgSaleModel::ITEM_COURSE) {
                $course = $validator->checkCourse($post['xm_course_id']);
                $data['item_id'] = $course->id;
                $data['item_info'] = $sync->getOriginCourseInfo($course);
            } elseif ($post['item_type'] == KgSaleModel::ITEM_EXAM_PAPER) {
                $paper = $validator->checkExamPaper($post['xm_paper_id']);
                $data['item_id'] = $paper->id;
                $data['item_info'] = $sync->getOriginExamPaperInfo($paper);
            } elseif ($post['item_type'] == KgSaleModel::ITEM_TOPIC) {
                $topic = $validator->checkTopic($post['xm_topic_id']);
                $data['item_id'] = $topic->id;
                $data['item_info'] = $sync->getOriginTopicInfo($topic);
            }
        }

        if (isset($post['grant_type'])) {
            $data['grant_type'] = $validator->checkGrantType($post['grant_type']);
        }

        if (isset($post['published'])) {
            $data['published'] = $validator->checkPublishStatus($post['published']);
        }

        $cert->assign($data);

        $cert->update();

        return $cert;
    }

    public function deleteCertificate($id)
    {
        $cert = $this->findOrFail($id);

        $cert->deleted = 1;

        $cert->update();

        return $cert;
    }

    public function restoreCertificate($id)
    {
        $cert = $this->findOrFail($id);

        $cert->deleted = 0;

        $cert->update();

        return $cert;
    }

    protected function findOrFail($id)
    {
        $validator = new CertificateValidator();

        return $validator->checkCertificate($id);
    }

}
