<?php

use yii\db\Migration;

class m250711_063104_odd_bet_type_add_column_alias extends Migration
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

            ALTER TABLE `odd_bet_type` 
            ADD COLUMN `alias` VARCHAR(100) NULL DEFAULT NULL AFTER `name`;

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
        echo "m250711_063104_odd_bet_type_add_column_alias cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250711_063104_odd_bet_type_add_column_alias cannot be reverted.\n";

        return false;
    }
    */
}
