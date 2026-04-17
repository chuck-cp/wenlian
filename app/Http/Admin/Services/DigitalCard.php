<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Admin\Services;

use App\Http\Admin\Services\Traits\AccountSearchTrait;
use App\Http\Admin\Services\Traits\DigitalCardSearchTrait;
use App\Library\Paginator\Query as PagerQuery;
use App\Models\DigitalCard as DigitalCardModel;
use App\Models\KgSale as KgSaleModel;
use App\Repos\Article as ArticleRepo;
use App\Repos\Course as CourseRepo;
use App\Repos\DigitalCard as DigitalCardRepo;
use App\Repos\ExamPaper as ExamPaperRepo;
use App\Repos\Package as PackageRepo;
use App\Repos\Vip as VipRepo;
use App\Validators\DigitalCard as DigitalCardValidator;

class DigitalCard extends Service
{

    use DigitalCardSearchTrait;
    use AccountSearchTrait;

    public function getItemTypes()
    {
        return DigitalCardModel::itemTypes();
    }

    public function getXmCourses()
    {
        $courseRepo = new CourseRepo();

        $items = $courseRepo->findAll([
            'free' => 0,
            'published' => 1,
            'deleted' => 0,
        ]);

        if ($items->count() == 0) return [];

        $result = [];

        foreach ($items as $item) {
            $result[] = [
                'name' => sprintf('%s - %s（¥%0.2f）', $item->id, $item->title, $item->market_price),
                'value' => $item->id,
            ];
        }

        return $result;
    }

    public function getXmPackages()
    {
        $packageRepo = new PackageRepo();

        $items = $packageRepo->findAll([
            'published' => 1,
            'deleted' => 0,
        ]);

        if ($items->count() == 0) return [];

        $result = [];

        foreach ($items as $item) {
            $result[] = [
                'name' => sprintf('%s - %s（¥%0.2f）', $item->id, $item->title, $item->market_price),
                'value' => $item->id,
            ];
        }

        return $result;
    }

    public function getXmVips()
    {
        $vipRepo = new VipRepo();

        $items = $vipRepo->findAll([
            'published' => 1,
            'deleted' => 0,
        ]);

        if ($items->count() == 0) return [];

        $result = [];

        foreach ($items as $item) {
            $result[] = [
                'name' => sprintf('%s - %s（¥%0.2f）', $item->id, $item->title, $item->price),
                'value' => $item->id,
            ];
        }

        return $result;
    }

    public function getXmExamPapers()
    {
        $paperRepo = new ExamPaperRepo();

        $items = $paperRepo->findAll([
            'free' => 0,
            'published' => 1,
            'deleted' => 0,
        ]);

        if ($items->count() == 0) return [];

        $result = [];

        foreach ($items as $item) {
            $result[] = [
                'name' => sprintf('%s（¥%0.2f）', $item->title, $item->market_price),
                'value' => $item->id,
            ];
        }

        return $result;
    }

    public function getXmArticles()
    {
        $articleRepo = new ArticleRepo();

        $items = $articleRepo->findAll([
            'free' => 0,
            'published' => 1,
            'deleted' => 0,
        ]);

        if ($items->count() == 0) return [];

        $result = [];

        foreach ($items as $item) {
            $result[] = [
                'name' => sprintf('%s（¥%0.2f）', $item->title, $item->market_price),
                'value' => $item->id,
            ];
        }

        return $result;
    }

    public function getDigitalCards()
    {
        $pagerQuery = new PagerQuery();

        $params = $pagerQuery->getParams();

        $params = $this->handleDigitalCardSearchParams($params);
        $params = $this->handleAccountSearchParams($params);

        $params['deleted'] = $params['deleted'] ?? 0;

        $sort = $pagerQuery->getSort();
        $page = $pagerQuery->getPage();
        $limit = $pagerQuery->getLimit();

        $cardRepo = new DigitalCardRepo();

        return $cardRepo->paginate($params, $sort, $page, $limit);
    }

