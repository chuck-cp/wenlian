<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Providers;

use Phalcon\Config as PhConfig;
use Phalcon\Queue\Beanstalk\Extended as PhBeanstalk;

class Beanstalk extends Provider
{

    protected $serviceName = 'beanstalk';

    public function register()
    {
        /**
         * @var PhConfig $config
         */
        $config = $this->di->getShared('config');

        $this->di->setShared($this->serviceName, function () use ($config) {

            return new PhBeanstalk([
                'host' => $config->path('beanstalk.host', 'beanstalk'),
                'port' => $config->path('beanstalk.port', 11300),
                'prefix' => $config->path('beanstalk.prefix', null),
            ]);
        });
    }

}
