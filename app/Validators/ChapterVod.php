<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Validators;

use App\Exceptions\BadRequest as BadRequestException;
use App\Library\Validators\Common as CommonValidator;
use App\Models\Chapter as ChapterModel;

class ChapterVod extends Validator
{

    public function checkFileId($fileId)
    {
        $value = $this->filter->sanitize($fileId, ['trim', 'int']);

        if (!CommonValidator::intNumber($value)) {
            throw new BadRequestException('chapter_vod.invalid_file_id');
        }

        return $value;
    }

    public function checkTransMode($mode)
    {
        if (!array_key_exists($mode, ChapterModel::transModeTypes())) {
            throw new BadRequestException('chapter_vod.invalid_trans_mode');
        }

        return $mode;
    }

    public function checkDuration($duration)
    {
        $value = $this->filter->sanitize($duration, ['trim', 'int']);

        if ($value < 10 || $value > 10 * 3600) {
            throw new BadRequestException('chapter_vod.invalid_duration');
        }

        return $value;
    }

    public function checkFileUrl($url)
    {
        $value = $this->filter->sanitize($url, ['trim', 'string']);

        if (!CommonValidator::url($value)) {
            throw new BadRequestException('chapter_vod.invalid_file_url');
        }

        $path = parse_url($value, PHP_URL_PATH);

        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        /**
         * 点播只支持mp4,m3u8格式
         */
        if (!in_array($ext, ['mp4', 'm3u8'])) {
            throw new BadRequestException('chapter_vod.invalid_file_ext');
        }

        return $value;
    }

    public function checkRemoteFile($hd, $sd, $fd)
    {
        if (empty($hd) && empty($sd) && empty($fd)) {
            throw new BadRequestException('chapter_vod.remote_file_required');
        }
    }

}
