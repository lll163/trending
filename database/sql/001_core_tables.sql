-- 核心业务表（无外键）；执行前请确保已存在 Laravel 默认 `users` 表，或自行创建等价 users 结构。
-- 字符集与排序规则可按部署环境调整。

SET NAMES utf8mb4;

-- 扩展 users：管理员标记、GitHub 绑定时间（应用层保证与 oauth_accounts 一致）
ALTER TABLE `users`
    ADD COLUMN `is_admin` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '是否后台管理员' AFTER `password`,
    ADD COLUMN `github_bound_at` DATETIME NULL DEFAULT NULL COMMENT '完成 GitHub 绑定时间' AFTER `is_admin`;

-- 第三方 OAuth 账户（与 users 逻辑关联，无 DB 外键）
CREATE TABLE IF NOT EXISTS `oauth_accounts` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` BIGINT UNSIGNED NOT NULL COMMENT '对应 users.id',
    `provider` VARCHAR(32) NOT NULL COMMENT '如 github',
    `provider_user_id` VARCHAR(64) NOT NULL COMMENT 'GitHub 用户数字 id 或 login，存字符串',
    `provider_login` VARCHAR(255) NULL DEFAULT NULL COMMENT 'GitHub login 展示用',
    `access_token` TEXT NULL COMMENT '敏感：生产建议加密或仅存引用',
    `refresh_token` TEXT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_oauth_accounts_user_id` (`user_id`),
    UNIQUE KEY `uniq_oauth_provider_user` (`provider`, `provider_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='OAuth 绑定';

-- 平台收录的 GitHub 仓库快照
CREATE TABLE IF NOT EXISTS `repos_snapshots` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `github_owner` VARCHAR(255) NOT NULL,
    `github_repo` VARCHAR(255) NOT NULL,
    `description` TEXT NULL,
    `stars_cnt` INT UNSIGNED NOT NULL DEFAULT 0,
    `forks_cnt` INT UNSIGNED NOT NULL DEFAULT 0,
    `default_branch` VARCHAR(255) NULL DEFAULT NULL,
    `homepage_url` VARCHAR(512) NULL DEFAULT NULL,
    `pushed_at` DATETIME NULL DEFAULT NULL,
    `snapshot_synced_at` DATETIME NULL DEFAULT NULL,
    `snapshot_sync_error` VARCHAR(500) NULL DEFAULT NULL,
    `is_published` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '是否对前台展示',
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uniq_repos_snapshots_github_coords` (`github_owner`, `github_repo`),
    KEY `idx_repos_snapshots_stars` (`stars_cnt`),
    KEY `idx_repos_snapshots_synced` (`snapshot_synced_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='仓库元数据快照';

-- 用户投稿
CREATE TABLE IF NOT EXISTS `submissions` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `github_owner` VARCHAR(255) NOT NULL,
    `github_repo` VARCHAR(255) NOT NULL,
    `pitch` TEXT NULL COMMENT '推荐语/说明',
    `status` VARCHAR(32) NOT NULL DEFAULT 'pending_auto' COMMENT 'pending_auto|pending_review|approved|rejected_auto|rejected_review',
    `reject_reason` VARCHAR(500) NULL DEFAULT NULL,
    `repos_snapshot_id` BIGINT UNSIGNED NULL DEFAULT NULL COMMENT '审核通过后关联 repos_snapshots.id',
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_submissions_user_id` (`user_id`),
    KEY `idx_submissions_status` (`status`),
    KEY `idx_submissions_repos_snapshot_id` (`repos_snapshot_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='项目投稿';

-- 审核操作留痕
CREATE TABLE IF NOT EXISTS `review_logs` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `submission_id` BIGINT UNSIGNED NOT NULL,
    `admin_user_id` BIGINT UNSIGNED NOT NULL COMMENT '操作者 users.id',
    `action` VARCHAR(32) NOT NULL COMMENT 'approve|reject|...',
    `remark` VARCHAR(500) NULL DEFAULT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_review_logs_submission_id` (`submission_id`),
    KEY `idx_review_logs_admin_user_id` (`admin_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='投稿审核日志';
