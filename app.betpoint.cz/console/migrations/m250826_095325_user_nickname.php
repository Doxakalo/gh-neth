<?php

use yii\db\Migration;

class m250826_095325_user_nickname extends Migration
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

        ALTER TABLE `user` 
        ADD COLUMN `nickname` VARCHAR(128) NOT NULL AFTER `last_name`,
        CHANGE COLUMN `first_name` `first_name` VARCHAR(128) NULL DEFAULT NULL ,
        CHANGE COLUMN `last_name` `last_name` VARCHAR(128) NULL DEFAULT NULL ;

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
        echo "m250826_095325_user_nickname cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250826_095325_user_nickname cannot be reverted.\n";

        return false;
    }
    */
}
