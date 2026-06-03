<?php
/**
 * @copyright Copyright (c) 2023 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Validators;

use App\Exceptions\BadRequest as BadRequestException;
use App\Models\Danmu as DanmuModel;
use App\Repos\Danmu as DanmuRepo;
use Phalcon\Text;

class Danmu extends Validator
{

    public function checkDanmu($id)
    {
        $danmuRepo = new DanmuRepo();

        $danmu = $danmuRepo->findById($id);

        if (!$danmu) {
            throw new BadRequestException('danmu.not_found');
        }

        return $danmu;
    }

    public function checkChapter($id)
    {
        $validator = new Chapter();

        return $validator->checkChapter($id);
    }

    public function checkAuthor($id)
    {
        $validator = new User();

        return $validator->checkUser($id);
    }

    public function checkText($text)
    {
        $value = $this->filter->sanitize($text, ['trim', 'string']);

        $length = kg_strlen($value);

        if ($length < 1) {
            throw new BadRequestException('danmu.text_too_short');
        }

        if ($length > 50) {
            throw new BadRequestException('danmu.text_too_long');
        }

        return $value;
    }

    public function checkColor($color)
    {
        if (is_int($color)) {
            $color = sprintf('#%06s', dechex($color));
        }

        if (!Text::startsWith($color, '#')) {
            throw new BadRequestException('danmu.invalid_color');
        }

        return $color;
    }

    public function checkSize($size)
    {
        if ($size < 12 || $size > 36) {
            throw new BadRequestException('danmu.invalid_size');
        }

        return $size;
    }

    public function checkType($type)
    {
        if (!array_key_exists($type, DanmuModel::posTypes())) {
            throw new BadRequestException('danmu.invalid_type');
        }

        return $type;
    }

    public function checkTime($time)
    {
        $value = (int)$time;

        if ($value < 0) {
            throw new BadRequestException('danmu.invalid_time');
        }

        if ($value > 3 * 3600) {
            throw new BadRequestException('danmu.invalid_time');
        }

        return $value;
    }

    public function checkPublishStatus($status)
    {
        if (!array_key_exists($status, DanmuModel::publishTypes())) {
            throw new BadRequestException('danmu.invalid_publish_status');
        }

        return $status;
    }

}
