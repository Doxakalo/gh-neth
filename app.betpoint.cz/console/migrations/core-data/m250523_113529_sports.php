<?php

use yii\db\Migration;

class m250523_113529_sports extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $time = time();

        $this->batchInsert('sport', ['id', 'name', 'alias','created_at', 'updated_at'], [
			[null, 'Football', 'football',$time, $time],
			[null, 'Hockey','hockey',$time, $time],
			[null, 'Baseball','baseball',$time, $time],
            [null, 'Basketball', 'basketball', $time, $time],
			[null, 'MMA','mma',$time, $time],
			[null, 'Rugby','rugby',$time, $time],
			[null, 'NFL','nfl',$time, $time],
		]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250523_113529_sports cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250523_113529_sports cannot be reverted.\n";

        return false;
    }
    */
}
