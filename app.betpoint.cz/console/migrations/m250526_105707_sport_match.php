<?php

use yii\db\Migration;

class m250526_105707_sport_match extends Migration
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

        ALTER TABLE `sport_match` 
        DROP FOREIGN KEY `fk_sport_match_sport1`;

        ALTER TABLE `category` 
        DROP COLUMN `season`,
        ADD COLUMN `id_vendor` INT(11) NOT NULL AFTER `id`,
        ADD COLUMN `enabled` INT(11) NOT NULL DEFAULT 0 AFTER `logo_url`,
        ADD UNIQUE INDEX `id_vendor_UNIQUE` (`id_vendor` ASC);

                ALTER TABLE `sport_match` 
        ADD COLUMN `sport_id` INT(11) NOT NULL AFTER `category_id`,
        ADD COLUMN `season_id` INT(11) NOT NULL AFTER `sport_id`,
        CHANGE COLUMN `extra` `extra` INT(11) NOT NULL AFTER `detail`,
        DROP INDEX `fk_sport_match_sport1_idx`,
        ADD INDEX `fk_sport_match_sport1_idx` (`sport_id` ASC),
        ADD INDEX `fk_sport_match_season1_idx` (`season_id` ASC);

        ALTER TABLE `sport_match` 
        ADD CONSTRAINT `fk_sport_match_sport1`
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
        echo "m250526_105707_sport_match cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250526_105707_sport_match cannot be reverted.\n";

        return false;
    }
    */
}
