<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Http\Admin\Services;

use App\Builders\ExamPaperList as ExamPaperListBuilder;
use App\Caches\CategoryTreeList as CategoryTreeListCache;
use App\Caches\ExamPaper as ExamPaperCache;
use App\Library\Paginator\Query as PagerQuery;
use App\Models\Category as CategoryModel;
use App\Models\ExamPaper as ExamPaperModel;
use App\Models\ExamPaperTag as ExamPaperTagModel;
use App\Models\ExamQuestion as ExamQuestionModel;
use App\Repos\ExamPaper as ExamPaperRepo;
use App\Repos\ExamPaperTag as ExamPaperTagRepo;
use App\Repos\Tag as TagRepo;
use App\Repos\User as UserRepo;
use App\Services\Category as CategoryService;
use App\Services\Logic\Exam\Paper\XmTagList as XmTagListService;
use App\Services\Sync\ExamPaperIndex as ExamPaperIndexSync;
use App\Validators\ExamPaper as ExamPaperValidator;

class ExamPaper extends Service
{

    public function getExamTypes()
    {
        return ExamPaperModel::examTypes();
    }

    public function getPackTypes()
    {
        return ExamPaperModel::packTypes();
    }

    public function getGradeTypes()
    {
        return ExamPaperModel::gradeTypes();
    }

    public function getPaperLevelTypes()
    {
        return ExamPaperModel::levelTypes();
    }

    public function getQuestionLevelTypes()
    {
        return ExamQuestionModel::levelTypes();
    }

    public function getDurationOptions()
    {
        return range(10, 180, 10);
    }

    public function getStudyExpiryOptions()
    {
        return ExamPaperModel::studyExpiryOptions();
    }

    public function getRefundExpiryOptions()
    {
        return ExamPaperModel::refundExpiryOptions();
    }

    public function getCategoryOptions()
    {
        $categoryService = new CategoryService();

        return $categoryService->getCategoryOptions(CategoryModel::TYPE_EXAM_PAPER);
    }

    public function getTeacherOptions()
    {
        $userRepo = new UserRepo();

        $teachers = $userRepo->findTeachers();

        if ($teachers->count() == 0) return [];

        $options = [];

        foreach ($teachers as $teacher) {
            $options[] = [
                'id' => $teacher->id,
                'name' => $teacher->name,
            ];
        }

        return $options;
    }

    public function getXmTags($id)
    {
        $service = new XmTagListService();

        return $service->handle($id);
    }

    public function getQuestionXmCategories(array $ids)
    {
        $cache = new CategoryTreeListCache();

        $categories = $cache->get(CategoryModel::TYPE_EXAM_QUESTION);

        $result = [];

        if (!$categories) return $result;

        foreach ($categories as $category) {
            $parent = [
                'name' => $category['name'],
                'value' => $category['id'],
                'selected' => in_array($category['id'], $ids),
            ];
            if (count($category['children']) > 0) {
                $children = [];
                foreach ($category['children'] as $child) {
                    $children[] = [
                        'name' => $child['name'],
                        'value' => $child['id'],
                        'selected' => in_array($child['id'], $ids),
                    ];
                }
                $parent['children'] = $children;
            }
            $result[] = $parent;
        }

        return $result;
    }

    public function getQuestions($id)
    {
        $paperRepo = new ExamPaperRepo();

        $questions = $paperRepo->findQuestions($id);

        if ($questions->count() == 0) return [];

        $result = [];

        foreach (ExamQuestionModel::modelTypes() as $key => $value) {
            $result[] = $this->handleModelQuestions($questions, $key);
        }

        return $result;
    }

    public function getPapers()
    {
        $pagerQuery = new PagerQuery();

        $params = $pagerQuery->getParams();

        $params['deleted'] = $params['deleted'] ?? 0;

        $sort = $pagerQuery->getSort();
        $page = $pagerQuery->getPage();
        $limit = $pagerQuery->getLimit();

        $paperRepo = new ExamPaperRepo();

        $pager = $paperRepo->paginate($params, $sort, $page, $limit);

        return $this->handlePapers($pager);
    }

