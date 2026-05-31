<?php
/**
 * @copyright Copyright (c) 2026 娣卞湷甯傛枃鑱旇蒋浠舵湁闄愬叕鍙?
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

use Phinx\Migration\AbstractMigration;

final class V20260421163000 extends AbstractMigration
{

    public function up()
    {
        $this->alterCertificateUserTable();
    }

    protected function alterCertificateUserTable()
    {
        $table = $this->table('kg_certificate_user');

        if ($table->hasIndexByName('cert_user')) {
            $table->removeIndexByName('cert_user')->save();
        }

        $table->addIndex(['cert_id', 'user_id'], [
            'name' => 'cert_user',
            'unique' => true,
        ])->save();
    }

}
