<?php

use yii\db\Migration;

class m250527_093550_category_active_session extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $query = "
 
        SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
        SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
        SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

        ALTER TABLE `category` 
        ADD COLUMN `active_session` INT(11) NOT NULL DEFAULT 0 AFTER `enabled`;

        SET SQL_MODE=@OLD_SQL_MODE;
        SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
        SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

      ";

        $this->db->createCommand($query)->execute();
    }


    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250527_093550_category_active_session cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250527_093550_category_active_session cannot be reverted.\n";

        return false;
    }
    */
}
