<?php

use yii\db\Migration;

class m250617_080703_update_odd_and_bet_odd_type_relations_fields extends Migration
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

        ALTER TABLE `odd` 
        DROP FOREIGN KEY `fk_odd_odd_bet_type1`;

        ALTER TABLE `odd` 
        ADD COLUMN `odd_bet_type_id` INT(11) NOT NULL AFTER `sport_match_id`,
        ADD INDEX `fk_odd_odd_bet_type1_idx` (`odd_bet_type_id` ASC),
        DROP INDEX `fk_odd_odd_bet_type1_idx`;

        ALTER TABLE `odd` 
        ADD CONSTRAINT `fk_odd_odd_bet_type1`
        FOREIGN KEY (`odd_bet_type_id`)
        REFERENCES `odd_bet_type` (`id`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION;

        ALTER TABLE `odd`
        DROP COLUMN `odd_bet_type_id_vendor`;

        ALTER TABLE `odd_bet_type` 
        DROP INDEX `id_vendor_UNIQUE`;

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
        echo "m250617_080703_update_odd_and_bet_odd_type_relations_fields cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250617_080703_update_odd_and_bet_odd_type_relations_fields cannot be reverted.\n";

        return false;
    }
    */
}
