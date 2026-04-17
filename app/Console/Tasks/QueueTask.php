<?php
/**
 * @copyright Copyright (c) 2023 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Console\Tasks;

use App\Console\Queues\Main as MainQueue;
use App\Console\Queues\Notice as NoticeQueue;
use App\Repos\Task as TaskRepo;
use Phalcon\Queue\Beanstalk\Job as BeanstalkJob;

class QueueTask extends Task
{

    /**
     * 启动main消费队列
     *
     * @command php console.php queue main_worker
     */
    public function mainWorkerAction()
    {
        $tube = 'main';

        echo "------{$tube} worker start ------" . PHP_EOL;

        $beanstalk = $this->getBeanstalk();

        $logger = $this->getLogger('queue');

        $config = $this->getConfig();

        while (true) {
            $job = $beanstalk->reserveFromTube($tube, 15);
            if ($job instanceof BeanstalkJob) {
                $taskId = $job->getBody();
                if ($config->get('env') == ENV_DEV) {
                    $logger->debug("tube:{$tube}, task:{$taskId} handling");
                }
                try {
                    $manager = new MainQueue();
                    $manager->handle($taskId);
                    $job->delete();
                } catch (\Throwable $e) {
                    $logger->error("tube:{$tube}, task:{$taskId} Exception: " . kg_json_encode([
                            'file' => $e->getFile(),
                            'line' => $e->getLine(),
                            'message' => $e->getMessage(),
                        ]));
                }
            } else {
                $this->keepDbConnection(10);
            }
        }
    }

    /**
     * 启动notice消费队列
     *
     * @command php console.php queue notice_worker
     */
    public function noticeWorkerAction()
    {
        $tube = 'notice';

        echo "------{$tube} worker start ------" . PHP_EOL;

        $beanstalk = $this->getBeanstalk();

        $logger = $this->getLogger('queue');

        $config = $this->getConfig();

        while (true) {
            $job = $beanstalk->reserveFromTube($tube, 5);
            if ($job instanceof BeanstalkJob) {
                $taskId = $job->getBody();
                if ($config->get('env') == ENV_DEV) {
                    $logger->debug("tube:{$tube}, task:{$taskId} handling");
                }
                try {
                    $manager = new NoticeQueue();
                    $manager->handle($taskId);
                    $job->delete();
                } catch (\Throwable $e) {
                    $logger->error("tube:{$tube}, task:{$taskId} Exception: " . kg_json_encode([
                            'file' => $e->getFile(),
                            'line' => $e->getLine(),
                            'message' => $e->getMessage(),
                        ]));
                }
            } else {
                $this->keepDbConnection(30);
            }
        }
    }

    /**
     * 查看队列整体状态
     *
     * @command php console.php queue stats
     */
    public function statsAction()
    {
        $beanstalk = $this->getBeanstalk();

        $items = $beanstalk->stats();

        echo "------ beanstalk stats ------" . PHP_EOL;

        foreach ($items as $key => $value) {
            echo "{$key}: {$value}" . PHP_EOL;
        }
    }

    /**
     * 查看tubes列表
     *
     * @command php console.php queue tubes
     */
    public function tubesAction()
    {
        $beanstalk = $this->getBeanstalk();

        $tubes = $beanstalk->getTubes();

        echo "------ tube list ------" . PHP_EOL;

        foreach ($tubes as $key => $value) {
            echo "{$key}: {$value}" . PHP_EOL;
        }
    }

    /**
     * 查看特定tube状态
     *
     * @command php console.php queue tube_stats {tube}
     */
    public function tubeStatsAction($params)
    {
        $tube = $params[0] ?? null;

        if (!$tube) {
            exit('please special a tube' . PHP_EOL);
        }

        echo "------ tube:{$tube} stats ------" . PHP_EOL;

        $beanstalk = $this->getBeanstalk();

        $items = $beanstalk->getTubeStats($tube);

        foreach ($items as $key => $value) {
            echo "{$key}: {$value}" . PHP_EOL;
        }
    }

    /**
     * 保持数据库链接
     * @param int $max
     * @return void
     */
    protected function keepDbConnection($max = 10)
    {
        $max = max($max, 10);

        $rand = rand(1, $max);

        if ($rand == 1) {
            $taskRepo = new TaskRepo();
            $taskRepo->findById(1);
        }
    }

}
