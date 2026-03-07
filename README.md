# xTools

## 插件简介
xTools 是一个面向 WordPress 站点运维与管理者的工具集合插件，集中提供邮件发送配置、CDN 加速、ID 替换、数据库管理、系统状态诊断与常见性能优化功能。

## 项目地址
- GitHub 仓库：https://github.com/gentpan/xTools
- 问题反馈：https://github.com/gentpan/xTools/issues
- Git 克隆：git clone https://github.com/gentpan/xTools.git

## 当前功能
- 邮件发送配置
- 支持 `SMTP`、`Resend API`、`SendFlare API` 三种发送模式
- 支持测试邮件发送、测试主题/正文模板、HTML/纯文本切换
- 可设置发件人邮箱与发件人名称
- 邮件模板中心（多模板管理、HTML/纯文本正文、变量渲染、预览与测试发送）

- CDN 加速
- 一键 CDN 方案（BlueCDN / Yite 预设）
- Google Fonts 加速（替换 `fonts.googleapis.com` / `fonts.gstatic.com`）
- jsDelivr 加速（替换 `cdn.jsdelivr.net`）
- cdnjs 加速（替换 `cdnjs.cloudflare.com`）
- Gravatar 头像 CDN 加速（含多种预设与自定义地址）
- 支持前端输出缓冲替换硬编码 CDN 地址

- 内容与数据库工具
- ID 替换工具（可视化）
- 支持按文章类型/状态/关键词筛选与分页查看
- 提供数据库概览（总记录、最大 ID、ID 空洞估算、按类型统计）
- 一键将列表项"设为旧 ID"并执行替换
- 数据库管理工具（表字段读取、分页查看、单行 JSON 受控更新）
- 支持数据库一键备份与一键恢复

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

## 安装说明
1. 下载插件压缩包。
2. 上传到 `/wp-content/plugins/xtools/`。
3. 在 WordPress 后台启用 `xTools`。
4. 在后台 `工具 -> xTools` 中使用。

如需通过 Git 管理源码，可直接克隆 GitHub 仓库到插件目录：`/wp-content/plugins/xtools/`。

## 系统要求
- WordPress >= 5.0
- PHP >= 7.2

## 更新日志
### 2.0.1 (2026-03-07)
- 改进：插件主文件版本同步升级到 2.0.1，修正 README 与实际插件版本不一致的问题
- 改进：插件头部新增 GitHub 仓库地址与 `Update URI`，统一项目地址为 https://github.com/gentpan/xTools
- 改进：README 新增 GitHub 仓库、Issues 与 Git 克隆说明，便于通过 Git 管理插件源码

### 2.0.0 (2026-03-07)
- 新增：SendFlare API 邮件发送模式（SMTP / Resend / SendFlare 三选一）
- 新增：CDN 加速功能（Google Fonts / jsDelivr / cdnjs）
- 新增：一键 CDN 方案预设（BlueCDN 港/美/德、Yite 国内/德）
- 新增：Gravatar 头像新增 Yite 预设（gravatar.yite.net）
- 新增：前端输出缓冲替换硬编码 CDN 地址
- 改进：「SMTP 邮件设置」标签页更名为「邮件配置」
- 改进：JS 资源版本号跟随插件版本自动同步，解决浏览器缓存问题
- 改进：插件描述更新，反映全部当前功能

### 1.5.1 (2026-02-26)
- 新增：数据库管理页高风险提示（flash notice）
- 新增：数据库一键备份 / 一键恢复（站点前缀表）
- 新增：数据库管理分页读取、字段注释提示与单行受控更新
- 增强：禁止修订功能（`wp_revisions_to_keep = 0` + 定时清理修订/自动草稿）
- 修复：自定义头像 CDN 选项动态显示问题

### 1.5.0 (2026-02-26)
- 新增：邮件模板中心（多模板管理、HTML/纯文本正文、变量渲染、预览与测试发送）
- 增强：SMTP 测试发送支持选择模板与 JSON 变量

### 1.4.0 (2026-02-26)
- 新增：邮件发送模式支持 `SMTP` 与 `Resend API`
- 新增：测试邮件模板（主题/正文/内容类型）
- 新增：ID 替换工具（含列表筛选、分页、数据库概览、日志导出）
- 新增：系统状态页（版本、缓存、常量、目录权限）
- 优化：后台界面回归 WordPress 原生管理样式
- 优化：菜单标题统一为 xTools

### 1.3.0
- 增加：禁止版本修订、数据库清理

### 1.2.0
- 增加：禁用 Emoji 转换
- 修正：邮件配置输入问题

### 1.0.0
- 首次发布基础功能

## 贡献指南
- 问题反馈：[GitHub Issues](https://github.com/gentpan/xTools/issues)
- 代码贡献：Fork 项目后提交 Pull Request

## 许可
Copyright (c) 2023-2026 西风

本插件基于 [GPL v2 或更高版本](https://www.gnu.org/licenses/gpl-2.0.html) 许可证发布。
