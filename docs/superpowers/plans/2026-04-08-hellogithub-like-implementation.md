# HelloGitHub 类开源社区站 Implementation Plan

> **For agentic workers:** REQUIRED: 若有子代理能力则使用 `superpowers:subagent-driven-development`；否则使用 `superpowers:executing-plans` 按任务执行。步骤使用 `- [ ]` 勾选跟踪。

**Goal:** 在单体 Laravel + Blade + MySQL 下，实现设计说明中的用户与 OAuth、仓库快照、投稿自动规则与人工审核、月刊与文章、标签、榜单与 GitHub 定时同步等能力，并满足无外键、`uniq_` 唯一索引、表结构由独立 SQL 演进等约定。

**Architecture:** 按领域划分 PHP 命名空间与服务类（认证与绑定、投稿流水线、审核、内容、同步、榜单）；表现层为 Blade 前台 + 独立管理后台路由组；写路径走表单与策略，读路径对列表与榜单使用缓存；GitHub 调用封装在可单测的客户端中，队列任务仅编排调用。

**Tech Stack:** PHP 8.2+、Laravel 11.x（版本以实现时 LTS 为准）、MySQL 8、Redis（队列与缓存）、Laravel Socialite（GitHub OAuth）、HTTP 客户端（Laravel HTTP / Guzzle）、PHPUnit / Pest、可选 Filament 或自研 Blade 后台（计划中二选一后锁定）。

**依据规格：** [docs/superpowers/specs/2026-04-08-hellogithub-like-design.md](../specs/2026-04-08-hellogithub-like-design.md)

---

## 工程约定速查

| 项 | 要求 |
|----|------|
| 分支 | 在独立功能分支开发；未经明确同意不在 `master` 上直接改功能代码 |
| 数据库 | 不使用 MySQL 外键；唯一索引名 `uniq_*`；字段避开保留字 |
| 结构变更 | 不使用 Laravel Migration 提交 DDL；使用 `database/sql/`（或项目约定目录）下版本化 SQL 文件，并在计划中注明执行顺序 |
| 测试 | 不编写会删除业务表或清空业务数据的测试；敏感数据库操作须人工确认 |
| 文档 | 本计划不嵌入完整业务代码示例，以文件路径、职责与验收命令描述为准 |

---

## 文件与目录总览（将创建或主要修改）

| 路径 | 职责 |
|------|------|
| `database/sql/` | 建表与索引 SQL（按序号前缀执行，如 `001_*.sql`） |
| `app/Models/` | Eloquent 模型，关联在模型层声明，数据库层无外键 |
| `app/Services/Submission/` | 投稿自动规则流水线、状态迁移 |
| `app/Services/GitHub/` | GitHub API 客户端、限流与错误类型 |
| `app/Jobs/` | 同步单仓/批量同步、榜单重算 |
| `app/Policies/` | 投稿编辑、审核、后台能力 |
| `routes/web.php` | 前台与认证相关 Web 路由 |
| `routes/admin.php`（或 `web.php` 前缀组） | 管理后台路由 |
| `resources/views/` | Blade 布局、首页、列表、详情、月刊、文章、用户中心 |
| `tests/Unit/` | 自动规则、GitHub 响应映射、纯函数 |
| `tests/Feature/` | 投稿、审核、OAuth 回调（HTTP 假实现） |
| `config/services.php` | GitHub OAuth 与 Token 配置项 |
| `.env.example` | 文档化 `GITHUB_TOKEN`、`GITHUB_CLIENT_*`、队列与 Redis |

---

## Chunk 1：项目基座与运行环境

### Task 1.1：初始化 Laravel 应用骨架

**Files:**

- Create: 项目根目录下标准 Laravel 目录树（`composer create-project` 生成）
- Modify: `.env.example`（数据库、Redis、队列、`GITHUB_*` 占位）
- Modify: `config/queue.php`、`config/cache.php`（Redis 驱动就绪）

- [ ] **Step 1：** 在仓库根目录执行 `composer create-project laravel/laravel .` 或等价方式生成应用（若目录非空则先调整目录策略），确保 `php artisan --version` 可运行。
- [ ] **Step 2：** 配置 `.env` 中 `DB_*`、`REDIS_*`、`QUEUE_CONNECTION=redis`（或开发期 `database` 但计划中注明生产改为 `redis`）。
- [ ] **Step 3：** 运行 `php artisan config:clear`，确认无启动异常。
- [ ] **Step 4：** 提交，说明信息使用中文，例如：`chore: 初始化 Laravel 应用骨架`。

