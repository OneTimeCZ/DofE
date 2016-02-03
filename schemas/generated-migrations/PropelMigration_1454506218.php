<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1454506218.
 * Generated on 2016-02-03 14:30:18 
 */
class PropelMigration_1454506218
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

ALTER TABLE `activities`

  ADD `created_at` DATETIME AFTER `goal`,

  ADD `updated_at` DATETIME AFTER `created_at`;

ALTER TABLE `activity_logs`

  ADD `created_at` DATETIME AFTER `id_activity`,

  ADD `updated_at` DATETIME AFTER `created_at`;

ALTER TABLE `images`

  CHANGE `title` `title` VARCHAR(50) NOT NULL;

ALTER TABLE `quotes`

  ADD `created_at` DATETIME AFTER `content`,

  ADD `updated_at` DATETIME AFTER `created_at`;

ALTER TABLE `ratings`

  ADD `created_at` DATETIME AFTER `type`,

  ADD `updated_at` DATETIME AFTER `created_at`;

CREATE TABLE `user_reports`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `id_user` INTEGER NOT NULL,
    `reason` VARCHAR(500) NOT NULL,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`),
    INDEX `user_reports_fi_79a2df` (`id_user`),
    CONSTRAINT `user_reports_fk_79a2df`
        FOREIGN KEY (`id_user`)
        REFERENCES `users` (`id`)
) ENGINE=InnoDB;

CREATE TABLE `bug_reports`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `id_user` INTEGER NOT NULL,
    `location` VARCHAR(200) NOT NULL,
    `description` VARCHAR(1000) NOT NULL,
    `severity` INTEGER NOT NULL,
    `device` VARCHAR(200),
    `browser` VARCHAR(100),
    `fixed_at` DATETIME,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`),
    INDEX `bug_reports_fi_79a2df` (`id_user`),
    CONSTRAINT `bug_reports_fk_79a2df`
        FOREIGN KEY (`id_user`)
        REFERENCES `users` (`id`)
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

DROP TABLE IF EXISTS `user_reports`;

DROP TABLE IF EXISTS `bug_reports`;

ALTER TABLE `activities`

  DROP `created_at`,

  DROP `updated_at`;

ALTER TABLE `activity_logs`

  DROP `created_at`,

  DROP `updated_at`;

ALTER TABLE `images`

  CHANGE `title` `title` VARCHAR(50);

ALTER TABLE `quotes`

  DROP `created_at`,

  DROP `updated_at`;

ALTER TABLE `ratings`

  DROP `created_at`,

  DROP `updated_at`;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
',
);
    }

}