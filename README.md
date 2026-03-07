<div align="center">

# xTools

WordPress 运维与管理工具集插件，聚合邮件发送、CDN 加速、ID 替换、数据库管理、系统状态诊断与常规优化能力。

<p>
	<img src="https://img.shields.io/badge/version-2.0.1-2f7d32?style=for-the-badge" alt="Version">
	<img src="https://img.shields.io/badge/WordPress-5.0%2B-21759b?style=for-the-badge&logo=wordpress&logoColor=white" alt="WordPress">
	<img src="https://img.shields.io/badge/PHP-7.2%2B-777bb4?style=for-the-badge&logo=php&logoColor=white" alt="PHP">
	<img src="https://img.shields.io/badge/license-GPL%20v2%2B-dc8b00?style=for-the-badge" alt="License">
</p>

<p>
	<a href="https://github.com/gentpan/xTools"><img src="https://img.shields.io/badge/GitHub-Repository-111111?style=flat-square&logo=github&logoColor=white" alt="GitHub Repository"></a>
	<a href="https://github.com/gentpan/xTools/issues"><img src="https://img.shields.io/github/issues/gentpan/xTools?style=flat-square&logo=github" alt="GitHub Issues"></a>
	<a href="https://github.com/gentpan/xTools"><img src="https://img.shields.io/github/stars/gentpan/xTools?style=flat-square&logo=github" alt="GitHub Stars"></a>
</p>

<p>
	<a href="#功能概览">功能概览</a> |
	<a href="#快速开始">快速开始</a> |
	<a href="#系统要求">系统要求</a> |
	<a href="#更新日志">更新日志</a>
</p>

</div>

## 项目简介
xTools 是一个面向 WordPress 站点运维与管理者的工具集合插件，目标是把常见但分散的后台维护动作集中到一个插件内完成，减少多插件叠加带来的维护成本。

## 项目地址
- GitHub 仓库：https://github.com/gentpan/xTools
- 问题反馈：https://github.com/gentpan/xTools/issues
- Git 克隆：git clone https://github.com/gentpan/xTools.git

## 功能概览
| 模块 | 说明 |
| --- | --- |
| 邮件发送 | 支持 SMTP、Resend API、SendFlare API 三种发送模式，支持测试邮件、主题模板、HTML 和纯文本切换。 |
| 邮件模板中心 | 支持多模板管理、变量渲染、预览与测试发送，可设置默认模板。 |
| CDN 加速 | 支持 Google Fonts、jsDelivr、cdnjs、Gravatar 加速，并提供 BlueCDN、Yite 预设方案。 |
| ID 替换工具 | 提供可视化 ID 替换、筛选查询、分页列表、数据库概览与日志导出。 |
| 数据库管理 | 支持表结构读取、分页查看、单行 JSON 受控更新，以及一键备份和恢复。 |
| 系统状态诊断 | 展示 WordPress、PHP、MySQL 版本、缓存状态、关键常量与目录权限。 |
| 常规优化 | 包含禁用 Gutenberg、启用友情链接、去除 category、禁用 Emoji、禁止修订和数据库清理。 |

## 适用场景
- 需要统一管理 WordPress 邮件发送与模板测试的站点。
- 需要加速常见公共资源与头像访问的站点。
- 需要处理 ID 替换、数据库维护和系统诊断的运维场景。
- 希望减少安装多个小工具插件的后台维护成本。

## 快速开始
1. 下载插件压缩包，或使用 Git 克隆源码。
2. 将插件放入 `/wp-content/plugins/xtools/`。
3. 在 WordPress 后台启用 `xTools`。
4. 进入 `工具 -> xTools` 开始配置和使用。

Git 安装示例：

```bash
git clone https://github.com/gentpan/xTools.git /wp-content/plugins/xtools
```

## 主要能力
### 邮件发送与模板
- 支持 `SMTP`、`Resend API`、`SendFlare API` 三种发送模式。
- 支持测试邮件发送、测试主题/正文模板、HTML/纯文本切换。
- 可设置发件人邮箱与发件人名称。
- 支持邮件模板中心，多模板管理、变量渲染、预览与测试发送。

### CDN 加速
- 一键 CDN 方案预设：BlueCDN / Yite。
- Google Fonts 加速，替换 `fonts.googleapis.com` 与 `fonts.gstatic.com`。
- jsDelivr 加速，替换 `cdn.jsdelivr.net`。
- cdnjs 加速，替换 `cdnjs.cloudflare.com`。
- Gravatar 头像 CDN 加速，支持预设与自定义地址。
- 支持前端输出缓冲替换硬编码 CDN 地址。

### 内容与数据库工具
- ID 替换工具，支持可视化操作。
- 支持按文章类型、状态、关键词筛选与分页查看。
- 提供数据库概览，总记录、最大 ID、ID 空洞估算、按类型统计。
- 支持一键将列表项设为旧 ID 并执行替换。
- 数据库管理支持读取表字段、分页查看、单行 JSON 受控更新。
- 支持数据库一键备份与一键恢复。

### 系统状态与常规优化
- 显示 WordPress、PHP、MySQL 版本。
- 显示缓存状态、`advanced-cache.php`、关键常量与目录权限。
- 支持禁用 Gutenberg 编辑器和小工具。
- 支持启用友情链接、去除分类链接中的 `category`。
- 支持禁用 Emoji 转换、禁止版本修订与数据库清理工具。

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
