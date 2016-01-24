<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1453587317.
 * Generated on 2016-01-23 23:15:17 
 */
class PropelMigration_1453587317
{
    public $comment = '';

    public function preUp($manager)
    {
        // add the pre-migration code here
    }

    public function postUp($manager)
    {
        // add the post-migration code here
    }

    public function preDown($manager)
    {
        // add the pre-migration code here
    }

    public function postDown($manager)
    {
        // add the post-migration code here
    }

    /**
     * Get the SQL statements for the Up migration
     *
     * @return array list of the SQL strings to execute for the Up migration
     *               the keys being the datasources
     */
    public function getUpSQL()
    {
        return array (
  'dofe' => '
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

ALTER TABLE `comments`

  ADD `rating` INTEGER AFTER `id_article`;

CREATE TABLE `ratings`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `id_user` INTEGER NOT NULL,
    `id_comment` INTEGER NOT NULL,
    `type` TINYINT(1) NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `ratings_fi_79a2df` (`id_user`),
    INDEX `ratings_fi_24556a` (`id_comment`),
    CONSTRAINT `ratings_fk_79a2df`
        FOREIGN KEY (`id_user`)
        REFERENCES `users` (`id`),
    CONSTRAINT `ratings_fk_24556a`
        FOREIGN KEY (`id_comment`)
        REFERENCES `comments` (`id`)
) ENGINE=InnoDB;

CREATE TABLE `levels`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(50) NOT NULL,
    `short_length` INTEGER,
    `normal_length` INTEGER NOT NULL,
    `long_length` INTEGER NOT NULL,
    `color` VARCHAR(10) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB;

CREATE TABLE `activities`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `id_user` INTEGER NOT NULL,
    `id_level` INTEGER NOT NULL,
    `id_activity_type` INTEGER NOT NULL,
    `name` VARCHAR(100) NOT NULL,
    `description` VARCHAR(250),
    `goal` VARCHAR(250) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB;

CREATE TABLE `activity_types`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(50) NOT NULL,
    `color` VARCHAR(10) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB;

CREATE TABLE `activity_logs`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `id_week` INTEGER NOT NULL,
    `id_activity` INTEGER NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
',
);
    }

    /**
     * Get the SQL statements for the Down migration
     *
     * @return array list of the SQL strings to execute for the Down migration
     *               the keys being the datasources
     */
    public function getDownSQL()
    {
        return array (
  'dofe' => '
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `ratings`;

DROP TABLE IF EXISTS `levels`;

DROP TABLE IF EXISTS `activities`;

DROP TABLE IF EXISTS `activity_types`;

DROP TABLE IF EXISTS `activity_logs`;

ALTER TABLE `comments`

  DROP `rating`;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
',
);
    }

}