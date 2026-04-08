SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS `tags` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `slug` VARCHAR(128) NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uniq_tags_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='标签';

CREATE TABLE IF NOT EXISTS `repo_tag` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `repos_snapshot_id` BIGINT UNSIGNED NOT NULL,
    `tag_id` BIGINT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uniq_repo_tag_pair` (`repos_snapshot_id`, `tag_id`),
    KEY `idx_repo_tag_tag_id` (`tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='仓库与标签';

CREATE TABLE IF NOT EXISTS `blacklist_entries` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `pattern_type` VARCHAR(32) NOT NULL COMMENT 'owner_repo|owner|keyword',
    `pattern_value` VARCHAR(512) NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_blacklist_pattern_type` (`pattern_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='自动规则黑名单';

CREATE TABLE IF NOT EXISTS `magazine_issues` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `issue_code` VARCHAR(32) NOT NULL COMMENT '如 2026-01',
    `title` VARCHAR(255) NOT NULL,
    `cover_path` VARCHAR(512) NULL DEFAULT NULL COMMENT '封面存储路径或 URL',
    `catalog_json` JSON NULL COMMENT '目录结构 JSON',
    `status` VARCHAR(32) NOT NULL DEFAULT 'draft' COMMENT 'draft|published',
    `published_at` DATETIME NULL DEFAULT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uniq_magazine_issues_issue_code` (`issue_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='月刊期号';

CREATE TABLE IF NOT EXISTS `articles` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `magazine_issue_id` BIGINT UNSIGNED NULL DEFAULT NULL COMMENT 'NULL 表示非月刊独立文章',
    `slug` VARCHAR(255) NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `body` LONGTEXT NOT NULL COMMENT 'Markdown 或 HTML，由应用层约定',
    `sort_order` INT NOT NULL DEFAULT 0,
    `status` VARCHAR(32) NOT NULL DEFAULT 'draft',
    `published_at` DATETIME NULL DEFAULT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uniq_articles_slug` (`slug`),
    KEY `idx_articles_magazine_issue_id` (`magazine_issue_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='文章';
