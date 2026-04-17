<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Validators;

use App\Exceptions\BadRequest as BadRequestException;
use App\Models\DigitalCard as DigitalCardModel;
use App\Repos\DigitalCard as DigitalCardRepo;

class DigitalCard extends Validator
{

    public function checkById($id)
    {
        $cardRepo = new DigitalCardRepo();

        $card = $cardRepo->findById($id);

        if (!$card) {
            throw new BadRequestException('digital_card.not_found');
        }

        return $card;
    }

    public function checkByCode($code)
    {
        $cardRepo = new DigitalCardRepo();

        $card = $cardRepo->findByCode($code);

        if (!$card) {
            throw new BadRequestException('digital_card.not_found');
        }

        return $card;
    }

    public function checkRemark($remark)
    {
        $value = $this->filter->sanitize($remark, ['trim', 'string']);

        $length = kg_strlen($value);

        if ($length > 200) {
            throw new BadRequestException('digital_card.remark_too_long');
        }

        return $value;
    }

    public function checkExpiry($expiry)
    {
        $value = $this->filter->sanitize($expiry, ['trim', 'int']);

        if ($expiry < 1) {
            throw new BadRequestException('digital_card.invalid_expiry');
        }

        return $value;
    }

    public function checkInsertCount($count)
    {
        $value = $this->filter->sanitize($count, ['trim', 'int']);

        if ($value < 1 || $value > 100) {
            throw new BadRequestException('digital_card.invalid_insert_count');
        }

        return $value;
    }

    public function checkItemType($type)
    {
        if (!array_key_exists($type, DigitalCardModel::itemTypes())) {
            throw new BadRequestException('digital_card.invalid_item_type');
        }

        return $type;
    }

    public function checkCourse($id)
    {
        $validator = new Course();

        return $validator->checkCourse($id);
    }

    public function checkPackage($id)
    {
        $validator = new Package();

        return $validator->checkPackage($id);
    }

    public function checkVip($id)
    {
        $validator = new Vip();

        return $validator->checkVip($id);
    }

    public function checkExamPaper($id)
    {
        $validator = new ExamPaper();

        return $validator->checkExamPaper($id);
    }

    public function checkArticle($id)
    {
        $validator = new Article();

        return $validator->checkArticle($id);
    }

    public function checkIfActiveCard(DigitalCardModel $card)
    {
        if ($card->expire_time < time()) {
            throw new BadRequestException('digital_card.has_expired');
        }

        if ($card->user_id > 0) {
            throw new BadRequestException('digital_card.has_redeemed');
        }
    }

}
