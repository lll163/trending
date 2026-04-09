# 项目执行记录

本文档汇总仓库内已落地的实现步骤、运行环境要求、命令与 Git 历史，便于续开发与交接。

**最后更新：** 2026-04-08（含 Chunk 4：仓库列表/标签/快照打标）  
**当前开发分支：** `feature/laravel-bootstrap`（已推送远程 `origin/feature/laravel-bootstrap`）

---

## 1. 规格与计划入口

| 文档 | 路径 |
|------|------|
| 设计说明 | [specs/2026-04-08-hellogithub-like-design.md](./specs/2026-04-08-hellogithub-like-design.md) |
| 实现计划 | [plans/2026-04-08-hellogithub-like-implementation.md](./plans/2026-04-08-hellogithub-like-implementation.md) |

---

## 2. 已执行实现阶段（对照实现计划）

### Chunk 1：基座与数据模型

- **Laravel 12** 应用合并至仓库根目录，包名 `lll163/trending`，保留 `docs/`。
- **Composer：** 若阿里云等镜像解析失败，可对项目使用 `composer config repositories.packagist composer https://repo.packagist.org` 后再安装依赖（当前 `composer.json` 中可能保留该 `repositories` 配置）。
- **`.env.example`：** 默认 MySQL、`QUEUE_CONNECTION`/`CACHE_STORE` 倾向 redis、`SESSION_DRIVER=file`、GitHub 相关占位变量。
- **`config/services.php`：** `github` 段（OAuth + `GITHUB_TOKEN` 占位）。
- **`database/sql/`：** `001_core_tables.sql`、`002_content_and_tags.sql`、`003_ranking.sql` 及 `README.md`（**MySQL 8**、无外键、`uniq_` 前缀；`001` 含对 `users` 的 `ALTER`）。
- **Eloquent 模型：** `User` 扩展字段与关联；`OauthAccount`、`ReposSnapshot`、`Submission`、`ReviewLog`、`Tag`、`BlacklistEntry`、`MagazineIssue`、`Article`、`RankingConfig`、`RankingEntry`。
- **说明：** 业务表以 SQL 脚本为准；Laravel 默认 **Migration** 仍提供 `users` / `cache` / `jobs` 等框架表。

### Chunk 2：认证、GitHub、投稿门禁

- **Laravel Breeze（Blade）** + **Tailwind**，标准注册/登录/资料/密码流程。
- **Laravel Socialite：** `GitHubController` 处理 `GET /auth/github` 与 `GET /auth/github/callback`（需已登录）；写入 `oauth_accounts` 并设置 `users.github_bound_at`；同一 GitHub 账户不可绑定到第二个用户。
- **资料页** 含 GitHub 绑定区块：`profile.partials.github-binding-form`。
- **`SubmissionPolicy`：** 创建/更新投稿需已绑定 GitHub；`SubmissionController@create` 与占位视图 `submissions/create`。
- **测试：** `GitHubBindingTest`、`SubmissionCreateGateTest`；`tests/TestCase` 在 **sqlite** 下自动补 `users.is_admin`、`users.github_bound_at` 与 `oauth_accounts` 表结构（不写 DROP，与生产 SQL 语义对齐）。

### Chunk 3：投稿自动规则与人工审核

