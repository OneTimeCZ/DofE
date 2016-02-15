<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1455531364.
 * Generated on 2016-02-15 11:16:04 
 */
class PropelMigration_1455531364
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

ALTER TABLE `quotes` DROP FOREIGN KEY `quotes_fk_79a2df`;

DROP INDEX `quotes_fi_79a2df` ON `quotes`;

ALTER TABLE `quotes`

  CHANGE `id_user` `id_member` INTEGER NOT NULL;

CREATE INDEX `quotes_fi_e49747` ON `quotes` (`id_member`);

ALTER TABLE `quotes` ADD CONSTRAINT `quotes_fk_e49747`
    FOREIGN KEY (`id_member`)
    REFERENCES `members` (`id`);

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

ALTER TABLE `quotes` DROP FOREIGN KEY `quotes_fk_e49747`;

DROP INDEX `quotes_fi_e49747` ON `quotes`;

ALTER TABLE `quotes`

  CHANGE `id_member` `id_user` INTEGER NOT NULL;

CREATE INDEX `quotes_fi_79a2df` ON `quotes` (`id_user`);

ALTER TABLE `quotes` ADD CONSTRAINT `quotes_fk_79a2df`
    FOREIGN KEY (`id_user`)
    REFERENCES `users` (`id`);

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
',
);
    }

}