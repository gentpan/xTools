# 🚀 WP Starter Kit

**WP Starter Kit** 是一个面向 **WordPress 新手用户** 的优化配置插件，集成了邮件发送、编辑器切换、头像 CDN 加速、分类链接优化等实用功能，帮助你快速上手 WordPress 建站。

---

## 🔧 功能特性
- **📧 SMTP 邮件发送**
  - 支持 QQ 邮箱、企业邮箱等主流邮件服务
  - 提供邮件测试工具
  - 自定义发件人信息

- **✏️ 编辑器优化**
  - 一键关闭 Gutenberg 编辑器
  - 恢复经典编辑器与传统小工具
  - 支持 WP 5.8+ 小工具面板优化

- **🖼️ 头像 CDN 加速**
  - 支持多个公共 CDN 源
  - 支持自定义 CDN 地址模板（如 `https://cdn.example.com/avatar/{hash}`）

- **🔗 WordPress 优化**
  - 自动去除分类链接中的 `category` 前缀
  - 支持开启友情链接功能
  - 禁用文章版本修订与草稿保存，保持数据库简洁
  - 禁用 Emoji 图片转换，加快页面加载速度

---

## 📦 系统要求
- WordPress >= 5.0  
- PHP >= 7.2  

---

## ⚡ 安装方法
1. [下载插件压缩包](https://xifeng.net/download/wp-starter-kit.zip) 或从 GitHub Release 获取最新版本  
2. 上传到 `/wp-content/plugins/` 目录  
3. 在 WordPress 后台插件页面启用 **WP Starter Kit**  
4. 在「工具」菜单中找到 **WP Starter Kit** 进行设置  

---

## 📘 使用指南
- **邮件设置**：配置 SMTP 服务器、端口、邮箱账号，支持测试功能  
- **编辑器设置**：选择是否禁用 Gutenberg，恢复经典编辑器和小工具  
- **头像加速**：选择合适的 CDN 源或填写自定义模板  
- **优化功能**：去除分类前缀、开启友情链接、关闭修订和 Emoji  

---

## ❓ 常见问题
**Q1. 邮件无法发送？**  
- 确认 SMTP 主机、端口、账号/授权码是否正确  
- 确认服务器是否开放邮件端口（465 / 587）  

**Q2. 头像加载很慢？**  
- 尝试更换为国内 CDN 源（如 `loli.net`、`BlueCDN`）  
- 使用自定义 CDN 地址模板  

---

## 📜 更新日志
### v1.3 (2025-08-27)
- 增加：禁用草稿保存与文章修订  
- 增加：禁用 Emoji 图片转换功能  
- 优化：SMTP 输入验证与设置页面 UI  

### v1.0.0 (2025-05-27)
- 首次发布  
- 基础功能实现：SMTP 邮件、编辑器优化、头像 CDN、分类优化、友情链接  

---

## 🤝 贡献
欢迎提交问题和建议：  
- 问题反馈：[GitHub Issues](https://github.com/gentpan/wp-starter-kit/issues)  
- 代码贡献：Fork 本项目并提交 PR  

---

## 📄 许可证
本插件基于 [GPL v2 或更高版本](https://www.gnu.org/licenses/gpl-2.0.html) 发布。  

---

## 🌐 项目信息
- 插件主页：[https://xifeng.net/wp-starter-kit.html](https://xifeng.net/wp-starter-kit.html)  
- 作者博客：[https://xifeng.net](https://xifeng.net)  
- 联系邮箱：hi@xifeng.net  

⭐ 如果觉得本插件有帮助，欢迎在 [GitHub](https://github.com/gentpan/wp-starter-kit) 上 **Star** 支持！
