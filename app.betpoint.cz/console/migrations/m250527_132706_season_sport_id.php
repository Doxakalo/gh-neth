<?php

use yii\db\Migration;

class m250527_132706_season_sport_id extends Migration
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

        ALTER TABLE `season` 
        ADD COLUMN `sport_id` INT(11) NOT NULL AFTER `category_id`,
        ADD INDEX `fk_season_sport1_idx` (`sport_id` ASC);

        ALTER TABLE `season` 
        ADD CONSTRAINT `fk_season_sport1`
        FOREIGN KEY (`sport_id`)
        REFERENCES `sport` (`id`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION;

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
        echo "m250527_132706_season_sport_id cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250527_132706_season_sport_id cannot be reverted.\n";

        return false;
    }
    */
}