- **状态常量：** `App\Models\SubmissionStatus`（`pending_review`、`rejected_auto`、`approved`、`rejected_review` 等）。
- **自动规则：** `AutoRulePipeline` + `SubmissionDraft` + `AutoRuleResult`；依赖 `EloquentDuplicateRepoChecker`、`BlacklistMatcher`；`config/submission.php` 配置推荐语关键词（`.env` 可选 `SUBMISSION_BLOCKED_KEYWORDS`）。流水线第三参数可为单测覆盖关键词（生产由容器解析为 null）。
- **用户侧：** `GET/POST /submissions`、`SubmissionController@index|create|store`；通过规则 → `pending_review`，否则 `rejected_auto` 并写入 `reject_reason`；视图 `submissions/index`、`submissions/create`；导航栏增加「我的投稿」「提交项目」。
- **管理侧（方案 B）：** 中间件 `admin`（`EnsureUserIsAdmin`）；`Admin\SubmissionReviewController`；路由前缀 `/admin/submissions`；通过时 `firstOrCreate` `repos_snapshots` 并写 `review_logs`；驳回时 `rejected_review` + 原因。
- **策略：** `SubmissionPolicy` 增加 `viewAny`、`view`。
- **测试：** `tests/Unit/SubmissionAutoRulesTest`（纯 PHPUnit，无 DB）；`SubmissionStoreFlowTest`、`AdminSubmissionReviewTest`；`tests/TestCase` 在 sqlite 下补充 `repos_snapshots`、`submissions`、`review_logs`、`blacklist_entries` 表（无 `users` 表时不执行补丁，避免纯单元测试连库）。

### Chunk 4：前台仓库列表、标签与快照打标

- **前台布局：** `layouts/site`、`partials/public-nav`；`GET /repositories` 分页列表，支持 `?tag=slug`；`GET /repositories/{owner}/{repo}` 详情（仅 `is_published=true`，owner/repo 小写匹配）。
- **控制器：** `RepositoryController`；`Admin\TagController`（resource，无 show）；`Admin\ReposSnapshotController`（快照列表检索、`editTags` / `updateTags` 同步 `repo_tag`）。
- **视图：** `repositories/index|show`；`admin/tags/*`；`admin/repos_snapshots/*`；欢迎页与 Breeze 导航增加「开源仓库」/ 管理员「标签」「仓库快照」入口。
- **工厂：** `TagFactory`；`Tag` 模型启用 `HasFactory`。
- **测试：** `RepositoryAndTagAdminTest`（列表仅已发布、`?tag=` 筛选、详情大小写、管理员同步标签、非管理员禁止）；`tests/TestCase` 在 sqlite 下补充 `tags`、`repo_tag` 表。

---

## 3. 环境依赖

| 组件 | 说明 |
|------|------|
| PHP | 8.2+，扩展建议：`pdo_mysql` 或 **`pdo_sqlite`**（跑 PHPUnit 默认配置）、`openssl`、`mbstring` 等 |
| Composer | 2.x |
| Node.js | Breeze/Vite 7 官方建议 **Node 20.19+**；Node 18 下 `npm run build` 可能告警但仍可能成功 |
| MySQL | 8（执行 `database/sql` 脚本） |
| Redis | 生产/队列与缓存推荐；本地可在 `.env` 改为 `database` / `sync` |

---

## 4. 常用命令

```bash
# 依赖
composer install
npm install

# 前端资源（开发）
npm run dev
# 或生产构建
npm run build

# 应用密钥（首次）
cp .env.example .env
php artisan key:generate

# 框架表
php artisan migrate

# 业务表（在 MySQL 上按序执行，见 database/sql/README.md）
# mysql ... < database/sql/001_core_tables.sql
# mysql ... < database/sql/002_content_and_tags.sql
# mysql ... < database/sql/003_ranking.sql

# 测试（需启用 pdo_sqlite，或自行改 phpunit 使用 MySQL 测试库）
php artisan test
php artisan test tests/Unit
```

---

## 5. 环境变量（OAuth 与同步）

在 `.env` 中配置（示例见 `.env.example`）：

- `GITHUB_CLIENT_ID`、`GITHUB_CLIENT_SECRET`
- `GITHUB_REDIRECT_URI`：须与 GitHub OAuth App 回调一致，例如 `{APP_URL}/auth/github/callback`
- `GITHUB_TOKEN`：后续定时同步 GitHub API 使用（可暂空）
- `SUBMISSION_BLOCKED_KEYWORDS`：可选，逗号分隔；留空则用 `config/submission.php` 默认关键词列表

---

## 6. 主要路由（摘录）