    public function getPaper($id)
    {
        return $this->findOrFail($id);
    }

    public function createPaper()
    {
        $post = $this->request->getPost();

        $validator = new ExamPaperValidator();

        $paper = new ExamPaperModel();

        $paper->exam_type = $validator->checkExamType($post['exam_type']);
        $paper->pack_type = $validator->checkPackType($post['pack_type']);
        $paper->title = $validator->checkTitle($post['title']);

        $paper->create();

        $this->rebuildExamPaperCache($paper);
        $this->rebuildExamPaperIndex($paper);

        return $paper;
    }

    public function updatePaper($id)
    {
        $post = $this->request->getPost();

        $paper = $this->findOrFail($id);

        $validator = new ExamPaperValidator();

        $data = [];

        if (isset($post['category_id'])) {
            $data['category_id'] = $validator->checkCategoryId($post['category_id']);
        }

        if (isset($post['teacher_id'])) {
            $data['teacher_id'] = $validator->checkTeacherId($post['teacher_id']);
        }

        if (isset($post['xm_tag_ids'])) {
            $this->saveTags($paper, $post['xm_tag_ids']);
        }

        if (isset($post['title'])) {
            $data['title'] = $validator->checkTitle($post['title']);
        }

        if (isset($post['cover'])) {
            $data['cover'] = $validator->checkCover($post['cover']);
        }

        if (isset($post['keywords'])) {
            $data['keywords'] = $validator->checkKeywords($post['keywords']);
        }

        if (isset($post['summary'])) {
            $data['summary'] = $validator->checkSummary($post['summary']);
        }

        if (isset($post['details'])) {
            $data['details'] = $validator->checkDetails($post['details']);
        }

        if (isset($post['level'])) {
            $data['level'] = $validator->checkLevel($post['level']);
        }

        if (isset($post['duration'])) {
            $data['duration'] = $validator->checkDuration($post['duration']);
        }

        if (isset($post['grade_type'])) {
            $data['grade_type'] = $validator->checkGradeType($post['grade_type']);
        }

        if (isset($post['pass_score'])) {
            $data['pass_score'] = $validator->checkPassScore($post['pass_score']);
        }

        if (isset($post['study_expiry'])) {
            $data['study_expiry'] = $validator->checkStudyExpiry($post['study_expiry']);
        }

        if (isset($post['refund_expiry'])) {
            $data['refund_expiry'] = $validator->checkRefundExpiry($post['refund_expiry']);
        }

        if (isset($post['fake_join_count'])) {
            $data['fake_join_count'] = $validator->checkJoinCount($post['fake_join_count']);
        }

        if (isset($post['market_price'])) {
            $data['market_price'] = $validator->checkMarketPrice($post['market_price']);
        }

        if (isset($post['vip_price'])) {
            $data['vip_price'] = $validator->checkVipPrice($post['vip_price']);
        }

        if (isset($post['featured'])) {
            $data['featured'] = $validator->checkFeatureStatus($post['featured']);
        }

        if (isset($post['published'])) {
            $data['published'] = $validator->checkPublishStatus($post['published']);
            if ($post['published'] == 1) {
                $validator->checkPublishAbility($paper);
            }
        }

        $paper->assign($data);

        $paper->update();

        $this->syncSaleInfo($paper);
        $this->rebuildExamPaperCache($paper);
        $this->rebuildExamPaperIndex($paper);

        return $paper;
    }

    public function deletePaper($id)
    {
        $paper = $this->findOrFail($id);

        $paper->deleted = 1;

        $paper->update();

        $this->rebuildExamPaperCache($paper);
        $this->rebuildExamPaperIndex($paper);

        return $paper;
    }

    public function restorePaper($id)
    {
        $paper = $this->findOrFail($id);

        $paper->deleted = 0;

        $paper->update();

        $this->rebuildExamPaperCache($paper);
        $this->rebuildExamPaperIndex($paper);

        return $paper;
    }

