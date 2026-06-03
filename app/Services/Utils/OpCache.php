<?php
/**
 * @copyright Copyright (c) 2023 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Utils;

use App\Services\Service as AppService;
use Phalcon\Text;

class OpCache extends AppService
{

    public function reset($scope)
    {
        $rootPath = root_path();

        if ($scope == 'diff') {
            exec('git diff --name-only HEAD~ HEAD', $files);
            foreach ($files as $file) {
                if (Text::endsWith($file, '.php')) {
                    $filename = sprintf('%s/%s', $rootPath, $file);
                    opcache_invalidate($filename, true);
                }
            }
        } elseif ($scope == 'all') {
            opcache_reset();
        }
    }

}
