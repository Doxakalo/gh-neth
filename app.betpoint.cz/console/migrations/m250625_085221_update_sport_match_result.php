<?php

use yii\db\Migration;

class m250625_085221_update_sport_match_result extends Migration
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

        ALTER TABLE `sport_match_result` 
        DROP FOREIGN KEY `fk_match_result_user1`;

        ALTER TABLE `sport_match_result` 
        ADD COLUMN `result` JSON NOT NULL AFTER `result_vendor`,
        ADD COLUMN `evaluated` INT(11) NULL DEFAULT 0 AFTER `result`,
        CHANGE COLUMN `result_json` `result_vendor` JSON NULL ,
        CHANGE COLUMN `user_id` `user_id` INT(11) NULL DEFAULT NULL ;

        ALTER TABLE `sport_match_result` 
        ADD CONSTRAINT `fk_match_result_user1`
        FOREIGN KEY (`user_id`)
        REFERENCES `user` (`id`);

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
        echo "m250625_085221_update_sport_match_result cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250625_085221_update_sport_match_result cannot be reverted.\n";

        return false;
    }
    */
}
