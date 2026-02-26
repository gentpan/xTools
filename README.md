# WP Starter Kit

## 插件简介
WP Starter Kit 是一个面向 WordPress 站点运维与新手管理者的工具集合插件，集中提供邮件发送配置、ID 替换、系统状态诊断与常见性能优化功能。

## 当前功能
- 邮件发送配置
- 支持 `SMTP` 与 `Resend API` 两种发送模式
- 支持测试邮件发送、测试主题/正文模板、HTML/纯文本切换
- 可设置发件人邮箱与发件人名称

- 内容与数据库工具
- ID 替换工具（可视化）
- 支持按文章类型/状态/关键词筛选与分页查看
- 提供数据库概览（总记录、最大 ID、ID 空洞估算、按类型统计）
- 一键将列表项“设为旧 ID”并执行替换

- 系统状态诊断
- 显示 WordPress / PHP / MySQL 版本
- 显示缓存状态（对象缓存、`advanced-cache.php`）
- 显示关键常量（`WP_DEBUG`、`WP_CACHE`、`WP_MEMORY_LIMIT` 等）
- 显示关键目录权限（可读/可写/权限值）

- 常规优化
- 禁用 Gutenberg 编辑器
- 禁用 Gutenberg 小工具
- 启用友情链接
- 去除分类链接中的 `category`
- 禁用 Emoji 转换
- 禁止版本修订
- 数据库清理工具
- 头像 CDN 加速（含自定义地址）

## 安装说明
1. 下载插件压缩包。
2. 上传到 `/wp-content/plugins/wp-starter-kit/`。
3. 在 WordPress 后台启用 `WP Starter Kit`。
4. 在后台 `工具 -> 新手配置插件 / WP Starter Kit` 中使用。

## 系统要求
- WordPress >= 5.0
- PHP >= 7.2

## 更新日志
### 1.4.0 (2026-02-26)
- 新增：邮件发送模式支持 `SMTP` 与 `Resend API`
- 新增：测试邮件模板（主题/正文/内容类型）
- 新增：ID 替换工具（含列表筛选、分页、数据库概览、日志导出）
- 新增：系统状态页（版本、缓存、常量、目录权限）
- 优化：后台界面回归 WordPress 原生管理样式
- 优化：菜单多语言显示（中文显示“新手配置插件”）

### 1.3.0
- 增加：禁止版本修订、数据库清理

### 1.2.0
- 增加：禁用 Emoji 转换
- 修正：邮件配置输入问题

### 1.0.0
- 首次发布基础功能

## 贡献指南
- 问题反馈：[GitHub Issues](https://github.com/gentpan/wp-starter-kit/issues)
- 代码贡献：Fork 项目后提交 Pull Request

## 许可
Copyright (c) 2023-2026 西风

本插件基于 [GPL v2 或更高版本](https://www.gnu.org/licenses/gpl-2.0.html) 许可证发布。