### Task 1.2：数据库 DDL（第一版表集合）

**Files:**

- Create: `database/sql/001_core_tables.sql`（用户、OAuth 账户、仓库快照、投稿、审核日志）
- Create: `database/sql/002_content_and_tags.sql`（标签、仓库标签关联、黑名单、月刊、文章）
- Create: `database/sql/003_ranking.sql`（榜单配置与榜单结果缓存表，若采用「配置 + 物化结果」二表或单表，在注释中说明）
- Create: `database/sql/README.md`（执行顺序、环境与备份提示；**不写 SQL 示例外**可仅写步骤说明）

- [ ] **Step 1：** 根据规格第 5 节列出字段草案，表名统一小写蛇形；每张需要唯一约束的表使用 `uniq_` 前缀命名唯一索引（如 `uniq_repos_snapshots_github_coords` 类语义）。
- [ ] **Step 2：** 在本地或 CI 使用的 **非生产** 库中手工执行 SQL（或团队约定工具），确认无语法错误。
- [ ] **Step 3：** 文档中记录「新增环境如何初始化库」：按序号执行 `database/sql/*.sql`。
- [ ] **Step 4：** 提交：`chore: 添加首期数据库 DDL SQL 脚本`。

### Task 1.3：Eloquent 模型与关系（无迁移文件）

**Files:**

- Create: `app/Models/User.php` 及与各业务表对应模型文件
- 原则：不在仓库中新增 `database/migrations/*` 业务迁移；若 Laravel 安装自带迁移，在任务中注明是否保留仅 `failed_jobs` 等框架表或改为 SQL 手工对齐

- [ ] **Step 1：** 为每张业务表建立模型，`$table`、`$fillable`/`$casts` 与规格一致。
- [ ] **Step 2：** 在模型上声明 `belongsTo`/`hasMany`/`belongsToMany`（中间表无 DB 外键）。
- [ ] **Step 3：** `php artisan tinker` 或最小 Feature 测试：创建用户与一条快照记录（使用工厂或原始插入，**测试不删除整张表**）。
- [ ] **Step 4：** 提交：`feat: 添加核心业务 Eloquent 模型`。

---

## Chunk 2：认证、GitHub OAuth 与绑定门禁

### Task 2.1：邮箱注册登录

**Files:**

- 选用 Laravel Breeze（Blade）或等价最小脚手架；修改：`routes/web.php`、`app/Http/Controllers/Auth/*`、`resources/views/auth/*`

- [ ] **Step 1：** 安装并配置 Breeze（Blade），跑通注册、登录、登出。
- [ ] **Step 2：** Feature 测试：注册后可访问需登录页面；未登录重定向登录页。
- [ ] **Step 3：** 提交：`feat: 邮箱注册登录（Breeze Blade）`。

### Task 2.2：GitHub Socialite 与绑定

**Files:**

- Create: `app/Http/Controllers/Auth/GitHubController.php`（或 OAuth 专用命名空间）
- Modify: `routes/web.php`、`config/services.php`
- Create: `tests/Feature/GitHubOAuthTest.php`（HTTP Fake 回调与 state）

- [ ] **Step 1：** 安装 `laravel/socialite`，配置 `GITHUB_CLIENT_ID`、`GITHUB_CLIENT_SECRET`、`GITHUB_REDIRECT_URI`。
- [ ] **Step 2：** 实现「跳转 GitHub → 回调 → 写入/更新 `oauth_accounts` 并关联当前登录用户」；保证 `github_user_id` + provider 唯一（应用层 + `uniq_` 索引）。
- [ ] **Step 3：** 用户中心页展示是否已绑定；未绑定显示绑定按钮。
- [ ] **Step 4：** Feature 测试覆盖成功绑定与重复绑定拒绝场景。
- [ ] **Step 5：** 提交：`feat: GitHub OAuth 与账户绑定`。

### Task 2.3：投稿门禁策略

**Files:**

- Create: `app/Policies/SubmissionPolicy.php`
- Modify: 投稿相关 Controller 构造函数或中间件绑定策略

- [ ] **Step 1：** 规则：未绑定 GitHub 的用户不能创建/更新自己的投稿；管理员规则后续在审核任务中扩展。
- [ ] **Step 2：** Feature 测试：未绑定返回 403 或重定向带提示；已绑定可访问表单。
- [ ] **Step 3：** 提交：`feat: 投稿需绑定 GitHub 的策略`。

