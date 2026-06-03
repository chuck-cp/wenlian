<?php
/**
 * @copyright Copyright (c) 2023 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Models;

use Phalcon\Text;

class CertificateUser extends Model
{

    /**
     * 主键编号
     *
     * @var int
     */
    public $id = 0;

    /**
     * 证书序号
     *
     * @var string
     */
    public $sn = '';

    /**
     * 证书路径
     *
     * @var string
     */
    public $cert_path = '';

    /**
     * 证书编号
     *
     * @var int
     */
    public $cert_id = 0;

    /**
     * 用户编号
     *
     * @var int
     */
    public $user_id = 0;

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
        return 'kg_certificate_user';
    }

    public function beforeCreate()
    {
        $this->sn = $this->getCertSn();

        $this->create_time = time();
    }

    public function beforeUpdate()
    {
        if (Text::startsWith($this->cert_path, 'http')) {
            $this->cert_path = self::getCertPath($this->cert_path);
        }

        $this->update_time = time();
    }

    public function afterFetch()
    {
        if (!Text::startsWith($this->cert_path, 'http')) {
            $this->cert_path = kg_cos_img_url($this->cert_path);
        }
    }

    public static function getCertPath($url)
    {
        if (Text::startsWith($url, 'http')) {
            return parse_url($url, PHP_URL_PATH);
        }

        return $url;
    }

    protected function getCertSn()
    {
        $sn = date('Ymd') . rand(10000, 99999);

        $result = self::findFirst([
            'conditions' => 'sn = :sn:',
            'bind' => ['sn' => $sn],
        ]);

        if (!$result) return $sn;

        return $this->getCertSn();
    }

}
