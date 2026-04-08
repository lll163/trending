SET NAMES utf8mb4;

-- 榜单规则参数（JSON），与具体排序实现解耦
CREATE TABLE IF NOT EXISTS `ranking_configs` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `ranking_key` VARCHAR(64) NOT NULL COMMENT '如 trending_monthly',
    `params_json` JSON NULL COMMENT '权重、时间窗等',
    `last_computed_at` DATETIME NULL DEFAULT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uniq_ranking_configs_key` (`ranking_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='榜单配置';

-- 物化榜单结果，供前台只读（可定时清空重算）
CREATE TABLE IF NOT EXISTS `ranking_entries` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `ranking_key` VARCHAR(64) NOT NULL,
    `repos_snapshot_id` BIGINT UNSIGNED NOT NULL,
    `position` INT UNSIGNED NOT NULL COMMENT '名次，从 1 开始',
    `score` DECIMAL(20, 6) NOT NULL DEFAULT 0,
    `computed_at` DATETIME NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uniq_ranking_entries_key_position` (`ranking_key`, `position`),
    KEY `idx_ranking_entries_key` (`ranking_key`),
    KEY `idx_ranking_entries_repo` (`repos_snapshot_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='榜单条目快照';