    public function createDigitalCard()
    {
        $post = $this->request->getPost();

        $validator = new DigitalCardValidator();

        $post['item_type'] = $validator->checkItemType($post['item_type']);

        switch ($post['item_type']) {
            case KgSaleModel::ITEM_COURSE:
                $this->createCourseDigitalCard($post);
                break;
            case KgSaleModel::ITEM_PACKAGE:
                $this->createPackageDigitalCard($post);
                break;
            case KgSaleModel::ITEM_VIP:
                $this->createVipDigitalCard($post);
                break;
            case KgSaleModel::ITEM_EXAM_PAPER:
                $this->createExamPaperDigitalCard($post);
                break;
            case KgSaleModel::ITEM_ARTICLE:
                $this->createArticleDigitalCard($post);
                break;
        }
    }

    public function deleteDigitalCard($id)
    {
        $card = $this->findOrFail($id);

        $manager = new DigitalCardCancel();

        $manager->handle($card->id);

        return $card;
    }

    protected function createCourseDigitalCard($post)
    {
        $validator = new DigitalCardValidator();

        $course = $validator->checkCourse($post['xm_course_id']);
        $expiry = $validator->checkExpiry($post['expiry']);
        $insertCount = $validator->checkInsertCount($post['insert_count']);

        $prefix = sprintf('%s%s', KgSaleModel::ITEM_COURSE, $course->id);
        $expireTime = strtotime("+{$expiry} months");
        $createTime = time();

        $rows = [];

        for ($i = 0; $i < $insertCount; $i++) {
            $rows[] = [
                'item_id' => $course->id,
                'item_title' => $course->title,
                'item_price' => $course->market_price,
                'item_type' => KgSaleModel::ITEM_COURSE,
                'code' => DigitalCardModel::getRandCode($prefix),
                'expire_time' => $expireTime,
                'create_time' => $createTime,
            ];
        }

        $card = new DigitalCardModel();

        $sql = kg_batch_insert_sql($card->getSource(), $rows);

        try {

            $this->db->begin();
            $this->db->execute($sql);
            $this->db->commit();

        } catch (\Exception $e) {

            $this->db->rollback();

            $logger = $this->getLogger();

            $logger->error('Create Course Digital Card Exception: ' . kg_json_encode([
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'message' => $e->getMessage(),
                ]));

            throw new \RuntimeException('sys.rollback');
        }
    }

    protected function createPackageDigitalCard($post)
    {
        $validator = new DigitalCardValidator();

        $package = $validator->checkPackage($post['xm_package_id']);
        $expiry = $validator->checkExpiry($post['expiry']);
        $insertCount = $validator->checkInsertCount($post['insert_count']);

        $prefix = sprintf('%s%s', KgSaleModel::ITEM_PACKAGE, $package->id);
        $expireTime = strtotime("+{$expiry} months");
        $createTime = time();

        $rows = [];

        for ($i = 0; $i < $insertCount; $i++) {
            $rows[] = [
                'item_id' => $package->id,
                'item_title' => $package->title,
                'item_price' => $package->market_price,
                'item_type' => KgSaleModel::ITEM_PACKAGE,
                'code' => DigitalCardModel::getRandCode($prefix),
                'expire_time' => $expireTime,
                'create_time' => $createTime,
            ];
        }

        $card = new DigitalCardModel();

        $sql = kg_batch_insert_sql($card->getSource(), $rows);

        try {

            $this->db->begin();
            $this->db->execute($sql);
            $this->db->commit();

        } catch (\Exception $e) {

            $this->db->rollback();

            $logger = $this->getLogger();

            $logger->error('Create Package Digital Card Exception: ' . kg_json_encode([
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'message' => $e->getMessage(),
                ]));

            throw new \RuntimeException('sys.trans_rollback');
        }
    }

