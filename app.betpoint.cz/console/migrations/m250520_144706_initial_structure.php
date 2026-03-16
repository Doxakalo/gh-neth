<?php

use yii\db\Migration;

class m250520_144706_initial_structure extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$query = "

        -- MySQL Workbench Forward Engineering

        SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
        SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
        SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

        -- -----------------------------------------------------
        -- Schema sports_betting_college
        -- -----------------------------------------------------


        -- -----------------------------------------------------
        -- Table `sport`
        -- -----------------------------------------------------
        CREATE TABLE IF NOT EXISTS `sport` (
          `id` INT NOT NULL AUTO_INCREMENT,
          `name` VARCHAR(128) NOT NULL,
          `alias` VARCHAR(128) NOT NULL,
          `created_at` INT NOT NULL,
          `updated_at` INT NOT NULL,
          PRIMARY KEY (`id`),
          UNIQUE INDEX `name_UNIQUE` (`name` ASC),
          UNIQUE INDEX `alias_UNIQUE` (`alias` ASC))
        ENGINE = InnoDB;


        -- -----------------------------------------------------
        -- Table `category`
        -- -----------------------------------------------------
        CREATE TABLE IF NOT EXISTS `category` (
          `id` INT NOT NULL AUTO_INCREMENT,
          `name` VARCHAR(150) NOT NULL,
          `country_name` VARCHAR(50) NOT NULL,
          `season` INT NOT NULL,
          `logo_url` VARCHAR(255) NULL,
          `sport_id` INT NOT NULL,
          `created_at` INT NOT NULL,
          `updated_at` INT NOT NULL,
          PRIMARY KEY (`id`),
          INDEX `fk_category_sport_idx` (`sport_id` ASC),
          CONSTRAINT `fk_category_sport`
            FOREIGN KEY (`sport_id`)
            REFERENCES `sport` (`id`))
        ENGINE = InnoDB;


        -- -----------------------------------------------------
        -- Table `sport_match`
        -- -----------------------------------------------------
        CREATE TABLE IF NOT EXISTS `sport_match` (
          `id` INT NOT NULL AUTO_INCREMENT,
          `name` VARCHAR(255) NOT NULL,
          `match_start` INT NULL,
          `home` VARCHAR(255) NOT NULL,
          `away` VARCHAR(255) NOT NULL,
          `evaluated` INT NOT NULL DEFAULT 0,
          `status` VARCHAR(5) NOT NULL,
          `status_name` VARCHAR(100) NOT NULL,
          `detail` JSON NOT NULL,
          `category_id` INT NOT NULL,
          `extra` INT NOT NULL,
          `created_at` INT NOT NULL,
          `updated_at` INT NOT NULL,
          PRIMARY KEY (`id`),
          INDEX `fk_sport_match_category1_idx` (`category_id` ASC),
          INDEX `fk_sport_match_sport1_idx` (`extra` ASC),
          CONSTRAINT `fk_sport_match_category1`
            FOREIGN KEY (`category_id`)
            REFERENCES `category` (`id`),
          CONSTRAINT `fk_sport_match_sport1`
            FOREIGN KEY (`extra`)
            REFERENCES `sport` (`id`))
        ENGINE = InnoDB;


        -- -----------------------------------------------------
        -- Table `sport_match_result`
        -- -----------------------------------------------------
        CREATE TABLE IF NOT EXISTS `sport_match_result` (
          `id` INT NOT NULL AUTO_INCREMENT,
          `result_json` JSON NOT NULL,
          `sport_match_id` INT NOT NULL,
          `user_id` INT NOT NULL,
          `created_at` INT NOT NULL,
          `updated_at` INT NOT NULL,
          PRIMARY KEY (`id`),
          INDEX `fk_sport_match_result_sport_match1_idx` (`sport_match_id` ASC),
          INDEX `fk_sport_match_result_user1_idx` (`user_id` ASC),
          CONSTRAINT `fk_match_result_match1`
            FOREIGN KEY (`sport_match_id`)
            REFERENCES `sport_match` (`id`),
          CONSTRAINT `fk_match_result_user1`
            FOREIGN KEY (`user_id`)
            REFERENCES `user` (`id`))
        ENGINE = InnoDB;


        -- -----------------------------------------------------
        -- Table `odd_bet_type`
        -- -----------------------------------------------------
        CREATE TABLE IF NOT EXISTS `odd_bet_type` (
          `id` INT NOT NULL AUTO_INCREMENT,
          `id_vendor` INT NOT NULL,
          `name` VARCHAR(100) NOT NULL,
          `enabled` INT NOT NULL DEFAULT 0,
          `sport_id` INT NOT NULL,
          `created_at` INT NOT NULL,
          `updated_at` INT NOT NULL,
          PRIMARY KEY (`id`),
          UNIQUE INDEX `id_vendor_UNIQUE` (`id_vendor` ASC),
          INDEX `fk_odd_bet_type_sport1_idx` (`sport_id` ASC),
          CONSTRAINT `fk_odd_bet_type_sport1`
            FOREIGN KEY (`sport_id`)
            REFERENCES `sport` (`id`))
        ENGINE = InnoDB;


        -- -----------------------------------------------------
        -- Table `odd`
        -- -----------------------------------------------------
        CREATE TABLE IF NOT EXISTS `odd` (
          `id` INT NOT NULL AUTO_INCREMENT,
          `name` VARCHAR(255) NOT NULL,
          `odd_raw` FLOAT NOT NULL,
          `odd` FLOAT NOT NULL,
          `odd_bet_type_id_vendor` INT NOT NULL,
          `sport_match_id` INT NOT NULL,
          `updated_at` INT NOT NULL,
          `created_at` INT NOT NULL,
          PRIMARY KEY (`id`),
          INDEX `fk_odd_odd_bet_type1_idx` (`odd_bet_type_id_vendor` ASC),
          INDEX `fk_odd_sport_match1_idx` (`sport_match_id` ASC),
          CONSTRAINT `fk_odd_sport_match1`
            FOREIGN KEY (`sport_match_id`)
            REFERENCES `sport_match` (`id`),
          CONSTRAINT `fk_odd_odd_bet_type1`
            FOREIGN KEY (`odd_bet_type_id_vendor`)
            REFERENCES `odd_bet_type` (`id_vendor`))
        ENGINE = InnoDB;


        -- -----------------------------------------------------
        -- Table `user_bet`
        -- -----------------------------------------------------
        CREATE TABLE IF NOT EXISTS `user_bet` (
          `id` INT NOT NULL AUTO_INCREMENT,
          `amount` FLOAT NOT NULL,
          `odd` FLOAT NOT NULL,
          `evaluated` INT NOT NULL DEFAULT 0,
          `odd_id` INT NOT NULL,
          `match_result_id` INT NOT NULL,
          `user_id` INT NOT NULL,
          `created_at` INT NOT NULL,
          `updated_at` INT NOT NULL,
          PRIMARY KEY (`id`),
          INDEX `fk_user_bet_odd1_idx` (`odd_id` ASC),
          INDEX `fk_user_bet_match_result1_idx` (`match_result_id` ASC),
          INDEX `fk_user_bet_user1_idx` (`user_id` ASC),
          CONSTRAINT `fk_user_bet_match_result1`
            FOREIGN KEY (`match_result_id`)
            REFERENCES `sport_match_result` (`id`),
          CONSTRAINT `fk_user_bet_odd1`
            FOREIGN KEY (`odd_id`)
            REFERENCES `odd` (`id`),
          CONSTRAINT `fk_user_bet_user1`
            FOREIGN KEY (`user_id`)
            REFERENCES `user` (`id`))
        ENGINE = InnoDB;


        -- -----------------------------------------------------
        -- Table `transaction`
        -- -----------------------------------------------------
        CREATE TABLE IF NOT EXISTS `transaction` (
          `id` INT NOT NULL AUTO_INCREMENT,
          `amount` FLOAT NOT NULL,
          `note` VARCHAR(255) NOT NULL,
          `type` INT NOT NULL,
          `match_result_id` INT NULL,
          `user_bet_id` INT NULL,
          `user_id` INT NOT NULL,
          `created_at` INT NOT NULL,
          PRIMARY KEY (`id`),
          INDEX `fk_transaction_match_result1_idx` (`match_result_id` ASC),
          INDEX `fk_transaction_user_bet1_idx` (`user_bet_id` ASC),
          INDEX `fk_transaction_user1_idx` (`user_id` ASC),
          CONSTRAINT `fk_transaction_match_result1`
            FOREIGN KEY (`match_result_id`)
            REFERENCES `sport_match_result` (`id`),
          CONSTRAINT `fk_transaction_user1`
            FOREIGN KEY (`user_id`)
            REFERENCES `user` (`id`),
          CONSTRAINT `fk_transaction_user_bet1`
            FOREIGN KEY (`user_bet_id`)
            REFERENCES `user_bet` (`id`))
        ENGINE = InnoDB;


        -- -----------------------------------------------------
        -- Table `contact_form`
        -- -----------------------------------------------------
        CREATE TABLE IF NOT EXISTS `contact_form` (
          `id` INT NOT NULL AUTO_INCREMENT,
          `topic` VARCHAR(255) NOT NULL,
          `message` TEXT NOT NULL,
          `user_id` INT NOT NULL,
          `created_at` INT NOT NULL,
          PRIMARY KEY (`id`),
          INDEX `fk_contact_form_user1_idx` (`user_id` ASC),
          CONSTRAINT `fk_contact_form_user1`
            FOREIGN KEY (`user_id`)
            REFERENCES `user` (`id`))
        ENGINE = InnoDB;

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
        echo "m250520_144706_initial_structure cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250520_144706_initial_structure cannot be reverted.\n";

        return false;
    }
    */
}
