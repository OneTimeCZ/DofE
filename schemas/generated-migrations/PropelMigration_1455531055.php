<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1455531055.
 * Generated on 2016-02-15 11:10:55 
 */
class PropelMigration_1455531055
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

ALTER TABLE `activities` DROP FOREIGN KEY `activities_fk_79a2df`;

DROP INDEX `activities_fi_79a2df` ON `activities`;

ALTER TABLE `activities`

  CHANGE `id_user` `id_member` INTEGER NOT NULL;

CREATE INDEX `activities_fi_e49747` ON `activities` (`id_member`);

ALTER TABLE `activities` ADD CONSTRAINT `activities_fk_e49747`
    FOREIGN KEY (`id_member`)
    REFERENCES `members` (`id`);

ALTER TABLE `users`

  ADD `id_member` INTEGER AFTER `id`,

  DROP `name`,

  DROP `surname`;

CREATE INDEX `users_fi_e49747` ON `users` (`id_member`);

ALTER TABLE `users` ADD CONSTRAINT `users_fk_e49747`
    FOREIGN KEY (`id_member`)
    REFERENCES `members` (`id`);

CREATE TABLE `members`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(50) NOT NULL,
    `surname` VARCHAR(50) NOT NULL,
    `from` DATETIME,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB;

CREATE TABLE `membership_applications`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `id_user` INTEGER NOT NULL,
    `name` VARCHAR(50) NOT NULL,
    `surname` VARCHAR(50) NOT NULL,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`),
    INDEX `membership_applications_fi_79a2df` (`id_user`),
    CONSTRAINT `membership_applications_fk_79a2df`
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

DROP TABLE IF EXISTS `members`;

DROP TABLE IF EXISTS `membership_applications`;

ALTER TABLE `activities` DROP FOREIGN KEY `activities_fk_e49747`;

DROP INDEX `activities_fi_e49747` ON `activities`;

ALTER TABLE `activities`

  CHANGE `id_member` `id_user` INTEGER NOT NULL;

CREATE INDEX `activities_fi_79a2df` ON `activities` (`id_user`);

ALTER TABLE `activities` ADD CONSTRAINT `activities_fk_79a2df`
    FOREIGN KEY (`id_user`)
    REFERENCES `users` (`id`);

ALTER TABLE `users` DROP FOREIGN KEY `users_fk_e49747`;

DROP INDEX `users_fi_e49747` ON `users`;

ALTER TABLE `users`

  ADD `name` VARCHAR(50) NOT NULL AFTER `username`,

  ADD `surname` VARCHAR(50) NOT NULL AFTER `name`,

  DROP `id_member`;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
',
);
    }

}