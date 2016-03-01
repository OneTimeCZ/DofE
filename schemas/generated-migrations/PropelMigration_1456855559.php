<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1456855559.
 * Generated on 2016-03-01 19:05:59 
 */
class PropelMigration_1456855559
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

CREATE TABLE `bans`
(
    `id` INTEGER NOT NULL,
    `id_user` INTEGER NOT NULL,
    `banned_by` INTEGER NOT NULL,
    `reason` VARCHAR(250),
    `ending_date` DATETIME NOT NULL,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`),
    INDEX `bans_fi_79a2df` (`id_user`),
    INDEX `bans_fi_ceb9cc` (`banned_by`),
    CONSTRAINT `bans_fk_79a2df`
        FOREIGN KEY (`id_user`)
        REFERENCES `users` (`id`),
    CONSTRAINT `bans_fk_ceb9cc`
        FOREIGN KEY (`banned_by`)
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

DROP TABLE IF EXISTS `bans`;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
',
);
    }

}