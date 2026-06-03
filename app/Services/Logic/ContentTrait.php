<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace App\Services\Logic;

use Phalcon\Text;

trait ContentTrait
{

    public function handleContent($content)
    {
        $content = $this->handleCosImageStyle($content);

        return $this->handleOutLink($content);
    }

    protected function handleCosImageStyle($content)
    {
        $style = '';

        $pattern = '/src="(.*?)\/img\/content\/(.*?)"/';

        $replacement = 'src="$1/img/content/$2' . $style . '"';

        return preg_replace($pattern, $replacement, $content);
    }

    protected function handleOutLink($content)
    {
        $site = kg_setting('site');

        return preg_replace_callback('/href="(.*?)"/', function ($matches) use ($site) {
            $siteHost = parse_url($site['url'], PHP_URL_HOST);
            if (Text::startsWith($matches[1], 'http')) {
                $outHost = parse_url($matches[1], PHP_URL_HOST);
                if ($siteHost != $outHost) {
                    return sprintf('href="/link?url=%s" target="_blank"', urlencode($matches[1]));
                }
            }
            return sprintf('href="%s"', $matches[1]);
        }, $content);
    }

}
