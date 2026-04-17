<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Models;

class ExamQuestionUser extends Model
{

    /**
     * 主键编号
     *
     * @var int
     */
    public $id = 0;

    /**
     * 场次编号
     *
     * @var int
     */
    public $paper_user_id = 0;

    /**
     * 试卷编号
     *
     * @var int
     */
    public $paper_id = 0;

    /**
     * 题目编号
     *
     * @var int
     */
    public $question_id = 0;

    /**
     * 用户编号
     *
     * @var int
     */
    public $user_id = 0;

    /**
     * 父题编号
     *
     * @var int
     */
    public $question_parent_id = 0;

    /**
     * 题目类型
     *
     * @var int
     */
    public $question_model = 0;

    /**
     * 题目分值
     *
     * @var int
     */
    public $question_score = 0;

    /**
     * 题目时长（建议）
     *
     * @var int
     */
    public $question_duration = 0;

    /**
     * 用户得分
     *
     * @var int
     */
    public $user_score = 0;

    /**
     * 用户答案
     *
     * @var string
     */
    public $user_answer = '';

    /**
     * 答案附图
     *
     * @var string|array
     */
    public $user_answer_files = [];

    /**
     * 用户耗时（秒）
     *
     * @var int
     */
    public $user_duration = 0;

    /**
     * 完成标识
     *
     * @var int
     */
    public $finished = 0;

    /**
     * 删除标识
     *
     * @var int
     */
    public $deleted = 0;

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
        return 'kg_exam_question_user';
    }

    public function beforeCreate()
    {
        $this->create_time = time();
    }

    public function beforeUpdate()
    {
        $this->update_time = time();
    }

    public function beforeSave()
    {
        if (is_array($this->user_answer_files)) {
            $this->user_answer_files = kg_json_encode($this->user_answer_files);
        }
    }

    public function afterFetch()
    {
        if (is_string($this->user_answer_files)) {
            $this->user_answer_files = json_decode($this->user_answer_files, true);
        }
    }

}
