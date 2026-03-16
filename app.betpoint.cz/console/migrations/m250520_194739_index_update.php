<?php

use yii\db\Migration;

class m250520_194739_index_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
		$query = "

          SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
          SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
          SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

          ALTER TABLE `odd` 
          DROP FOREIGN KEY `fk_odd_sport_match1`;

          ALTER TABLE `odd` 
          DROP COLUMN `sport_match_id`,
          ADD COLUMN `sport_match_id` INT(11) NOT NULL AFTER `odd_bet_type_id_vendor`,
          ADD INDEX `fk_odd_sport_match1_idx` (`sport_match_id` ASC),
          DROP INDEX `fk_odd_sport_match1_idx` ;
          ;

          SET SQL_MODE=@OLD_SQL_MODE;
          SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
          SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

      ";

      $this->db->createCommand($query)->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        echo "m250520_194739_index_update cannot be reverted.\n";

        return false;
    }
  
}
