<?php

use yii\db\Migration;

class m250623_114021_user_bet_match_result_id extends Migration
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

        ALTER TABLE `user_bet` 
        DROP FOREIGN KEY `fk_user_bet_match_result1`;

        ALTER TABLE `user_bet` 
        CHANGE COLUMN `match_result_id` `match_result_id` INT(11) NULL DEFAULT NULL ;

        ALTER TABLE `user_bet` 
        ADD CONSTRAINT `fk_user_bet_match_result1`
        FOREIGN KEY (`match_result_id`)
        REFERENCES `sport_match_result` (`id`);

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
        echo "m250623_114021_user_bet_match_result_id cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250623_114021_user_bet_match_result_id cannot be reverted.\n";

        return false;
    }
    */
}