---

## Chunk 3：投稿自动规则与人工审核

**已锁定 — 管理后台方案：** B（`routes/web.php` 中 `admin` 前缀 + `EnsureUserIsAdmin` + `Admin\SubmissionReviewController` + Blade），不使用 Filament。

### Task 3.1：自动规则服务（TDD）

**Files:**

- Create: `app/Services/Submission/AutoRulePipeline.php` 及规则类（必填、URL 格式、`owner/name` 解析、去重、黑名单、关键词）
- Create: `tests/Unit/SubmissionAutoRulesTest.php`

- [ ] **Step 1：** 编写单元测试：给定输入仓库坐标与文案，期望通过或失败及错误码/文案列表（**测试数据使用内存或事务回滚，不 drop 表**）。
- [ ] **Step 2：** 实现最小流水线，使测试通过。
- [ ] **Step 3：** 提交：`feat: 投稿自动规则流水线`。

### Task 3.2：投稿 CRUD 与状态机

**Files:**

- Create: `app/Http/Controllers/SubmissionController.php`（或领域命名）
- Create: `resources/views/submissions/*.blade.php`

- [ ] **Step 1：** 创建投稿：写入后状态为「待人工审核」或规格约定的中间状态；自动规则失败则不入库或入「已驳回-自动」状态（实现前在代码注释中固定一种并与测试一致）。
- [ ] **Step 2：** 列表「我的投稿」只读当前用户。
- [ ] **Step 3：** Feature 测试：绑定用户成功提交一条进入待审队列。
- [ ] **Step 4：** 提交：`feat: 用户投稿与状态写入`。

### Task 3.3：管理后台审核队列

**Files:**

- 方案 A：`filament/filament` 资源类；方案 B：`routes/admin.php` + `app/Http/Controllers/Admin/*` + Blade
- Create: 中间件 `EnsureUserIsAdmin`（或 Spatie Permission，若引入则单独任务）
- Create: `app/Http/Controllers/Admin/SubmissionReviewController.php`（若不用 Filament）

- [ ] **Step 1：** 锁定方案 A 或 B 并写入本文件 Chunk 3 末尾一行记录（避免混用两套 CRUD）。
- [ ] **Step 2：** 审核通过：创建或更新 `repos_snapshots`、关联投稿状态、写 `review_logs`。
- [ ] **Step 3：** 审核驳回：写驳回原因与日志。
- [ ] **Step 4：** Feature 测试：管理员账号可通过/驳回（使用角色字段或 `is_admin` 布尔，以 DDL 为准）。
- [ ] **Step 5：** 提交：`feat: 后台投稿审核与审计日志`。

---

## Chunk 4：标签、黑名单与仓库前台展示

### Task 4.1：标签与多对多

**Files:**

- Create: `app/Http/Controllers/Admin/TagController.php` 或 Filament Resource
- Create: 前台 `app/Http/Controllers/RepositoryController.php`（列表筛选 `tag` 查询参数）

- [x] **Step 1：** 后台维护标签 CRUD；仓库快照与标签可编辑关联（至少后台）。
- [x] **Step 2：** 前台列表支持按标签过滤。
- [x] **Step 3：** Feature 测试：筛选 URL 与结果条数断言。
- [x] **Step 4：** 提交：`feat: 标签管理与前台筛选`。

### Task 4.2：仓库详情与列表 Blade

**Files:**

- Create: `resources/views/repositories/index.blade.php`、`show.blade.php`
- Modify: `routes/web.php`

- [x] **Step 1：** 列表分页、详情展示快照字段（来自本地库，不实时打 GitHub）。
- [x] **Step 2：** 提交：`feat: 仓库列表与详情页（Blade）`。

---

## Chunk 5：月刊与文章

### Task 5.1：月刊期号与文章后台

**Files:**

- Create: `app/Http/Controllers/Admin/MagazineIssueController.php`、`ArticleController.php` 或 Filament Resources
- Create: `resources/views/magazines/*.blade.php`（前台）

