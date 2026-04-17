<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Models;

class Model extends \Phalcon\Mvc\Model
{

    public function initialize()
    {
        $this->setup([
            'exceptionOnFailedSave' => true,
            'notNullValidations' => false,
        ]);

        $this->useDynamicUpdate(true);
    }

}
