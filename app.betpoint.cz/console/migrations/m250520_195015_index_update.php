<?php

use yii\db\Migration;

class m250520_195015_index_update extends Migration
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
          ADD CONSTRAINT `fk_odd_sport_match1`
          FOREIGN KEY (`sport_match_id`)
          REFERENCES `sport_match` (`id`);

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
        echo "m250520_195015_index_update cannot be reverted.\n";

        return false;
    }

}
