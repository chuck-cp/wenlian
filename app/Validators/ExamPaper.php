<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Validators;

use App\Caches\ExamPaper as ExamPaperCache;
use App\Caches\MaxExamPaperId as MaxExamPaperIdCache;
use App\Exceptions\BadRequest as BadRequestException;
use App\Library\Validators\Common as CommonValidator;
use App\Models\ExamPaper as ExamPaperModel;
use App\Repos\ExamPaper as ExamPaperRepo;
use App\Services\EditorStorage as EditorStorageService;

class ExamPaper extends Validator
{

    /**
     * @param int $id
     * @return ExamPaperModel
     * @throws BadRequestException
     */
    public function checkExamPaperCache($id)
    {
        $this->checkId($id);

        $paperCache = new ExamPaperCache();

        $paper = $paperCache->get($id);

        if (!$paper) {
            throw new BadRequestException('exam_paper.not_found');
        }

        return $paper;
    }

    public function checkExamPaper($id)
    {
        $this->checkId($id);

        $courseRepo = new ExamPaperRepo();

        $course = $courseRepo->findById($id);

        if (!$course) {
            throw new BadRequestException('exam_paper.not_found');
        }

        return $course;
    }

    public function checkId($id)
    {
        $id = intval($id);

        $maxIdCache = new MaxExamPaperIdCache();

        $maxId = $maxIdCache->get();

        if ($id < 1 || $id > $maxId) {
            throw new BadRequestException('exam_paper.not_found');
        }
    }

    public function checkTitle($title)
    {
        $value = $this->filter->sanitize($title, ['trim', 'string']);

        $length = kg_strlen($value);

        if ($length < 5 || $length > 50) {
            throw new BadRequestException('exam_paper.invalid_title');
        }

        return $value;
    }

    public function checkCover($cover)
    {
        $value = $this->filter->sanitize($cover, ['trim', 'string']);

        if (!CommonValidator::image($value)) {
            throw new BadRequestException('exam_paper.invalid_cover');
        }

        return kg_cos_img_style_trim($value);
    }

    public function checkDetails($details)
    {
        $value = $this->filter->sanitize($details, ['trim']);

        $storage = new EditorStorageService();

        $value = $storage->handle($value);

        $length = kg_editor_content_length($value);

        if ($length > 10000) {
            throw new BadRequestException('exam_paper.detail_too_long');
        }

        return kg_clean_html($value);
    }

    public function checkSummary($summary)
    {
        $value = $this->filter->sanitize($summary, ['trim', 'string']);

        $length = kg_strlen($value);

        if ($length > 255) {
            throw new BadRequestException('exam_paper.summary_too_long');
        }

        return $value;
    }

    public function checkKeywords($keywords)
    {
        $keywords = $this->filter->sanitize($keywords, ['trim', 'string']);

        $length = kg_strlen($keywords);

        if ($length > 100) {
            throw new BadRequestException('exam_paper.keyword_too_long');
        }

        return kg_parse_keywords($keywords);
    }

    public function checkCategoryId($id)
    {
        $result = 0;

        if ($id > 0) {
            $validator = new Category();
            $category = $validator->checkCategory($id);
            $result = $category->id;
        }

        return $result;
    }

    public function checkTeacherId($id)
    {
        $result = 0;

        if ($id > 0) {
            $validator = new User();
            $user = $validator->checkTeacher($id);
            $result = $user->id;
        }

        return $result;
    }

    public function checkExamType($type)
    {
        $list = ExamPaperModel::examTypes();

        if (!array_key_exists($type, $list)) {
            throw new BadRequestException('exam_paper.invalid_exam_type');
        }

        return $type;
    }

    public function checkPackType($type)
    {
        $list = ExamPaperModel::packTypes();

        if (!array_key_exists($type, $list)) {
            throw new BadRequestException('exam_paper.invalid_pack_type');
        }

        return $type;
    }

    public function checkGradeType($type)
    {
        $list = ExamPaperModel::gradeTypes();

        if (!array_key_exists($type, $list)) {
            throw new BadRequestException('exam_paper.invalid_grade_type');
        }

        return $type;
    }

    public function checkLevel($level)
    {
        $list = ExamPaperModel::levelTypes();

        if (!array_key_exists($level, $list)) {
            throw new BadRequestException('exam_paper.invalid_level');
        }

        return $level;
    }

    public function checkDuration($duration)
    {
        $value = $this->filter->sanitize($duration, ['trim', 'int']);

        if ($value < 0 || $value > 120) {
            throw new BadRequestException('exam_paper.invalid_duration');
        }

        return $value;
    }

    public function checkPassScore($score)
    {
        $value = $this->filter->sanitize($score, ['trim', 'int']);

        if ($value < 1 || $value > 100) {
            throw new BadRequestException('exam_paper.invalid_pass_score');
        }

        return $value;
    }

    public function checkJoinCount($joinCount)
    {
        $value = $this->filter->sanitize($joinCount, ['trim', 'int']);

        if ($value < 0 || $value > 999999) {
            throw new BadRequestException('exam_paper.invalid_join_count');
        }

        return $value;
    }

    public function checkMarketPrice($price)
    {
        $value = $this->filter->sanitize($price, ['trim', 'float']);

        if ($value < 0 || $value > 999999) {
            throw new BadRequestException('exam_paper.invalid_market_price');
        }

        return $value;
    }

    public function checkVipPrice($price)
    {
        $value = $this->filter->sanitize($price, ['trim', 'float']);

        if ($value < 0 || $value > 999999) {
            throw new BadRequestException('exam_paper.invalid_vip_price');
        }

        return $value;
    }

    public function checkStudyExpiry($expiry)
    {
        $options = ExamPaperModel::studyExpiryOptions();

        if (!isset($options[$expiry])) {
            throw new BadRequestException('exam_paper.invalid_study_expiry');
        }

        return $expiry;
    }

    public function checkRefundExpiry($expiry)
    {
        $options = ExamPaperModel::refundExpiryOptions();

        if (!isset($options[$expiry])) {
            throw new BadRequestException('exam_paper.invalid_refund_expiry');
        }

        return $expiry;
    }

    public function checkFeatureStatus($status)
    {
        if (!in_array($status, [0, 1])) {
            throw new BadRequestException('exam_paper.invalid_feature_status');
        }

        return $status;
    }

    public function checkPublishStatus($status)
    {
        if (!in_array($status, [0, 1])) {
            throw new BadRequestException('exam_paper.invalid_publish_status');
        }

        return $status;
    }

    public function checkPublishAbility(ExamPaperModel $paper)
    {
        $paper->afterFetch();

        if ($paper->pack_type == ExamPaperModel::PACK_TYPE_MANUAL) {
            if ($paper->question_count < 1) {
                throw new BadRequestException('exam_paper.no_question_assigned');
            }
        } elseif ($paper->pack_type == ExamPaperModel::PACK_TYPE_RANDOM) {
            if (empty($paper->attrs['conditions'])) {
                throw new BadRequestException('exam_paper.no_question_assigned');
            }
        }
    }

}
