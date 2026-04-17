<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Console\Tasks;

use App\Caches\AppInfo as AppInfoCache;
use App\Caches\NavTreeList as NavTreeListCache;
use App\Caches\Setting as SettingCache;
use App\Models\MigrationTask as MigrationTaskModel;
use App\Models\Setting as SettingModel;
use App\Services\Utils\OpCache as OpCacheUtil;
use GuzzleHttp\Client as HttpClient;
use Phalcon\Mvc\Model\Resultset;
use Phalcon\Mvc\Model\ResultsetInterface;
use Phalcon\Text;

class UpgradeTask extends Task
{

    public function mainAction()
    {
        $this->migrateAction();
        $this->resetAppInfoAction();
        $this->resetSettingAction();
        $this->resetAnnotationAction();
        $this->resetMetadataAction();
        $this->resetVoltAction();
        $this->resetNavAction();
        $this->resetOpcacheAction();
    }

    /**
     * 执行迁移
     *
     * @command: php console.php upgrade migrate
     */
    public function migrateAction()
    {
        $tasks = $this->findMigrationTasks();

        $versionList = [];

        if ($tasks->count() > 0) {
            $versionList = kg_array_column($tasks->toArray(), 'version');
        }

        $files = scandir(app_path('Console/Migrations'));

        foreach ($files as $file) {

            if (preg_match('/^V[0-9]+\.php$/', $file)) {

                $version = substr($file, 0, -4);

                if (!in_array($version, $versionList)) {

                    $startTime = time();

                    $className = "\App\Console\Migrations\\{$version}";
                    $obj = new $className();
                    $obj->run();

                    $endTime = time();

                    $task = new MigrationTaskModel();
                    $task->version = $version;
                    $task->start_time = $startTime;
                    $task->end_time = $endTime;
                    $task->create();

                    echo "------ console migration {$version} ok ------" . PHP_EOL;
                }
            }
        }
    }

    /**
     * 重置应用信息
     *
     * @command: php console.php upgrade reset_app_info
     */
    public function resetAppInfoAction()
    {
        $cache = new AppInfoCache();

        $cache->rebuild();

        echo '------ reset app info ok ------' . PHP_EOL;
    }

    /**
     * 重置系统设置
     *
     * @command: php console.php upgrade reset_setting
     */
    public function resetSettingAction()
    {
        $rows = SettingModel::query()->columns('section')->distinct(true)->execute();

        foreach ($rows as $row) {
            $cache = new SettingCache();
            $cache->rebuild($row->section);
        }

        echo '------ reset setting ok ------' . PHP_EOL;
    }

    /**
     * 重置注解
     *
     * @command: php console.php upgrade reset_annotation
     */
    public function resetAnnotationAction()
    {
        $redis = $this->getRedis();

        $statsKey = '_ANNOTATION_';

        $keys = $redis->sMembers($statsKey);

        if (count($keys) > 0) {
            $keys = $this->handlePhKeys($keys);
            $redis->del(...$keys);
            $redis->del($statsKey);
        }

        echo '------ reset annotation ok ------' . PHP_EOL;
    }

    /**
     * 重置元数据
     *
     * @command: php console.php upgrade reset_metadata
     */
    public function resetMetadataAction()
    {
        $redis = $this->getRedis();

        $statsKey = '_METADATA_';

        $keys = $redis->sMembers($statsKey);

        if (count($keys) > 0) {
            $keys = $this->handlePhKeys($keys);
            $redis->del(...$keys);
            $redis->del($statsKey);
        }

        echo "------ reset metadata ok ------" . PHP_EOL;
    }

    /**
     * 重置模板
     *
     * @command: php console.php upgrade reset_volt
     */
    public function resetVoltAction()
    {
        $dir = cache_path('volt');

        foreach (scandir($dir) as $file) {
            if (strpos($file, '.php')) {
                unlink($dir . '/' . $file);
            }
        }

        echo '------ reset volt ok ------' . PHP_EOL;
    }

    /**
     * 重置导航
     *
     * @command: php console.php upgrade reset_nav
     */
    public function resetNavAction()
    {
        $cache = new NavTreeListCache();

        $cache->delete();

        echo '------ reset navigation ok ------' . PHP_EOL;
    }

    /**
     * 重置opcache
     *
     * @command: php console.php upgrade reset_opcache
     */
    public function resetOpcacheAction($params = [])
    {
        $scope = $params[0] ?? 'all';

        $service = new OpCacheUtil();

        $service->reset($scope);

        /**
         * fpm的缓存cli模式下无法刷新，通过http调用执行刷新
         */
        $this->resetFpmOpcache($scope);

        echo '------ reset opcache ok ------' . PHP_EOL;
    }

    protected function resetFpmOpcache($scope)
    {
        $auth = $this->crypt->encrypt($scope);

        $url = sprintf('%s/admin/opcache', kg_setting('site', 'url'));

        if (!Text::startsWith($url, 'http')) return;

        $params = [
            'scope' => $scope,
            'auth' => $auth,
        ];

        $client = new HttpClient();

        $client->request('POST', $url, [
            'form_params' => $params,
            'http_errors' => false,
        ]);
    }

    /**
     * @return ResultsetInterface|Resultset|MigrationTaskModel[]
     */
    protected function findMigrationTasks()
    {
        return MigrationTaskModel::query()->execute();
    }

    protected function handlePhKeys($keys)
    {
        return array_map(function ($key) {
            return "_PHCR{$key}";
        }, $keys);
    }

}