| 方法 | URI | 名称 | 说明 |
|------|-----|------|------|
| GET | `/` | — | 欢迎页 |
| GET | `/register`、`POST /register` | register | 注册 |
| GET | `/login`、`POST /login` | login | 登录 |
| GET | `/dashboard` | dashboard | 需 `auth`、`verified` |
| GET | `/profile` | profile.edit | 资料（含 GitHub 绑定） |
| GET | `/auth/github` | github.redirect | 跳转 GitHub（需登录） |
| GET | `/auth/github/callback` | github.callback | OAuth 回调（需登录） |
| GET | `/submissions` | submissions.index | 我的投稿列表 |
| GET | `/submissions/create` | submissions.create | 投稿表单 |
| POST | `/submissions` | submissions.store | 提交投稿 |
| GET | `/admin/submissions` | admin.submissions.index | 审核队列（管理员） |
| GET | `/admin/submissions/{id}` | admin.submissions.show | 单条审核 |
| POST | `/admin/submissions/{id}/approve` | admin.submissions.approve | 通过 |
| POST | `/admin/submissions/{id}/reject` | admin.submissions.reject | 驳回（需 remark） |
| GET | `/repositories` | repositories.index | 开源仓库列表（公开） |
| GET | `/repositories/{owner}/{repo}` | repositories.show | 仓库详情（公开） |
| GET/POST 等 | `/admin/tags` | admin.tags.* | 标签 CRUD（管理员） |
| GET | `/admin/repos-snapshots` | admin.repos-snapshots.index | 快照列表（管理员） |
| GET | `/admin/repos-snapshots/{id}/tags` | admin.repos-snapshots.edit-tags | 编辑快照标签 |
| PUT | `/admin/repos-snapshots/{id}/tags` | admin.repos-snapshots.update-tags | 保存快照标签 |

---

## 7. 测试与本地限制说明

- **默认 `phpunit.xml`：** `DB_CONNECTION=sqlite`、`DB_DATABASE=:memory:`，需要 PHP 启用 **`pdo_sqlite`**。
- 若仅启用 **`pdo_mysql`**：请在 `phpunit.xml`（或本地覆盖文件）中改为测试库连接，并先 `migrate` + 按需执行业务 SQL；**勿对生产库跑测试**。
- 某次在「无 pdo_sqlite、无本机 MySQL」环境下，全量 `php artisan test` 会因无法连接数据库失败；**`tests/Unit` 可不依赖数据库刷新** 时更易通过。

---

## 8. Git 提交记录（与本实现相关）

| 提交说明 | 类型 |
|----------|------|
| `chore: 初始化 Laravel 12 应用骨架` | Chunk 1 |
| `chore: 添加首期业务表 DDL（database/sql）` | Chunk 1 |
| `feat: 添加业务域 Eloquent 模型与 User 扩展` | Chunk 1 |
| `feat: Breeze 认证、GitHub 绑定与投稿门禁` | Chunk 2 |
| `docs: 新增 HelloGitHub 类站点实现计划` | 文档 |
| `docs: 新增 HelloGitHub 类站点设计说明（Blade 前台）` | 文档 |

远程仓库：`https://github.com/lll163/trending`（默认主分支为 `master`，功能开发在 `feature/laravel-bootstrap`）。

---

## 9. 后续计划（未实现）

见 [实现计划](./plans/2026-04-08-hellogithub-like-implementation.md) **Chunk 5** 及之后：月刊、GitHub 定时同步、榜单等。

---

## 10. 变更记录

| 日期 | 说明 |
|------|------|
| 2026-04-09 | 初版：汇总 Chunk 1～2 执行信息、环境与 Git |
| 2026-04-09 | 补充 Chunk 3：自动规则、投稿 CRUD、Blade 后台审核、测试与路由 |
| 2026-04-08 | 补充 Chunk 4：前台仓库列表与标签筛选、管理员标签/快照打标、相关测试与路由 |
