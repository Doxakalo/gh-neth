<?php

use yii\db\Migration;

class m250925_092215_create_allowed_categories extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $tableOptions = null;

        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%allowed_categories}}', [
            'id'         => $this->primaryKey(),
            'id_vendor'  => $this->integer(11)->notNull(),
            'sport_id'   => $this->integer(11)->notNull(),
            'sport'      => $this->string()->notNull(),
            'country_name'    => $this->string(),
        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250925_092215_create_allowed_categories cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250925_092215_create_allowed_categories cannot be reverted.\n";

        return false;
    }
    */
}
