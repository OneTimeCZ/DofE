
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- users
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(50) NOT NULL,
    `url` VARCHAR(50) NOT NULL,
    `email` VARCHAR(100) NOT NULL,
    `email_confirmed_at` DATETIME,
    `email_confirm_token` VARCHAR(50),
    `password` VARCHAR(50) NOT NULL,
    `password_reset_token` VARCHAR(50),
    `permissions` INTEGER,
    `signin_count` INTEGER,
    `id_image` INTEGER,
    `last_signin_at` DATETIME,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- articles
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `articles`;

CREATE TABLE `articles`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `id_user` INTEGER NOT NULL,
    `id_image` INTEGER,
    `id_category` INTEGER NOT NULL,
    `title` VARCHAR(100) NOT NULL,
    `url` VARCHAR(120) NOT NULL,
    `keywords` VARCHAR(200) NOT NULL,
    `content` TEXT NOT NULL,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`),
    INDEX `articles_fi_79a2df` (`id_user`),
    INDEX `articles_fi_5d0623` (`id_image`),
    INDEX `articles_fi_e17637` (`id_category`),
    CONSTRAINT `articles_fk_79a2df`
        FOREIGN KEY (`id_user`)
        REFERENCES `users` (`id`),
    CONSTRAINT `articles_fk_5d0623`
        FOREIGN KEY (`id_image`)
        REFERENCES `images` (`id`),
    CONSTRAINT `articles_fk_e17637`
        FOREIGN KEY (`id_category`)
        REFERENCES `categories` (`id`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- images
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `images`;

CREATE TABLE `images`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `description` VARCHAR(150),
    `path` VARCHAR(150),
    PRIMARY KEY (`id`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- comments
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `comments`;

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

-- ---------------------------------------------------------------------
-- categories
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `categories`;

CREATE TABLE `categories`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(50) NOT NULL,
    `url` VARCHAR(50) NOT NULL,
    `color` VARCHAR(10),
    PRIMARY KEY (`id`)
) ENGINE=InnoDB;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
