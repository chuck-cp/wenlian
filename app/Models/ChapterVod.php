<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Models;

class ChapterVod extends Model
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
     * 文件编号
     *
     * @var string
     */
    public $file_id = '';

    /**
     * 原始文件
     *
     * @var array|string
     */
    public $file_origin = [];

    /**
     * 常规转码
     *
     * @var array|string
     */
    public $file_transcode = [];

    /**
     * 加密转码
     *
     * @var array|string
     */
    public $file_encrypt = [];

    /**
     * 远程资源
     *
     * @var array|string
     */
    public $file_remote = [];

    /**
     * 点播设置
     *
     * @var array|string
     */
    public $settings = [
        'comment_enabled' => 1,
        'danmu_enabled' => 1,
        'speed_enabled' => 1,
        'verify_enabled' => 0,
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
        return 'kg_chapter_vod';
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
        if (is_array($this->file_origin)) {
            $this->file_origin = kg_json_encode($this->file_origin);
        }

        if (is_array($this->file_transcode)) {
            $this->file_transcode = kg_json_encode($this->file_transcode);
        }

        if (is_array($this->file_encrypt)) {
            $this->file_encrypt = kg_json_encode($this->file_encrypt);
        }

        if (is_array($this->file_remote)) {
            $this->file_remote = kg_json_encode($this->file_remote);
        }

        if (is_array($this->settings)) {
            $this->settings = kg_json_encode($this->settings);
        }
    }

    public function afterFetch()
    {
        if (is_string($this->file_origin)) {
            $this->file_origin = json_decode($this->file_origin, true);
        }

        if (is_string($this->file_transcode)) {
            $this->file_transcode = json_decode($this->file_transcode, true);
        }

        if (is_string($this->file_encrypt)) {
            $this->file_encrypt = json_decode($this->file_encrypt, true);
        }

        if (is_string($this->file_remote)) {
            $this->file_remote = json_decode($this->file_remote, true);
        }

        if (is_string($this->settings)) {
            $this->settings = json_decode($this->settings, true);
        }
    }

}
