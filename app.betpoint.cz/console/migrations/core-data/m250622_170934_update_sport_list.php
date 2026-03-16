<?php

use yii\db\Migration;

class m250622_170934_update_sport_list extends Migration
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

        DELETE FROM `sport` WHERE `sport`.`id` = 5;

        INSERT INTO `sport` (`id`, `name`, `alias`, `created_at`, `updated_at`) VALUES (NULL, 'Handball', 'handball', '1750277706', '1750277706');
        INSERT INTO `sport` (`id`, `name`, `alias`, `created_at`, `updated_at`) VALUES (NULL, 'Volleyball', 'volleyball', '1750277706', '1750277706');

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
        echo "m250622_170934_update_sport_list cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250622_170934_update_sport_list cannot be reverted.\n";

        return false;
    }
    */
}
