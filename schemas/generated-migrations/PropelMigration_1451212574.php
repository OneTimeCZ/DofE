<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1451212574.
 * Generated on 2015-12-27 11:36:14 
 */
class PropelMigration_1451212574
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

ALTER TABLE `articles`

  ADD `id_category` INTEGER NOT NULL AFTER `id_image`,

  ADD `url` VARCHAR(120) NOT NULL AFTER `title`;

CREATE INDEX `articles_fi_5d0623` ON `articles` (`id_image`);

CREATE INDEX `articles_fi_e17637` ON `articles` (`id_category`);

ALTER TABLE `articles` ADD CONSTRAINT `articles_fk_5d0623`
    FOREIGN KEY (`id_image`)
    REFERENCES `images` (`id`);

ALTER TABLE `articles` ADD CONSTRAINT `articles_fk_e17637`
    FOREIGN KEY (`id_category`)
    REFERENCES `categories` (`id`);

CREATE TABLE `images`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `description` VARCHAR(150),
    `path` VARCHAR(150),
    PRIMARY KEY (`id`)
) ENGINE=InnoDB;

CREATE TABLE `comments`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `id_user` INTEGER NOT NULL,
    `id_article` INTEGER NOT NULL,
    `content` VARCHAR(500) NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `comments_fi_79a2df` (`id_user`),
    INDEX `comments_fi_764d13` (`id_article`),
    CONSTRAINT `comments_fk_79a2df`
        FOREIGN KEY (`id_user`)
        REFERENCES `users` (`id`),
    CONSTRAINT `comments_fk_764d13`
        FOREIGN KEY (`id_article`)
        REFERENCES `articles` (`id`)
) ENGINE=InnoDB;

CREATE TABLE `categories`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(50) NOT NULL,
    `color` VARCHAR(10),
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

DROP TABLE IF EXISTS `images`;

DROP TABLE IF EXISTS `comments`;

DROP TABLE IF EXISTS `categories`;

ALTER TABLE `articles` DROP FOREIGN KEY `articles_fk_5d0623`;

ALTER TABLE `articles` DROP FOREIGN KEY `articles_fk_e17637`;

DROP INDEX `articles_fi_5d0623` ON `articles`;

DROP INDEX `articles_fi_e17637` ON `articles`;

ALTER TABLE `articles`

  DROP `id_category`,

  DROP `url`;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
',
);
    }

}