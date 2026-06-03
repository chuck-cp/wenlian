<?php
/**
 * @copyright Copyright (c) 2022 深圳市酷瓜软件有限公司
 * @license https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\Certificate;

use App\Services\Logic\CertificateTrait;
use App\Services\Logic\Service;

class CertInfo extends Service
{

    use CertificateTrait;

    public function handle($id)
    {
        $cert = $this->checkCert($id);

        return [
            'id' => $cert->id,
            'name' => $cert->name,
            'bg_type' => $cert->bg_type,
            'grant_type' => $cert->grant_type,
            'item_id' => $cert->item_id,
            'item_type' => $cert->item_type,
            'item_info' => $cert->item_info,
            'attrs' => $cert->attrs,
            'published' => $cert->published,
            'deleted' => $cert->deleted,
            'grant_count' => $cert->grant_count,
            'create_time' => $cert->create_time,
            'update_time' => $cert->update_time,
        ];
    }

}