    protected function createVipDigitalCard($post)
    {
        $validator = new DigitalCardValidator();

        $vip = $validator->checkVip($post['xm_vip_id']);
        $expiry = $validator->checkExpiry($post['expiry']);
        $insertCount = $validator->checkInsertCount($post['insert_count']);

        $prefix = sprintf('%s%s', KgSaleModel::ITEM_VIP, $vip->id);
        $itemTitle = sprintf('会员服务（%s个月）', $vip->expiry);
        $expireTime = strtotime("+{$expiry} months");
        $createTime = time();

        $rows = [];

        for ($i = 0; $i < $insertCount; $i++) {
            $rows[] = [
                'item_id' => $vip->id,
                'item_title' => $itemTitle,
                'item_price' => $vip->price,
                'item_type' => KgSaleModel::ITEM_VIP,
                'code' => DigitalCardModel::getRandCode($prefix),
                'expire_time' => $expireTime,
                'create_time' => $createTime,
            ];
        }

        $card = new DigitalCardModel();

        $sql = kg_batch_insert_sql($card->getSource(), $rows);

        try {

            $this->db->begin();
            $this->db->execute($sql);
            $this->db->commit();

        } catch (\Exception $e) {

            $this->db->rollback();

            $logger = $this->getLogger();

            $logger->error('Create Vip Digital Card Exception: ' . kg_json_encode([
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'message' => $e->getMessage(),
                ]));

            throw new \RuntimeException('sys.trans_rollback');
        }
    }

    protected function createExamPaperDigitalCard($post)
    {
        $validator = new DigitalCardValidator();

        $paper = $validator->checkExamPaper($post['xm_paper_id']);
        $expiry = $validator->checkExpiry($post['expiry']);
        $insertCount = $validator->checkInsertCount($post['insert_count']);

        $prefix = sprintf('%s%s', KgSaleModel::ITEM_EXAM_PAPER, $paper->id);
        $expireTime = strtotime("+{$expiry} months");
        $createTime = time();

        $rows = [];

        for ($i = 0; $i < $insertCount; $i++) {
            $rows[] = [
                'item_id' => $paper->id,
                'item_title' => $paper->title,
                'item_price' => $paper->market_price,
                'item_type' => KgSaleModel::ITEM_EXAM_PAPER,
                'code' => DigitalCardModel::getRandCode($prefix),
                'expire_time' => $expireTime,
                'create_time' => $createTime,
            ];
        }

        $card = new DigitalCardModel();

        $sql = kg_batch_insert_sql($card->getSource(), $rows);

        try {

            $this->db->begin();
            $this->db->execute($sql);
            $this->db->commit();

        } catch (\Exception $e) {

            $this->db->rollback();

            $logger = $this->getLogger();

            $logger->error('Create Exam Paper Digital Card Exception: ' . kg_json_encode([
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'message' => $e->getMessage(),
                ]));

            throw new \RuntimeException('sys.rollback');
        }
    }

    protected function createArticleDigitalCard($post)
    {
        $validator = new DigitalCardValidator();

        $article = $validator->checkArticle($post['xm_article_id']);
        $expiry = $validator->checkExpiry($post['expiry']);
        $insertCount = $validator->checkInsertCount($post['insert_count']);

        $prefix = sprintf('%s%s', KgSaleModel::ITEM_ARTICLE, $article->id);
        $expireTime = strtotime("+{$expiry} months");
        $createTime = time();

        $rows = [];

        for ($i = 0; $i < $insertCount; $i++) {
            $rows[] = [
                'item_id' => $article->id,
                'item_title' => $article->title,
                'item_price' => $article->market_price,
                'item_type' => KgSaleModel::ITEM_ARTICLE,
                'code' => DigitalCardModel::getRandCode($prefix),
                'expire_time' => $expireTime,
                'create_time' => $createTime,
            ];
        }

        $card = new DigitalCardModel();

        $sql = kg_batch_insert_sql($card->getSource(), $rows);

        try {

            $this->db->begin();
            $this->db->execute($sql);
            $this->db->commit();

        } catch (\Exception $e) {

            $this->db->rollback();

            $logger = $this->getLogger();

            $logger->error('Create Article Digital Card Exception: ' . kg_json_encode([
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'message' => $e->getMessage(),
                ]));

            throw new \RuntimeException('sys.rollback');
        }
    }

    protected function findOrFail($id)
    {
        $validator = new DigitalCardValidator();

        return $validator->checkById($id);
    }

}
