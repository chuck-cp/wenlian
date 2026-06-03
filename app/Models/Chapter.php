<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Models;

use App\Caches\MaxChapterId as MaxChapterIdCache;
use Phalcon\Mvc\Model\Behavior\SoftDelete;

class Chapter extends Model
{

    /**
     * 转码模式
     */
    const TRANS_MODE_STANDARD = 'standard'; // 标准转码
    const TRANS_MODE_ENCRYPT = 'encrypt'; // 加密转码
    const TRANS_MODE_NONE = 'none'; // 暂不转码

    /**
     * 转码状态
     */
    const TRANS_STATUS_PENDING = 'pending'; // 待启动
    const TRANS_STATUS_CREATED = 'created'; // 已创建
    const TRANS_STATUS_PROCESSING = 'processing'; // 转码中
    const TRANS_STATUS_FINISHED = 'finished'; // 已完成
    const TRANS_STATUS_FAILED = 'failed'; // 已失败

    /**
     * 推流状态
     */
    const STREAM_STATUS_ACTIVE = 'active'; // 活跃
    const STREAM_STATUS_INACTIVE = 'inactive'; // 静默
    const STREAM_STATUS_FORBID = 'forbid'; // 禁播

    /**
     * @var array
     *
     * 点播扩展属性
     */
    protected $_vod_attrs = [
        'duration' => 0,
        'transcode' => [
            'standard' => ['status' => self::TRANS_STATUS_PENDING],
            'encrypt' => ['status' => self::TRANS_STATUS_PENDING],
        ],
    ];

    /**
     * @var array
     *
     * 直播扩展属性
     */
    protected $_live_attrs = [
        'start_time' => 0,
        'end_time' => 0,
        'stream' => ['status' => self::STREAM_STATUS_INACTIVE],
        'playback' => ['ready' => 0, 'duration' => 0],
    ];

    /**
     * @var array
     *
     * 图文扩展属性
     */
    protected $_read_attrs = [
        'format' => 'html',
        'duration' => 0,
        'word_count' => 0,
    ];

    /**
     * @var array
     *
     * 面授扩展属性
     */
    protected $_offline_attrs = [
        'start_time' => 0,
        'end_time' => 0,
    ];

    /**
     * @var array
     *
     * 文档扩展属性
     */
    protected $_doc_attrs = [
        'format' => 'html',
        'size' => 0,
    ];

    /**
     * 主键编号
     *
     * @var int
     */
    public $id = 0;

    /**
     * 父级编号
     *
     * @var int
     */
    public $parent_id = 0;

    /**
     * 课程编号
     *
     * @var int
     */
    public $course_id = 0;

    /**
     * 标题
     *
     * @var string
     */
    public $title = '';

    /**
     * 摘要
     *
     * @var string
     */
    public $summary = '';

    /**
     * 优先级
     *
     * @var int
     */
    public $priority = 10;

    /**
     * 免费标识
     *
     * @var int
     */
    public $free = 0;

    /**
     * 模式类型
     *
     * @var int
     */
    public $model = 0;

    /**
     * 扩展属性
     *
     * @var array|string
     */
    public $attrs = [];

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
     * 课时数
     *
     * @var int
     */
    public $lesson_count = 0;

    /**
     * 学员数
     *
     * @var int
     */
    public $user_count = 0;

    /**
     * 评论数
     *
     * @var int
     */
    public $comment_count = 0;

    /**
     * 点赞数
     *
     * @var int
     */
    public $like_count = 0;

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
        return 'kg_chapter';
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
        /**
         * @var Course $course
         */
        $course = Course::findFirst($this->course_id);

        if (empty($this->model)) {
            $this->model = $course->model;
        }

        if ($this->parent_id > 0) {
            if (empty($this->attrs)) {
                if ($this->model == Course::MODEL_VOD) {
                    $this->attrs = $this->_vod_attrs;
                } elseif ($this->model == Course::MODEL_LIVE) {
                    $this->attrs = $this->_live_attrs;
                } elseif ($this->model == Course::MODEL_READ) {
                    $this->attrs = $this->_read_attrs;
                } elseif ($this->model == Course::MODEL_OFFLINE) {
                    $this->attrs = $this->_offline_attrs;
                } elseif ($this->model == Course::MODEL_DOC) {
                    $this->attrs = $this->_doc_attrs;
                }
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

    public function afterCreate()
    {
        $cache = new MaxChapterIdCache();

        $cache->rebuild();

        if ($this->parent_id > 0) {

            $data = [
                'course_id' => $this->course_id,
                'chapter_id' => $this->id,
            ];

            $extend = false;

            switch ($this->model) {
                case Course::MODEL_VOD:
                    $vod = new ChapterVod();
                    $vod->assign($data);
                    $vod->create();
                    break;
                case Course::MODEL_LIVE:
                    $live = new ChapterLive();
                    $live->assign($data);
                    $live->create();
                    break;
                case Course::MODEL_READ:
                    $read = new ChapterRead();
                    $read->assign($data);
                    $read->create();
                    break;
                case Course::MODEL_OFFLINE:
                    $offline = new ChapterOffline();
                    $offline->assign($data);
                    $offline->create();
                    break;
                case Course::MODEL_DOC:
                    $doc = new ChapterDoc();
                    $doc->assign($data);
                    $doc->create();
                    break;
            }
        }
    }

    public function afterFetch()
    {
        if (is_string($this->attrs)) {
            $this->attrs = json_decode($this->attrs, true);
        }
    }

    public static function transModeTypes()
    {
        return [
            self::TRANS_MODE_STANDARD => '标准转码',
            self::TRANS_MODE_ENCRYPT => '加密转码',
            self::TRANS_MODE_NONE => '暂不转码',
        ];
    }

    public static function transStatusTypes()
    {
        return [
            self::TRANS_STATUS_PENDING => '待启动',
            self::TRANS_STATUS_CREATED => '已创建',
            self::TRANS_STATUS_PROCESSING => '转码中',
            self::TRANS_STATUS_FINISHED => '已完成',
            self::TRANS_STATUS_FAILED => '已失败',
        ];
    }

    public static function streamStatusTypes()
    {
        return [
            self::STREAM_STATUS_ACTIVE => '直播中',
            self::STREAM_STATUS_INACTIVE => '未开播',
            self::STREAM_STATUS_FORBID => '已禁止',
        ];
    }

}
