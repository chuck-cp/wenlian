<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Models;

class ExamPaperQuestion extends Model
{

    /**
     * 主键编号
     *
     * @var int
     */
    public $id = 0;

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
     * 优先级
     *
     * @var int
     */
    public $priority = 0;

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
        return 'kg_exam_paper_question';
    }

    public function beforeCreate()
    {
        $this->create_time = time();
    }

    public function beforeUpdate()
    {
        $this->update_time = time();
    }

}
