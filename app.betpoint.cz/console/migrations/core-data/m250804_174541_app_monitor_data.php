<?php

use common\models\AppMonitor;
use yii\db\Migration;

class m250804_174541_app_monitor_data extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $time = time();

        $this->batchInsert('app_monitor', ['id', 'alias', 'priority', 'created_at', 'updated_at'], [
            [null, 'SYNC_CATEGORIES', AppMonitor::PRIORITY_MEDIUM, $time, $time],
            [null, 'ENABLE_DEFAULT_CATEGORIES', AppMonitor::PRIORITY_LOW, $time, $time],
            [null, 'SYNC_ODD_BET_TYPE', AppMonitor::PRIORITY_LOW, $time, $time],
            [null, 'ENABLE_AND_CONFIGURE_ODD_BET_TYPES', AppMonitor::PRIORITY_LOW, $time, $time],
            [null, 'SYNC_MATCHES', AppMonitor::PRIORITY_HIGH, $time, $time],
            [null, 'SYNC_LAST_MATCHES', AppMonitor::PRIORITY_HIGH, $time, $time],
            [null, 'SYNC_ODDS', AppMonitor::PRIORITY_HIGH, $time, $time],
            [null, 'BETS_EVALUATE', AppMonitor::PRIORITY_HIGH, $time, $time],
            [null, 'DELETE_OLD_DATA', AppMonitor::PRIORITY_LOW, $time, $time],
            [null, 'DELETE_OLD_LOGS', AppMonitor::PRIORITY_LOW, $time, $time],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250804_174541_app_monitor_data cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250804_174541_app_monitor_data cannot be reverted.\n";

        return false;
    }
    */
}
