<?php

use yii\db\Migration;

class m250526_112255_season_table extends Migration
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

        CREATE TABLE IF NOT EXISTS `season` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `year` INT(11) NOT NULL,
        `current` INT(11) NOT NULL DEFAULT 0,
        `odds` INT(11) NOT NULL DEFAULT 0,
        `enabled` INT(11) NOT NULL DEFAULT 0,
        `category_id` INT(11) NOT NULL,
        `created_at` INT(11) NOT NULL,
        `updated_at` INT(11) NOT NULL,
        PRIMARY KEY (`id`),
        INDEX `fk_season_category1_idx` (`category_id` ASC),
        CONSTRAINT `fk_season_category1`
            FOREIGN KEY (`category_id`)
            REFERENCES `category` (`id`)
            ON DELETE NO ACTION
            ON UPDATE NO ACTION)
        ENGINE = InnoDB
        DEFAULT CHARACTER SET = utf8mb4
        COLLATE = utf8mb4_unicode_ci;

        ALTER TABLE `sport_match`
        ADD CONSTRAINT `fk_sport_match_season1`
        FOREIGN KEY (`season_id`)
        REFERENCES `season` (`id`)
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
        echo "m250526_112255_season_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250526_112255_season_table cannot be reverted.\n";

        return false;
    }
    */
}
