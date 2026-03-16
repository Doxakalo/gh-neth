<?php

use yii\db\Migration;

class m250612_124802_sport_match_vendor_id extends Migration
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
        ADD COLUMN `id_vendor` INT(11) NOT NULL AFTER `id`;

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
        echo "m250612_124802_sport_match_vendor_id cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250612_124802_sport_match_vendor_id cannot be reverted.\n";

        return false;
    }
    */
}
