<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1455993200.
 * Generated on 2016-02-20 19:33:20 
 */
class PropelMigration_1455993200
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

CREATE TABLE `images_galleries_map`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `id_image` INTEGER NOT NULL,
    `id_gallery` INTEGER NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `images_galleries_map_fi_5d0623` (`id_image`),
    INDEX `images_galleries_map_fi_f5e11a` (`id_gallery`),
    CONSTRAINT `images_galleries_map_fk_5d0623`
        FOREIGN KEY (`id_image`)
        REFERENCES `images` (`id`),
    CONSTRAINT `images_galleries_map_fk_f5e11a`
        FOREIGN KEY (`id_gallery`)
        REFERENCES `galleries` (`id`)
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

DROP TABLE IF EXISTS `images_galleries_map`;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
',
);
    }

}