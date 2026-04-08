# 业务库表 SQL 说明

与《设计说明》一致：**业务表结构由本目录 SQL 维护**，不使用 Laravel Migration 新增业务表。框架自带的 `users` / `cache` / `jobs` 等表仍可由 Laravel 默认 Migration 创建。

**方言：** 下列脚本按 **MySQL 8** 编写（`JSON` 类型等）；若使用 SQLite 仅作本地联调，需自行调整或改用 MySQL。

## 执行顺序

1. （可选）在项目根目录执行 `php artisan migrate`，创建框架默认表（含 `users`）。
2. 在目标库按文件名排序执行：
   - `001_core_tables.sql`
   - `002_content_and_tags.sql`
   - `003_ranking.sql`
3. 若 `001` 中的 `ALTER TABLE users` 与当前 `users` 结构冲突，请先备份并在测试库验证后再用于生产。

## 环境提示

- 仅在**非生产**或已备份的数据库上执行；本仓库测试用例**不得**包含删除业务表或清空业务数据的脚本。
- 关联关系在应用层维护，**未使用 MySQL 外键**。
