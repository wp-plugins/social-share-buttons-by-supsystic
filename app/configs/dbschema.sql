CREATE TABLE `%prefix%projects` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `settings` TEXT NOT NULL,
  PRIMARY KEY  (`id`)
)
  COLLATE='utf8_general_ci'
  ENGINE=InnoDB
;

CREATE TABLE `%prefix%networks` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `url` VARCHAR(255) NOT NULL,
  `class` VARCHAR(255) NOT NULL,
  `brand_primary` VARCHAR(7) NOT NULL DEFAULT '#000000',
  `brand_secondary` VARCHAR(7) NOT NULL DEFAULT '#ffffff',
  `total_shares` INT(11) UNSIGNED NULL DEFAULT '0',
  PRIMARY KEY  (`id`)
)
  COLLATE='utf8_general_ci'
  ENGINE=InnoDB
;
CREATE TABLE `%prefix%project_networks` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `project_id` INT(11) UNSIGNED NULL DEFAULT NULL,
  `network_id` INT(11) UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY  (`id`),
  INDEX `FK__%prefix%projects` (`project_id`),
  INDEX `FK__%prefix%networks` (`network_id`),
  CONSTRAINT `FK__%prefix%networks` FOREIGN KEY (`network_id`) REFERENCES `%prefix%networks` (`id`),
  CONSTRAINT `FK__%prefix%projects` FOREIGN KEY (`project_id`) REFERENCES `%prefix%projects` (`id`) ON DELETE CASCADE
)
  COLLATE='utf8_general_ci'
  ENGINE=InnoDB
;

CREATE TABLE `%prefix%shares` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `network_id` INT(11) UNSIGNED NULL DEFAULT NULL,
  `project_id` INT(11) UNSIGNED NULL DEFAULT NULL,
  `post_id` INT(11) UNSIGNED NULL DEFAULT NULL,
  `timestamp` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `FK_%prefix%shares_%prefix%networks` (`network_id`),
  INDEX `FK_%prefix%shares_%prefix%projects` (`project_id`),
  CONSTRAINT `FK_%prefix%shares_%prefix%networks` FOREIGN KEY (`network_id`) REFERENCES `%prefix%networks` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_%prefix%shares_%prefix%projects` FOREIGN KEY (`project_id`) REFERENCES `%prefix%projects` (`id`) ON DELETE CASCADE
)
  COLLATE='utf8_general_ci'
  ENGINE=InnoDB
;


INSERT INTO `%prefix%networks` (`id`, `name`, `url`, `class`, `brand_primary`, `brand_secondary`) VALUES (null, 'Facebook', 'http://www.facebook.com/sharer.php?u={url}', 'facebook', '#3b5998', '#ffffff');
INSERT INTO `%prefix%networks` (`id`, `name`, `url`, `class`, `brand_primary`, `brand_secondary`) VALUES (null, 'Twitter', 'https://twitter.com/share?url={url}&text={title}', 'twitter', '#55acee', '#ffffff');
INSERT INTO `%prefix%networks` (`id`, `name`, `url`, `class`, `brand_primary`, `brand_secondary`) VALUES (null, 'Google+', 'https://plus.google.com/share?url={url}', 'googleplus', '#dd4b39', '#ffffff');
INSERT INTO `%prefix%networks` (`id`, `name`, `url`, `class`, `brand_primary`, `brand_secondary`) VALUES (null, 'VKontakte', 'http://vk.com/share.php?url={url}', 'vk', '#45668e', '#ffffff');
INSERT INTO `%prefix%networks` (`id`, `name`, `url`, `class`, `brand_primary`, `brand_secondary`) VALUES (null, 'Like', '#', 'like', '#9b59b6', '#ffffff');
