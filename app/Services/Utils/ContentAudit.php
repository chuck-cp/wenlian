<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Utils;

use App\Services\Storage as StorageService;
use App\Services\Service as AppService;

class ContentAudit extends AppService
{

    public function auditHtml($html)
    {
        $result = -1;

        $storage = new StorageService();

        $text = $this->filterText($html);

        if (kg_strlen($text) > 0) {
            $result = $storage->detectText($text);
            if ($result == 1) {
                return $result;
            }
        }

        $images = $this->filterImages($html);

        if (count($images) > 0) {
            $result = $storage->detectImages($images);
            if ($result == 1) {
                return $result;
            }
        }

        return $result;
    }

    protected function filterText($html)
    {
        $text = strip_tags($html);

        $text = str_replace("\n\n", "\n", $text);

        return trim($text);
    }

    protected function filterImages($html)
    {
        preg_match_all('/img src="(.*?)"/i', $html, $matches);

        return $matches[1];
    }

}
