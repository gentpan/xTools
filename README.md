# WP Starter Kit

## 插件简介
WP Starter Kit 是一个为 WordPress 新手用户设计的插件，提供了一系列实用功能，包括邮件发送、关闭 Gutenberg 编辑器、头像 CDN 和去除分类链接。该插件旨在简化用户的 WordPress 使用体验。

## 功能特性
- **SMTP 邮件发送**
  - 支持主流邮件服务商
  - 提供邮件测试功能
  - 自定义发件人信息
- **编辑器优化**
  - 一键关闭 Gutenberg 编辑器
  - 恢复经典编辑器界面
  - 禁用 Gutenberg 小工具
- **头像 CDN 加速**
  - 支持多个公共 CDN 源
  - 包含国内外主流加速节点
  - 支持自定义 CDN 地址
- **WordPress 优化**
  - 去除分类链接中的 category
  - 支持开启友情链接功能
  - 更简洁的永久链接结构

## 系统要求
- WordPress 版本 >= 5.0
- PHP 版本 >= 7.2

## 安装说明
1. 下载插件压缩包
2. 将插件文件夹上传到 `/wp-content/plugins/` 目录
3. 在 WordPress 后台插件页面启用 "WP Starter Kit"
4. 在"工具"菜单下找到 "WP Starter Kit" 进行相关设置

## 使用指南
1. **邮件设置**
   - 配置 SMTP 服务器信息
   - 填写发件人信息
   - 使用测试功能验证配置

2. **编辑器设置**
   - 选择是否禁用 Gutenberg
   - 设置小工具编辑器偏好

3. **头像加速**
   - 选择合适的 CDN 源
   - 或填写自定义 CDN 地址

4. **其他优化**
   - 设置分类链接优化
   - 管理友情链接功能

## 常见问题
1. **邮件发送失败？**
   - 检查 SMTP 配置是否正确
   - 确认服务器端口是否开放
   - 验证账号密码是否有效

2. **头像加载缓慢？**
   - 尝试更换其他 CDN 源
   - 确认 CDN 地址可以正常访问

## 更新日志
### 1.0.0 (2025-05-27)
- 首次发布
- 实现基础功能
--禁用 Gutenberg 编辑器，恢复经典编辑器
--禁用 Gutenberg 小工具，恢复经典小工具
--启用友情链接
--去除分类链接中的 category
--头像CDN加速
-- SMTP发送邮件
- 优化使用体验

### 1.0.1 (2025-05-28)
- 增加 禁止版本修订 数据库清理

### 1.0.2 (2025-05-29)
- 增加 禁用Emoji转换为图片
- 修正 邮件配置输入框错误


## 贡献指南
欢迎提交问题报告和功能建议！
- 问题反馈：[GitHub Issues](https://github.com/gentpan/wp-starter-kit/issues)
- 代码贡献：Fork 本项目并提交 Pull Request

## 版权和许可
Copyright (c) 2023-2025 西风

* 插件主页：[https://xifeng.net/wp-starter-kit.html](https://xifeng.net/wp-starter-kit.html)
* 作者博客：[https://xifeng.net](https://xifeng.net)
* 联系邮箱：hi@xifeng.net

本插件基于 [GPL v2 或更高版本](https://www.gnu.org/licenses/gpl-2.0.html) 许可证发布。

## 致谢
感谢所有为本插件提供反馈和建议的用户！