- [ ] **Step 1：** 后台：创建期号、排序、封面图路径或上传策略（仅存路径字符串，文件存储用 `storage/app/public` 并 `php artisan storage:link`）。
- [ ] **Step 2：** 文章正文：Markdown 存库 + 前台 `Str::markdown` 或等价渲染（安全过滤 XSS）。
- [ ] **Step 3：** 前台：期号列表 → 期详情目录 → 文章阅读页。
- [ ] **Step 4：** Feature 测试：发布一期含两篇文章可读。
- [ ] **Step 5：** 提交：`feat: 月刊与文章（后台+前台）`。

---

## Chunk 6：GitHub 定时同步

### Task 6.1：GitHub 客户端与假实现测试

**Files:**

- Create: `app/Services/GitHub/GitHubRepositoryClient.php`
- Create: `tests/Unit/GitHubRepositoryClientTest.php`

- [ ] **Step 1：** 封装「根据 `owner/repo` 拉取仓库信息」方法，映射到快照表字段。
- [ ] **Step 2：** 当 `GITHUB_TOKEN` 为空时，客户端返回明确「跳过」类型，不抛未捕获异常（由调用方记日志）。
- [ ] **Step 3：** 单元测试使用 `Http::fake` 模拟 200/404/403。
- [ ] **Step 4：** 提交：`feat: GitHub 仓库元数据客户端`。

### Task 6.2：队列任务与调度

**Files:**

- Create: `app/Jobs/SyncRepositorySnapshotJob.php`、`SyncAllRepositoriesJob.php`（或批量调度类）
- Modify: `routes/console.php`（`Schedule::` 注册）

- [ ] **Step 1：** 任务内逐条更新，单条失败写快照表错误字段并继续。
- [ ] **Step 2：** 配置 `schedule`：例如每日一次同步全部已发布仓库（具体 cron 写在 `routes/console.php` 注释）。
- [ ] **Step 3：** 本地运行 `php artisan schedule:test` 或手动 `dispatch` 验证一条。
- [ ] **Step 4：** 提交：`feat: 定时批量同步 GitHub 元数据`。

---

## Chunk 7：榜单与首页聚合

### Task 7.1：榜单计算与缓存

**Files:**

- Create: `app/Services/Ranking/RankingService.php`
- Create: `app/Jobs/RebuildRankingJob.php`
- Modify: `routes/console.php`

- [ ] **Step 1：** 第一版策略：按 `stars` 降序 + 更新时间过滤（可在 `ranking_configs` 存 JSON 参数）。
- [ ] **Step 2：** 结果写入榜单结果表或缓存 Tag；前台只读缓存/表。
- [ ] **Step 3：** 调度：在同步任务之后或独立低频任务重算（避免与同步同批锁表，采用队列）。
- [ ] **Step 4：** 提交：`feat: 榜单计算与定时重算`。

### Task 7.2：首页 Blade

**Files:**

- Modify: `resources/views/welcome.blade.php` 或新建 `home.blade.php`
- Modify: `routes/web.php`

- [ ] **Step 1：** 聚合：推荐仓库、最新通过投稿、榜单入口、月刊入口链接。
- [ ] **Step 2：** 提交：`feat: 首页聚合展示（Blade）`。

---

## Chunk 8：观测、配置与收尾

### Task 8.1：日志与后台健康摘要

**Files:**

- Modify: 同步 Job、GitHub 客户端（`Log::warning` 结构化上下文）
- Create: 管理端「同步最近错误」只读页或 Filament Widget

- [ ] **Step 1：** 列出最近 N 条同步失败仓库与原因。
- [ ] **Step 2：** 提交：`feat: 同步失败可观测后台摘要`。

### Task 8.2：全量测试与文档对齐

- [ ] **Step 1：** 运行 `php artisan test`，修复失败用例。
- [ ] **Step 2：** 更新规格或本计划中的偏差说明（若有）。
- [ ] **Step 3：** 提交：`chore: 测试与文档对齐`。

---

## 执行与评审说明

- 每个 Task 完成后应可独立演示或运行测试；Chunk 之间顺序依赖已按数据→认证→业务→同步→展示排列。
- 规格评审循环（plan-document-reviewer）由人工或具备子代理的环境执行；通过后再批量执行 Chunk 可降低返工。
- 执行阶段请遵守：`superpowers:using-git-worktrees`（若需隔离工作区）、`superpowers:executing-plans` 逐步勾选、`superpowers:finishing-a-development-branch` 收尾合并策略。

---

## 计划变更记录

| 日期 | 说明 |
|------|------|
| 2026-04-08 | 初版：基于设计说明拆 Chunk 与任务，无代码示例，含 SQL 目录与分支约定 |
