<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic\DigitalCard;

use App\Services\Logic\DigitalCardTrait;
use App\Services\Logic\Service as LogicService;

class CardInfo extends LogicService
{

    use DigitalCardTrait;

    public function handle($code)
    {

        $card = $this->checkCardByCode($code);

        return [
            'id' => $card->id,
            'code' => $card->code,
            'redeem_time' => $card->redeem_time,
            'create_time' => $card->create_time,
            'update_time' => $card->update_time,
            'user' => [
                'id' => $card->user_id,
                'name' => $card->user_name,
            ],
            'item' => [
                'id' => $card->item_id,
                'title' => $card->item_title,
                'price' => $card->item_price,
                'type' => $card->item_type,
            ],
        ];
    }

}
