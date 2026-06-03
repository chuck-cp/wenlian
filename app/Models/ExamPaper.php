<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Models;

use App\Caches\MaxExamPaperId as MaxExamPaperIdCache;
use App\Services\Sync\ExamPaperIndex as ExamPaperIndexSync;
use Phalcon\Mvc\Model\Behavior\SoftDelete;
use Phalcon\Text;

class ExamPaper extends Model
{

    /**
     * 测评方式
     */
    const EXAM_TYPE_MOCK = 1; // 模拟考试
    const EXAM_TYPE_UNIT = 2; // 同步练习

    /**
     * 组卷方式
     */
    const PACK_TYPE_MANUAL = 1; // 人工组卷
    const PACK_TYPE_RANDOM = 2; // 随机组卷

    /**
     * 主观题评分方式
     */
    const GRADE_TYPE_IGNORE = 1; // 忽略评分
    const GRADE_TYPE_TEACHER = 2; // 教师评分
    const GRADE_TYPE_STUDENT = 3; // 学员评分

    /**
     * 难度级别
     */
    const LEVEL_JUNIOR = 1; // 初级
    const LEVEL_MEDIUM = 2; // 中级
    const LEVEL_SENIOR = 3; // 高级

    /**
     * 主键编号
     *
     * @var int
     */
    public $id = 0;

    /**
     * 试卷标题
     *
     * @var string
     */
    public $title = '';

    /**
     * 试卷封面
     *
     * @var string
     */
    public $cover = '';

    /**
     * 简介
     *
     * @var string
     */
    public $summary = '';

    /**
     * 标签
     *
     * @var array|string
     */
    public $tags = [];

    /**
     * 关键字
     *
     * @var string
     */
    public $keywords = '';

    /**
     * 详情
     *
     * @var string
     */
    public $details = '';

    /**
     * 考试时长（分）
     *
     * @var int
     */
    public $duration = 30;

    /**
     * 难度级别
     *
     * @var int
     */
    public $level = self::LEVEL_JUNIOR;

    /**
     * 扩展属性
     *
     * @var array|string
     */
    public $attrs = [];

    /**
     * 考试类型
     *
     * @var int
     */
    public $exam_type = 0;

    /**
     * 组卷类型
     *
     * @var int
     */
    public $pack_type = 0;

    /**
     * 判卷方式
     *
     * @var int
     */
    public $grade_type = self::GRADE_TYPE_IGNORE;

    /**
     * 分类编号
     *
     * @var int
     */
    public $category_id = 0;

    /**
     * 教师编号
     *
     * @var int
     */
    public $teacher_id = 0;

    /**
     * 市场价格
     *
     * @var float
     */
    public $market_price = 0.00;

    /**
     * 会员价格
     *
     * @var float
     */
    public $vip_price = 0.00;

    /**
     * 试卷总分
     *
     * @var int
     */
    public $total_score = 0;

    /**
     * 及格分数
     *
     * @var int
     */
    public $pass_score = 0;

    /**
     * 学习期限（月）
     *
     * @var int
     */
    public $study_expiry = 12;

    /**
     * 退款期限（天）
     *
     * @var int
     */
    public $refund_expiry = 7;

    /**
     * 推荐标识
     *
     * @var int
     */
    public $featured = 0;

    /**
     * 发布标识
     *
     * @var int
     */
    public $published = 0;

    /**
     * 删除标识
     *
     * @var int
     */
    public $deleted = 0;

    /**
     * 题目数量
     *
     * @var int
     */
    public $question_count = 0;

    /**
     * 收藏次数
     *
     * @var int
     */
    public $favorite_count = 0;

    /**
     * 虚假参与数
     *
     * @var int
     */
    public $fake_join_count = 0;

    /**
     * 真实参与数
     *
     * @var int
     */
    public $join_count = 0;

    /**
     * 通过人数
     *
     * @var int
     */
    public $pass_count = 0;

    /**
     * 创建时间
     *
     * @var int
     */
    public $create_time = 0;

    /**
     * 更新时间
     *
     * @var int
     */
    public $update_time = 0;

    public function getSource(): string
    {
        return 'kg_exam_paper';
    }

    public function initialize()
    {
        parent::initialize();

        $this->addBehavior(
            new SoftDelete([
                'field' => 'deleted',
                'value' => 1,
            ])
        );
    }

    public function beforeCreate()
    {
        $this->create_time = time();
    }

