<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Validators;

use App\Exceptions\BadRequest as BadRequestException;
use App\Library\Validators\Common as CommonValidator;
use App\Repos\UserContact as UserContactRepo;

class UserContact extends Validator
{

    public function checkUserContact($id)
    {
        $repo = new UserContactRepo();

        $contact = $repo->findById($id);

        if (!$contact) {
            throw new BadRequestException('user_contact.not_found');
        }

        return $contact;
    }

    public function checkName($name)
    {
        $value = $this->filter->sanitize($name, ['trim', 'string']);

        $length = kg_strlen($value);

        if ($length < 2 || $length > 15) {
            throw new BadRequestException('user_contact.invalid_name');
        }

        return $value;
    }

    public function checkPhone($phone)
    {
        $value = $this->filter->sanitize($phone, ['trim', 'string']);

        if (!CommonValidator::phone($value)) {
            throw new BadRequestException('user_contact.invalid_phone');
        }

        return $value;
    }

    public function checkAddProvince($province)
    {
        $value = $this->filter->sanitize($province, ['trim', 'string']);

        $length = kg_strlen($value);

        if ($length < 2 || $length > 15) {
            throw new BadRequestException('user_contact.invalid_add_province');
        }

        return $value;
    }

    public function checkAddCity($city)
    {
        $value = $this->filter->sanitize($city, ['trim', 'string']);

        $length = kg_strlen($value);

        if ($length < 2 || $length > 15) {
            throw new BadRequestException('user_contact.invalid_add_city');
        }

        return $value;
    }

    public function checkAddCounty($county)
    {
        $value = $this->filter->sanitize($county, ['trim', 'string']);

        $length = kg_strlen($value);

        if ($length < 2 || $length > 15) {
            throw new BadRequestException('user_contact.invalid_add_county');
        }

        return $value;
    }

    public function checkAddOther($other)
    {
        $value = $this->filter->sanitize($other, ['trim', 'string']);

        $length = kg_strlen($value);

        if ($length < 5 || $length > 50) {
            throw new BadRequestException('user_contact.invalid_add_other');
        }

        return $value;
    }

    public function checkContactLimit($userId)
    {
        $repo = new UserContactRepo();

        $contacts = $repo->findByUserId($userId);

        if ($contacts->count() > 10) {
            throw new BadRequestException('invoice_account.reach_contact_limit');
        }
    }

}
