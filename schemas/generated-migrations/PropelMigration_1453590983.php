<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1453590983.
 * Generated on 2016-01-24 00:16:23 
 */
class PropelMigration_1453590983
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

CREATE INDEX `activities_fi_79a2df` ON `activities` (`id_user`);

CREATE INDEX `activities_fi_5574cf` ON `activities` (`id_level`);

CREATE INDEX `activities_fi_404138` ON `activities` (`id_activity_type`);

ALTER TABLE `activities` ADD CONSTRAINT `activities_fk_79a2df`
    FOREIGN KEY (`id_user`)
    REFERENCES `users` (`id`);

ALTER TABLE `activities` ADD CONSTRAINT `activities_fk_5574cf`
    FOREIGN KEY (`id_level`)
    REFERENCES `levels` (`id`);

ALTER TABLE `activities` ADD CONSTRAINT `activities_fk_404138`
    FOREIGN KEY (`id_activity_type`)
    REFERENCES `activity_types` (`id`);

CREATE INDEX `activity_logs_fi_efd5c8` ON `activity_logs` (`id_activity`);

ALTER TABLE `activity_logs` ADD CONSTRAINT `activity_logs_fk_efd5c8`
    FOREIGN KEY (`id_activity`)
    REFERENCES `activities` (`id`);

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

ALTER TABLE `activities` DROP FOREIGN KEY `activities_fk_79a2df`;

ALTER TABLE `activities` DROP FOREIGN KEY `activities_fk_5574cf`;

ALTER TABLE `activities` DROP FOREIGN KEY `activities_fk_404138`;

DROP INDEX `activities_fi_79a2df` ON `activities`;

DROP INDEX `activities_fi_5574cf` ON `activities`;

DROP INDEX `activities_fi_404138` ON `activities`;

ALTER TABLE `activity_logs` DROP FOREIGN KEY `activity_logs_fk_efd5c8`;

DROP INDEX `activity_logs_fi_efd5c8` ON `activity_logs`;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
',
);
    }

}