<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Models;

class UserReferer extends Model
{

    /**
     * 主键编号
     *
     * @var int
     */
    public $id = 0;

    /**
     * 用户编号
     *
     * @var int
     */
    public $user_id = 0;

    /**
     * 上级编号
     *
     * @var int
     */
    public $parent_id = 0;

    /**
     * 上级层级
     *
     * @var int
     */
    public $parent_level = 0;

    /**
     * 创建时间
     *
     * @var int
     */
    public $create_time = 0;

    public function getSource(): string
    {
        return 'kg_user_referer';
    }

    public function beforeCreate()
    {
        $this->create_time = time();
    }

}
