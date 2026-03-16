<?php

use yii\db\Migration;

class m250520_143319_user extends Migration
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

            ALTER TABLE `user` 
            COLLATE = 'utf8mb4_unicode_ci' ,
            DROP COLUMN `username`,
            ADD COLUMN `first_name` VARCHAR(128) COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `id`,
            ADD COLUMN `last_name` VARCHAR(128) NOT NULL AFTER `first_name`,
            CHANGE COLUMN `verification_token` `verification_token` VARCHAR(255) COLLATE 'utf8mb4_unicode_ci' NULL DEFAULT NULL AFTER `password_reset_token`,
            DROP INDEX `username` ;
            ;

            ALTER TABLE `user` 
            CHANGE `username` `username` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL, 
            CHANGE `auth_key` `auth_key` VARCHAR(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL, 
            CHANGE `password_hash` `password_hash` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL, 
            CHANGE `password_reset_token` `password_reset_token` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, 
            CHANGE `email` `email` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL, 
            CHANGE `verification_token` `verification_token` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;

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
        echo "m250520_143319_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250520_143319_user cannot be reverted.\n";

        return false;
    }
    */
}
