<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Models;

class ChapterRead extends Model
{

    /**
     * 主键编号
     *
     * @var int
     */
    public $id = 0;

    /**
     * 课程编号
     *
     * @var int
     */
    public $course_id = 0;

    /**
     * 章节编号
     *
     * @var int
     */
    public $chapter_id = 0;

    /**
     * 内容 (html)
     *
     * @var string
     */
    public $content = '';

    /**
     * 内容 (markdown)
     *
     * @var string
     */
    public $markdown = '';

    /**
     * 格式（html|markdown）
     *
     * @var string
     */
    public $format = 'html';

    /**
     * 图文设置
     *
     * @var array|string
     */
    public $settings = [
        'comment_enabled' => 1,
        'copy_enabled' => 1,
    ];

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
        return 'kg_chapter_read';
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
        if (is_array($this->settings)) {
            $this->settings = kg_json_encode($this->settings);
        }
    }

    public function afterFetch()
    {
        if (is_string($this->settings)) {
            $this->settings = json_decode($this->settings, true);
        }
    }

}