    public function beforeUpdate()
    {
        if (time() - $this->update_time > 3600) {
            $sync = new ExamPaperIndexSync();
            $sync->addItem($this->id);
        }

        if ($this->fake_join_count < $this->join_count) {
            $this->fake_join_count = $this->join_count;
        }

        $this->update_time = time();
    }

    public function beforeSave()
    {
        if (empty($this->cover)) {
            $this->cover = kg_default_paper_cover_path();
        } elseif (Text::startsWith($this->cover, 'http')) {
            $this->cover = self::getCoverPath($this->cover);
        }

        if (is_array($this->attrs)) {
            $this->attrs = kg_json_encode($this->attrs);
        }

        if (is_array($this->tags)) {
            $this->tags = kg_json_encode($this->tags);
        }

        if (empty($this->summary)) {
            $this->summary = kg_parse_summary($this->details);
        }
    }

    public function afterCreate()
    {
        $cache = new MaxExamPaperIdCache();

        $cache->rebuild();
    }

    public function afterFetch()
    {
        if (!Text::startsWith($this->cover, 'http')) {
            $this->cover = kg_cos_paper_cover_url($this->cover);
        }

        if (is_string($this->tags)) {
            $this->tags = json_decode($this->tags, true);
        }

        if (is_string($this->attrs)) {
            $attrs = json_decode($this->attrs, true);
            $this->attrs = $this->handleRandAttrs($attrs);
        }
    }

    public function getJoinCount()
    {
        $joinCount = $this->join_count;

        if ($this->fake_join_count > $joinCount) {
            $joinCount = $this->fake_join_count;
        }

        return $joinCount;
    }

    public static function getCoverPath($url)
    {
        if (Text::startsWith($url, 'http')) {
            return parse_url($url, PHP_URL_PATH);
        }

        return $url;
    }

    protected function handleRandAttrs(array $attrs = [])
    {
        $conditions = [
            'model' => 0,
            'limit' => 0,
            'level' => [],
        ];

        if (!isset($attrs['conditions']['single_choice'])) {
            $conditions['model'] = ExamQuestion::MODEL_SINGLE_CHOICE;
            $attrs['conditions']['single_choice'] = $conditions;
        }

        if (!isset($attrs['conditions']['multiple_choice'])) {
            $conditions['model'] = ExamQuestion::MODEL_MULTIPLE_CHOICE;
            $attrs['conditions']['multiple_choice'] = $conditions;
        }

        if (!isset($attrs['conditions']['true_false'])) {
            $conditions['model'] = ExamQuestion::MODEL_TRUE_FALSE;
            $attrs['conditions']['true_false'] = $conditions;
        }

        if (!isset($attrs['conditions']['blank_fill'])) {
            $conditions['model'] = ExamQuestion::MODEL_BLANK_FILL;
            $attrs['conditions']['blank_fill'] = $conditions;
        }

        if (!isset($attrs['conditions']['short_answer'])) {
            $conditions['model'] = ExamQuestion::MODEL_SHORT_ANSWER;
            $attrs['conditions']['short_answer'] = $conditions;
        }

        if (!isset($attrs['conditions']['complex_question'])) {
            $conditions['model'] = ExamQuestion::MODEL_COMPLEX_QUESTION;
            $attrs['conditions']['complex_question'] = $conditions;
        }

        if (!isset($attrs['category_ids'])) {
            $attrs['category_ids'] = [];
        }

        return $attrs;
    }

    public static function examTypes()
    {
        return [
            self::EXAM_TYPE_MOCK => '模拟考试',
            self::EXAM_TYPE_UNIT => '同步练习',
        ];
    }

    public static function packTypes()
    {
        return [
            self::PACK_TYPE_MANUAL => '人工组卷',
            self::PACK_TYPE_RANDOM => '随机组卷',
        ];
    }

    public static function gradeTypes()
    {
        return [
            self::GRADE_TYPE_IGNORE => '忽略评分',
            self::GRADE_TYPE_TEACHER => '教师评分',
            self::GRADE_TYPE_STUDENT => '学员评分',
        ];
    }

    public static function levelTypes()
    {
        return [
            self::LEVEL_JUNIOR => '初级',
            self::LEVEL_MEDIUM => '中级',
            self::LEVEL_SENIOR => '高级',
        ];
    }

    public static function sortTypes()
    {
        return [
            'latest' => '最新',
            'popular' => '最热',
            'featured' => '推荐',
            'free' => '免费',
        ];
    }

    public static function studyExpiryOptions()
    {
        return Course::studyExpiryOptions();
    }

    public static function refundExpiryOptions()
    {
        return Course::refundExpiryOptions();
    }

}