    protected function findOrFail($id)
    {
        $validator = new ExamPaperValidator();

        return $validator->checkExamPaper($id);
    }

    protected function saveTags(ExamPaperModel $paper, $xmTagIds)
    {
        /**
         * 修改数据后，afterFetch设置的属性会失效，重新执行
         */
        $paper->afterFetch();

        $originTagIds = [];

        if ($paper->tags) {
            $originTagIds = kg_array_column($paper->tags, 'id');
        }

        $newTagIds = $xmTagIds ? explode(',', $xmTagIds) : [];
        $addedTagIds = array_diff($newTagIds, $originTagIds);

        if ($addedTagIds) {
            foreach ($addedTagIds as $tagId) {
                $paperTag = new ExamPaperTagModel();
                $paperTag->paper_id = $paper->id;
                $paperTag->tag_id = $tagId;
                $paperTag->create();
                $this->recountTagExamPapers($tagId);
            }
        }

        $deletedTagIds = array_diff($originTagIds, $newTagIds);

        if ($deletedTagIds) {
            $paperTagRepo = new ExamPaperTagRepo();
            foreach ($deletedTagIds as $tagId) {
                $paperTag = $paperTagRepo->findExamPaperTag($paper->id, $tagId);
                if ($paperTag) {
                    $paperTag->delete();
                    $this->recountTagExamPapers($tagId);
                }
            }
        }

        $paperTags = [];

        if ($newTagIds) {
            $tagRepo = new TagRepo();
            $tags = $tagRepo->findByIds($newTagIds);
            if ($tags->count() > 0) {
                foreach ($tags as $tag) {
                    $paperTags[] = ['id' => $tag->id, 'name' => $tag->name];
                    $this->recountTagExamPapers($tag->id);
                }
            }
        }

        $paper->tags = $paperTags;

        $paper->update();
    }

    protected function syncSaleInfo(ExamPaperModel $paper)
    {
        if ($paper->hasUpdated(['title', 'cover', 'market_price'])) {
            $sync = new FlashSaleSync();
            $sync->syncExamPaperInfo($paper);

            $sync = new PointGiftSync();
            $sync->syncExamPaperInfo($paper);

            $sync = new GrouponSync();
            $sync->syncExamPaperInfo($paper);

            $sync = new CouponSync();
            $sync->syncExamPaperInfo($paper);

            $sync = new DistributionSync();
            $sync->syncExamPaperInfo($paper);

            $sync = new CertificateSync();
            $sync->syncExamPaperInfo($paper);
        }
    }

    protected function rebuildExamPaperCache(ExamPaperModel $paper)
    {
        $cache = new ExamPaperCache();

        $cache->rebuild($paper->id);
    }

    protected function rebuildExamPaperIndex(ExamPaperModel $paper)
    {
        $sync = new ExamPaperIndexSync();

        $sync->addItem($paper->id);
    }

    protected function recountTagExamPapers($tagId)
    {
        $tagRepo = new TagRepo();

        $tag = $tagRepo->findById($tagId);

        if (!$tag) return;

        $paperCount = $tagRepo->countExamPapers($tagId);

        $tag->paper_count = $paperCount;

        $tag->update();
    }

    /**
     * @param ExamQuestionModel[] $questions
     * @param int $model
     * @return array
     */
    protected function handleModelQuestions($questions, $model)
    {
        $result = [
            'model' => $model,
            'question_count' => 0,
            'total_score' => 0,
            'questions' => [],
        ];

        foreach ($questions as $question) {
            if ($question->model == $model) {
                $result['total_score'] += $question->score;
                $result['question_count'] += 1;
                $result['questions'][] = $question;
            }
        }

        return $result;
    }

    protected function handlePapers($pager)
    {
        if ($pager->total_items > 0) {

            $builder = new ExamPaperListBuilder();

            $pipeA = $pager->items->toArray();
            $pipeB = $builder->handleCategories($pipeA);
            $pipeC = $builder->objects($pipeB);

            $pager->items = $pipeC;
        }

        return $pager;
    }

}
