<?php

use yii\db\Migration;

class m250804_173942_create_table_app_monitor extends Migration
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

        CREATE TABLE IF NOT EXISTS `app_monitor` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `alias` VARCHAR(45) NOT NULL,
        `priority` INT(11) NOT NULL,
        `created_at` INT(11) NOT NULL,
        `updated_at` INT(11) NOT NULL,
        PRIMARY KEY (`id`))
        ENGINE = InnoDB
        DEFAULT CHARACTER SET = utf8mb4
        COLLATE = utf8mb4_unicode_ci;

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
        echo "m250804_173942_create_table_app_monitor cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250804_173942_create_table_app_monitor cannot be reverted.\n";

        return false;
    }
    */
}
