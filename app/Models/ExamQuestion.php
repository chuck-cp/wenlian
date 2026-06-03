<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Models;

use Phalcon\Mvc\Model\Behavior\SoftDelete;

class ExamQuestion extends Model
{

    /**
     * 题目类型
     */
    const MODEL_SINGLE_CHOICE = 1; // 单选题
    const MODEL_MULTIPLE_CHOICE = 2; // 多选题
    const MODEL_TRUE_FALSE = 3; // 判断题
    const MODEL_BLANK_FILL = 4; // 填空题
    const MODEL_SHORT_ANSWER = 5; // 简答题
    const MODEL_COMPLEX_QUESTION = 9; // 题冒题

    /**
     * 难度级别
     */
    const LEVEL_JUNIOR = 1; // 初级
    const LEVEL_MEDIUM = 2; // 中级
    const LEVEL_SENIOR = 3; // 高级

    /**
     * @var array
     *
     * 单选题扩展属性
     */
    protected $_single_choice_attrs = [
        'choices' => [
            'A' => '',
            'B' => '',
            'C' => '',
            'D' => '',
        ],
    ];

    /**
     * @var array
     *
     * 多选题扩展属性
     */
    protected $_multiple_choice_attrs = [
        'choices' => [
            'A' => '',
            'B' => '',
            'C' => '',
            'D' => '',
        ],
    ];

    /**
     * @var array
     *
     * 是非题扩展属性
     */
    protected $_true_false_attrs = [
        'choices' => [
            'T' => '正确',
            'F' => '错误',
        ],
    ];

    /**
     * @var array
     *
     * 填空题扩展属性
     */
    protected $_blank_fill_attrs = [];

    /**
     * @var array
     *
     * 简答题扩展属性
     */
    protected $_short_answer_attrs = [];

    /**
     * @var array
     *
     * 题冒题扩展属性
     */
    protected $_complex_question_attrs = [];

    /**
     * 主键编号
     *
     * @var int
     */
    public $id = 0;

    /**
     * 题干
     *
     * @var string
     */
    public $topic = '';

    /**
     * 答案
     *
     * @var string
     */
    public $answer = '';

    /**
     * 解析
     *
     * @var string
     */
    public $solution = '';

    /**
     * 扩展属性
     *
     * @var array|string
     */
    public $attrs = [];

    /**
     * 题目分值
     *
     * @var int
     */
    public $score = 0;

    /**
     * 建议用时（秒）
     *
     * @var int
     */
    public $duration = 0;

    /**
     * 题目类型
     *
     * @var int
     */
    public $model = 0;

    /**
     * 难度级别
     *
     * @var int
     */
    public $level = 0;

    /**
     * 优先级（子题排序）
     *
     * @var int
     */
    public $priority = 10;

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
     * 上级编号
     *
     * @var int
     */
    public $parent_id = 0;

    /**
     * 分类编号
     *
     * @var int
     */
    public $category_id = 0;

    /**
     * 收藏次数
     *
     * @var int
     */
    public $favorite_count = 0;

    /**
     * 举报次数
     *
     * @var int
     */
    public $report_count = 0;

    /**
     * 参与人数
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
        return 'kg_exam_question';
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
        if (empty($this->attrs)) {
            if ($this->model == self::MODEL_SINGLE_CHOICE) {
                $this->attrs = $this->_single_choice_attrs;
            } elseif ($this->model == self::MODEL_MULTIPLE_CHOICE) {
                $this->attrs = $this->_multiple_choice_attrs;
            } elseif ($this->model == self::MODEL_TRUE_FALSE) {
                $this->attrs = $this->_true_false_attrs;
            } elseif ($this->model == self::MODEL_BLANK_FILL) {
                $this->attrs = $this->_blank_fill_attrs;
            } elseif ($this->model == self::MODEL_SHORT_ANSWER) {
                $this->attrs = $this->_short_answer_attrs;
            } elseif ($this->model == self::MODEL_COMPLEX_QUESTION) {
                $this->attrs = $this->_complex_question_attrs;
            }
        }

        if (is_array($this->attrs)) {
            $this->attrs = kg_json_encode($this->attrs);
        }

        $this->create_time = time();
    }

    public function beforeUpdate()
    {
        if (is_array($this->attrs)) {
            $this->attrs = kg_json_encode($this->attrs);
        }

        $this->update_time = time();
    }

    public function afterFetch()
    {
        if (is_string($this->attrs)) {
            $this->attrs = json_decode($this->attrs, true);
        }
    }

    public static function modelTypes()
    {
        return [
            self::MODEL_SINGLE_CHOICE => '单选题',
            self::MODEL_MULTIPLE_CHOICE => '多选题',
            self::MODEL_TRUE_FALSE => '判断题',
            self::MODEL_BLANK_FILL => '填空题',
            self::MODEL_SHORT_ANSWER => '简答题',
            self::MODEL_COMPLEX_QUESTION => '题帽题',
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

}
