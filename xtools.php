<?php

/**
 * Plugin Name: xTools
 * Plugin URI: https://github.com/gentpan/xTools
 * Description: WordPress 工具集插件，提供邮件发送、CDN 加速、ID 替换、数据库管理、系统状态诊断与常规优化功能。项目仓库：https://github.com/gentpan/xTools
 * Version: 2.0.1
 * Author: 西风
 * Author URI: https://xifeng.net
 * Update URI: https://github.com/gentpan/xTools
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * 
 * Copyright (c) 2023-2026 西风
 * xTools is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 * 
 * xTools is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 */

defined('ABSPATH') || exit;

define('XTOOLS_VERSION', '2.0.1');

/**
 * 注册设置菜单
 */
add_action('admin_menu', 'xtools_add_options_page');
function xtools_add_options_page()
{
    $menu_title = xtools_get_menu_title();

    add_submenu_page(
        'tools.php',          // 父级菜单slug (工具菜单)
        'xTools 设置', // 页面标题
        $menu_title,          // 菜单标题
        'manage_options',      // 权限
        'xtools',     // 菜单slug
        'xtools_options_page' // 回调函数
    );
}

/**
 * 菜单标题
 */
function xtools_get_menu_title()
{
    return 'xTools';
}

require_once plugin_dir_path(__FILE__) . 'includes/mail.php';
$xtools_mail_templates_file = plugin_dir_path(__FILE__) . 'includes/mail-templates.php';
if (file_exists($xtools_mail_templates_file)) {
    require_once $xtools_mail_templates_file;
}
$xtools_id_replace_file = plugin_dir_path(__FILE__) . 'includes/id-replace.php';
if (file_exists($xtools_id_replace_file)) {
    require_once $xtools_id_replace_file;
}
$xtools_system_status_file = plugin_dir_path(__FILE__) . 'includes/system-status.php';
if (file_exists($xtools_system_status_file)) {
    require_once $xtools_system_status_file;
}
$xtools_db_manager_file = plugin_dir_path(__FILE__) . 'includes/db-manager.php';
if (file_exists($xtools_db_manager_file)) {
    require_once $xtools_db_manager_file;
}

/**
 * 设置页面输出
 */
function xtools_options_page()
{
    $active_tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'general';
?>
    <div class="wrap">
        <div class="xt-shell">
            <!-- Hero -->
            <div class="xt-hero">
                <div class="xt-hero-main">
                    <div class="xt-page-head">
                        <h1>
                            <span class="xt-title-mark"><span class="dashicons dashicons-admin-tools"></span></span>
                            <span class="xt-title-text">xTools</span>
                        </h1>
                        <div class="xt-page-meta">
                            <span class="xt-badge xt-badge-kicker">XTOOLS</span>
                            <span class="xt-badge xt-badge-version"><?php echo esc_html('v' . XTOOLS_VERSION); ?></span>
                            <a class="xt-badge xt-badge-author" href="https://xifeng.net" target="_blank" rel="noopener noreferrer">西风</a>
                        </div>
                    </div>
                    <p class="xt-hero-description">WordPress 工具集：邮件发送（SMTP/Resend/SendFlare+模板中心）、CDN 加速（Google Fonts/jsDelivr/cdnjs/Gravatar）、ID 替换工具、数据库管理、系统状态诊断、编辑器与链接优化。</p>
                </div>
            </div>

            <!-- Tabs -->
            <div class="xt-tabs-wrap">
                <div class="xt-tabs" role="tablist">
                    <button type="button" class="xt-tab <?php echo $active_tab === 'general' ? 'is-active' : ''; ?>" data-tab="general"><span class="xt-tab-icon"><span class="dashicons dashicons-admin-generic"></span></span>常规设置</button>
                    <button type="button" class="xt-tab <?php echo $active_tab === 'smtp' ? 'is-active' : ''; ?>" data-tab="smtp"><span class="xt-tab-icon"><span class="dashicons dashicons-email-alt"></span></span>邮件配置</button>
                    <button type="button" class="xt-tab <?php echo $active_tab === 'mail-templates' ? 'is-active' : ''; ?>" data-tab="mail-templates"><span class="xt-tab-icon"><span class="dashicons dashicons-editor-table"></span></span>邮件模板</button>
                    <button type="button" class="xt-tab <?php echo $active_tab === 'id-replace' ? 'is-active' : ''; ?>" data-tab="id-replace"><span class="xt-tab-icon"><span class="dashicons dashicons-randomize"></span></span>ID 替换工具</button>
                    <button type="button" class="xt-tab <?php echo $active_tab === 'db-manager' ? 'is-active' : ''; ?>" data-tab="db-manager"><span class="xt-tab-icon"><span class="dashicons dashicons-database"></span></span>数据库管理</button>
                    <button type="button" class="xt-tab <?php echo $active_tab === 'system-status' ? 'is-active' : ''; ?>" data-tab="system-status"><span class="xt-tab-icon"><span class="dashicons dashicons-dashboard"></span></span>系统状态</button>
                </div>
            </div>

            <!-- Panel: General Settings -->
            <div class="xt-tab-panel <?php echo $active_tab === 'general' ? 'is-active' : ''; ?>" data-tab-panel="general">
                <form method="post" action="options.php">
                    <?php xtools_general_settings_tab(); ?>
                </form>
            </div>

            <!-- Panel: Mail Config -->
            <div class="xt-tab-panel <?php echo $active_tab === 'smtp' ? 'is-active' : ''; ?>" data-tab-panel="smtp">
                <form method="post" action="options.php">
                    <?php xtools_smtp_settings_tab(); ?>
                </form>
            </div>

            <!-- Panel: Mail Templates -->
            <div class="xt-tab-panel <?php echo $active_tab === 'mail-templates' ? 'is-active' : ''; ?>" data-tab-panel="mail-templates">
                <div class="xt-tab-panel-inner">
                <?php if (function_exists('xtools_render_mail_templates_tab')) : ?>
                    <?php xtools_render_mail_templates_tab(); ?>
                <?php else : ?>
                    <div class="notice notice-error inline"><p>邮件模板模块未加载。</p></div>
                <?php endif; ?>
                </div>
            </div>

            <!-- Panel: ID Replace -->
            <div class="xt-tab-panel <?php echo $active_tab === 'id-replace' ? 'is-active' : ''; ?>" data-tab-panel="id-replace">
                <div class="xt-tab-panel-inner">
                <?php if (function_exists('xtools_render_replace_post_id_tab')) : ?>
                    <?php xtools_render_replace_post_id_tab(); ?>
                <?php else : ?>
                    <div class="notice notice-error inline"><p>ID 替换模块未加载。</p></div>
                <?php endif; ?>
                </div>
            </div>

            <!-- Panel: DB Manager -->
            <div class="xt-tab-panel <?php echo $active_tab === 'db-manager' ? 'is-active' : ''; ?>" data-tab-panel="db-manager">
                <div class="xt-tab-panel-inner">
                <?php if (function_exists('xtools_render_db_manager_tab')) : ?>
                    <?php xtools_render_db_manager_tab(); ?>
                <?php else : ?>
                    <div class="notice notice-error inline"><p>数据库管理模块未加载。</p></div>
                <?php endif; ?>
                </div>
            </div>

            <!-- Panel: System Status -->
            <div class="xt-tab-panel <?php echo $active_tab === 'system-status' ? 'is-active' : ''; ?>" data-tab-panel="system-status">
                <div class="xt-tab-panel-inner">
                <?php if (function_exists('xtools_render_system_status_tab')) : ?>
                    <?php xtools_render_system_status_tab(); ?>
                <?php else : ?>
                    <div class="notice notice-error inline"><p>系统状态模块未加载。</p></div>
                <?php endif; ?>
                </div>
            </div>
        </div><!-- .xt-shell -->
    </div><!-- .wrap -->
<?php
}

/**
 * 常规设置选项卡
 */
function xtools_general_settings_tab()
{
    $options = get_option('xtools_options');
    $cdn_url = $options['cdn_url'] ?? '';
    $custom_cdn_url = $options['custom_cdn_url'] ?? '';
    $cdn_fonts = $options['cdn_fonts'] ?? '';
    $custom_cdn_fonts = $options['cdn_fonts_custom'] ?? '';
    $cdn_jsdelivr = $options['cdn_jsdelivr'] ?? '';
    $custom_cdn_jsdelivr = $options['cdn_jsdelivr_custom'] ?? '';
    $cdn_cdnjs = $options['cdn_cdnjs'] ?? '';
    $custom_cdn_cdnjs = $options['cdn_cdnjs_custom'] ?? '';
?>
    <?php settings_fields('xtools_group'); ?>
    <?php do_settings_sections('xtools_group'); ?>

    <div class="xt-settings-grid">
        <!-- Card 1: 常规优化 -->
        <div class="card">
            <h2>常规优化</h2>

            <div class="xt-form-row">
                <label><input type="checkbox" name="xtools_options[disable_editor]" value="1" <?php checked($options['disable_editor'] ?? '', 1); ?> /> 禁用 Gutenberg 编辑器</label>
                <p class="description">恢复经典编辑器</p>
            </div>

            <div class="xt-form-row">
                <label><input type="checkbox" name="xtools_options[disable_widgets]" value="1" <?php checked($options['disable_widgets'] ?? '', 1); ?> /> 禁用 Gutenberg 小工具</label>
                <p class="description">恢复经典小工具</p>
            </div>

            <div class="xt-form-row">
                <label><input type="checkbox" name="xtools_options[enable_links]" value="1" <?php checked($options['enable_links'] ?? '', 1); ?> /> 启用友情链接</label>
            </div>

            <div class="xt-form-row">
                <label><input type="checkbox" name="xtools_options[remove_category]" value="1" <?php checked($options['remove_category'] ?? '', 1); ?> /> 去除分类链接中的 category</label>
            </div>

            <div class="xt-form-row">
                <label><input type="checkbox" name="xtools_options[disable_emoji]" value="1" <?php checked($options['disable_emoji'] ?? '', 1); ?> /> 禁用 Emoji 转换</label>
                <p class="description">禁用 Emoji 表情转换为图片功能，提高页面加载速度</p>
            </div>

            <div class="xt-form-row">
                <label><input type="checkbox" name="xtools_options[disable_revisions]" value="1" <?php checked($options['disable_revisions'] ?? '', 1); ?> /> 禁止版本修订</label>
                <p class="description">禁止保存文章的历史版本，减少数据库存储空间</p>
                <p style="margin-top:8px">
                    <button type="button" id="clean_database" class="button xt-btn-icon">
                        <span class="dashicons dashicons-database"></span> 清理数据库
                    </button>
                    <span class="spinner"></span>
                </p>
                <div id="clean_result"></div>
            </div>

            <div class="xt-inline-actions">
                <?php submit_button('保存设置', 'primary', 'submit', false); ?>
            </div>
        </div>

        <!-- Card 2: CDN 加速 -->
        <div class="card">
            <h2>CDN 加速</h2>

            <div class="xt-form-row">
                <label>一键 CDN 方案</label>
                <select id="cdn_preset_select">
                    <option value="">— 选择预设方案 —</option>
                    <option value="bluecdn">BlueCDN（港/美/德服务器推荐）</option>
                    <option value="yite">Yite（国内/德服务器推荐）</option>
                    <option value="clear">清除所有 CDN 设置</option>
                </select>
                <p class="description">选择后自动填充下方所有 CDN 加速设置，保存后生效。</p>
            </div>

            <div class="xt-form-row">
                <label>头像 CDN 加速</label>
                <select name="xtools_options[cdn_url]" id="cdn_url_select">
                    <option value="">系统默认</option>
                    <option value="gravatar.bluecdn.com" <?php selected($cdn_url, 'gravatar.bluecdn.com'); ?>>gravatar.bluecdn.com（BlueCDN）</option>
                    <option value="gravatar.yite.net" <?php selected($cdn_url, 'gravatar.yite.net'); ?>>gravatar.yite.net（Yite 国内）</option>
                    <option value="gravatar.loli.net" <?php selected($cdn_url, 'gravatar.loli.net'); ?>>gravatar.loli.net</option>
                    <option value="cn.cravatar.com" <?php selected($cdn_url, 'cn.cravatar.com'); ?>>cn.cravatar.com</option>
                    <option value="weavatar.com" <?php selected($cdn_url, 'weavatar.com'); ?>>weavatar.com</option>
                    <option value="custom" <?php selected($cdn_url, 'custom'); ?>>自定义</option>
                </select>
                <p class="description">替换 Gravatar 头像服务 (gravatar.com) 加速访问。</p>
            </div>

            <div class="xt-form-row <?php echo $cdn_url === 'custom' ? '' : 'hidden'; ?>" id="custom_cdn_url_row">
                <label>自定义 CDN 地址</label>
                <input type="text" name="xtools_options[custom_cdn_url]" class="regular-text" value="<?php echo esc_attr($custom_cdn_url); ?>">
                <p class="description">请输入自定义 CDN 地址，例如：cdn.example.com</p>
            </div>

            <div class="xt-form-row">
                <label>Google Fonts 加速</label>
                <select name="xtools_options[cdn_fonts]" id="cdn_fonts_select">
                    <option value="">系统默认</option>
                    <option value="fonts.bluecdn.com" <?php selected($cdn_fonts, 'fonts.bluecdn.com'); ?>>fonts.bluecdn.com（BlueCDN）</option>
                    <option value="fonts.yite.net" <?php selected($cdn_fonts, 'fonts.yite.net'); ?>>fonts.yite.net（Yite 国内）</option>
                    <option value="custom" <?php selected($cdn_fonts, 'custom'); ?>>自定义</option>
                </select>
                <p class="description">替换 Google Fonts (fonts.googleapis.com / fonts.gstatic.com) 加速访问。</p>
            </div>

            <div class="xt-form-row <?php echo $cdn_fonts === 'custom' ? '' : 'hidden'; ?>" id="custom_cdn_fonts_row">
                <label>自定义 Fonts CDN</label>
                <input type="text" name="xtools_options[cdn_fonts_custom]" class="regular-text" value="<?php echo esc_attr($custom_cdn_fonts); ?>">
                <p class="description">请输入自定义 Google Fonts CDN 地址，例如：fonts.example.com</p>
            </div>

            <div class="xt-form-row">
                <label>jsDelivr 加速</label>
                <select name="xtools_options[cdn_jsdelivr]" id="cdn_jsdelivr_select">
                    <option value="">系统默认</option>
                    <option value="static.bluecdn.com" <?php selected($cdn_jsdelivr, 'static.bluecdn.com'); ?>>static.bluecdn.com（BlueCDN）</option>
                    <option value="cdn.yite.net" <?php selected($cdn_jsdelivr, 'cdn.yite.net'); ?>>cdn.yite.net（Yite 国内）</option>
                    <option value="custom" <?php selected($cdn_jsdelivr, 'custom'); ?>>自定义</option>
                </select>
                <p class="description">替换 jsDelivr (cdn.jsdelivr.net) 加速访问。</p>
            </div>

            <div class="xt-form-row <?php echo $cdn_jsdelivr === 'custom' ? '' : 'hidden'; ?>" id="custom_cdn_jsdelivr_row">
                <label>自定义 jsDelivr CDN</label>
                <input type="text" name="xtools_options[cdn_jsdelivr_custom]" class="regular-text" value="<?php echo esc_attr($custom_cdn_jsdelivr); ?>">
                <p class="description">请输入自定义 jsDelivr CDN 地址，例如：jsdelivr.example.com</p>
            </div>

            <div class="xt-form-row">
                <label>cdnjs 加速</label>
                <select name="xtools_options[cdn_cdnjs]" id="cdn_cdnjs_select">
                    <option value="">系统默认</option>
                    <option value="cdnjs.bluecdn.com" <?php selected($cdn_cdnjs, 'cdnjs.bluecdn.com'); ?>>cdnjs.bluecdn.com（BlueCDN）</option>
                    <option value="cdnjs.yite.net" <?php selected($cdn_cdnjs, 'cdnjs.yite.net'); ?>>cdnjs.yite.net（Yite 国内）</option>
                    <option value="custom" <?php selected($cdn_cdnjs, 'custom'); ?>>自定义</option>
                </select>
                <p class="description">替换 cdnjs (cdnjs.cloudflare.com) 加速访问。</p>
            </div>

            <div class="xt-form-row <?php echo $cdn_cdnjs === 'custom' ? '' : 'hidden'; ?>" id="custom_cdn_cdnjs_row">
                <label>自定义 cdnjs CDN</label>
                <input type="text" name="xtools_options[cdn_cdnjs_custom]" class="regular-text" value="<?php echo esc_attr($custom_cdn_cdnjs); ?>">
                <p class="description">请输入自定义 cdnjs CDN 地址，例如：cdnjs.example.com</p>
            </div>

            <div class="xt-inline-actions">
                <?php submit_button('保存设置', 'primary', 'submit', false); ?>
            </div>
        </div>
    </div>
<?php
}

/**
 * 注册设置
 */
add_action('admin_init', 'xtools_register_settings');
function xtools_register_settings()
{
    register_setting(
        'xtools_group', // Option group
        'xtools_options', // Option name
        'xtools_sanitize' // Sanitize
    );
}

/**
 * 验证和清理设置
 */
function xtools_sanitize($input)
{
    $sanitized = array();
    $sanitized['disable_editor'] = isset($input['disable_editor']) ? 1 : 0;
    $sanitized['disable_widgets'] = isset($input['disable_widgets']) ? 1 : 0;
    $sanitized['enable_links'] = isset($input['enable_links']) ? 1 : 0;
    $sanitized['remove_category'] = isset($input['remove_category']) ? 1 : 0;
    $sanitized['disable_emoji'] = isset( $input['disable_emoji'] ) ? 1 : 0;
    $sanitized['cdn_url'] = sanitize_text_field($input['cdn_url']);
    $sanitized['custom_cdn_url'] = sanitize_text_field($input['custom_cdn_url'] ?? '');
    $sanitized['cdn_fonts'] = sanitize_text_field($input['cdn_fonts'] ?? '');
    $sanitized['cdn_fonts_custom'] = sanitize_text_field($input['cdn_fonts_custom'] ?? '');
    $sanitized['cdn_jsdelivr'] = sanitize_text_field($input['cdn_jsdelivr'] ?? '');
    $sanitized['cdn_jsdelivr_custom'] = sanitize_text_field($input['cdn_jsdelivr_custom'] ?? '');
    $sanitized['cdn_cdnjs'] = sanitize_text_field($input['cdn_cdnjs'] ?? '');
    $sanitized['cdn_cdnjs_custom'] = sanitize_text_field($input['cdn_cdnjs_custom'] ?? '');
    $sanitized['disable_revisions'] = isset($input['disable_revisions']) ? 1 : 0;
    return $sanitized;
}

/**
 * 初始化设置
 */
register_activation_hook(__FILE__, 'xtools_activate');
function xtools_activate()
{
    $default_options = array(
        'disable_editor' => 0,
        'disable_widgets' => 0,
        'enable_links' => 0,
        'remove_category' => 0,
        'disable_emoji' => 0,
        'cdn_url' => '',
        'custom_cdn_url' => '',
        'cdn_fonts' => '',
        'cdn_fonts_custom' => '',
        'cdn_jsdelivr' => '',
        'cdn_jsdelivr_custom' => '',
        'cdn_cdnjs' => '',
        'cdn_cdnjs_custom' => '',
        'disable_revisions' => 0,
    );
    update_option('xtools_options', $default_options);

    $default_smtp_options = array(
        'mode' => 'smtp',
        'host' => '',
        'port' => '587',
        'encryption' => '',
        'username' => '',
        'password' => '',
        'from' => '',
        'from_name' => '',
        'resend_api_key' => '',
        'resend_from' => '',
        'sendflare_api_key' => '',
        'sendflare_from' => '',
        'test_subject' => '这是一封测试邮件',
        'test_message' => "恭喜你，邮件发送配置成功！这是一封测试邮件。",
        'test_content_type' => 'html'
    );
    update_option('xtools_smtp_options', $default_smtp_options);
    if (function_exists('xtools_get_default_mail_template')) {
        update_option('xtools_mail_templates', array(xtools_get_default_mail_template()));
    }

    // 激活时启用友情链接
    add_filter('pre_option_link_manager_enabled', '__return_true');
    update_option('link_manager_enabled', 1);

    // 激活时刷新重写规则
    if (get_option('xtools_options')['remove_category'] ?? false) {
        add_option('xtools_flush_rewrite_rules', true);
    }
}

// 在 init 钩子中检查并刷新重写规则
add_action('init', 'xtools_check_flush_rules');
function xtools_check_flush_rules()
{
    if (get_option('xtools_flush_rewrite_rules')) {
        flush_rewrite_rules();
        delete_option('xtools_flush_rewrite_rules');
    }
}

/**
 * 插件停用时
 */
register_deactivation_hook(__FILE__, 'xtools_deactivate');
function xtools_deactivate()
{
    // 停用时移除友情链接
    remove_filter('pre_option_link_manager_enabled', '__return_true');
    delete_option('link_manager_enabled');
    xtools_clear_cleanup_event();
}

/**
 * 插件卸载时
 */
register_uninstall_hook(__FILE__, 'xtools_uninstall');
function xtools_uninstall()
{
    // 插件卸载时，删除数据库中的所有插件数据
    delete_option('xtools_options');
    delete_option('xtools_smtp_options');
    delete_option('xtools_mail_templates');
    delete_option('xtools_flush_rewrite_rules');
}

/**
 * 应用设置
 */
add_action('init', 'xtools_apply_settings');
function xtools_apply_settings()
{
    $options = get_option('xtools_options');

    // 禁用 Gutenberg 编辑器
    if ($options['disable_editor'] ?? '') {
        add_filter('use_block_editor_for_post', '__return_false');
    }

    // 禁用 Gutenberg 小工具
    if ($options['disable_widgets'] ?? '') {
        add_filter('use_widgets_block_editor', '__return_false');
    }

    // 启用友情链接
    if ($options['enable_links'] ?? '') {
        add_filter('pre_option_link_manager_enabled', '__return_true');
    }

    // 去除分类链接中的 category
    if ($options['remove_category'] ?? '') {
        add_filter('category_link', 'xtools_remove_category_base');

        // 刷新重写规则
        if (get_option('xtools_flush_rewrite_rules')) {
            flush_rewrite_rules();
            delete_option('xtools_flush_rewrite_rules');
        }
    }

    // 头像 CDN 加速
    add_filter('get_avatar', 'xtools_avatar_cdn', 10, 5);

    // 禁用 Emoji 转换
    if ( $options['disable_emoji'] ?? false ) {
        remove_action('wp_head', 'print_emoji_detection_script', 7);
        remove_action('admin_print_scripts', 'print_emoji_detection_script');
        remove_action('wp_print_styles', 'print_emoji_styles');
        remove_action('admin_print_styles', 'print_emoji_styles');
        remove_filter('the_content_feed', 'wp_staticize_emoji');
        remove_filter('comment_text_rss', 'wp_staticize_emoji');
        remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
        
        // 禁用 DNS 预获取
        add_filter('emoji_svg_url', '__return_false');
        
        // 移除 TinyMCE 中的 emoji 插件
        add_filter('tiny_mce_plugins', function($plugins) {
            if (is_array($plugins)) {
                return array_diff($plugins, array('wpemoji'));
            }
            return array();
        });
    }

    // CDN 加速（Google Fonts / jsDelivr / cdnjs）
    if (xtools_has_cdn_replacements($options)) {
        add_filter('style_loader_src', 'xtools_cdn_replace_src', 99);
        add_filter('script_loader_src', 'xtools_cdn_replace_src', 99);
        add_action('template_redirect', 'xtools_cdn_ob_start', 0);
    }

    // 增强：禁止修订并持续清理自动草稿/历史修订
    if ($options['disable_revisions'] ?? false) {
        add_filter('wp_revisions_to_keep', 'xtools_disable_revisions_keep', 99, 2);
        xtools_maybe_schedule_cleanup_event();
    } else {
        xtools_clear_cleanup_event();
    }
}

function xtools_disable_revisions_keep($num, $post)
{
    return 0;
}

add_action('xtools_cleanup_drafts_revisions_event', 'xtools_cleanup_drafts_revisions_job');
function xtools_cleanup_drafts_revisions_job()
{
    global $wpdb;

    // 清理全部修订
    $wpdb->query("DELETE FROM {$wpdb->posts} WHERE post_type = 'revision'");

    // 清理 24 小时前的自动草稿，避免影响编辑中的草稿
    $wpdb->query(
        $wpdb->prepare(
            "DELETE FROM {$wpdb->posts} WHERE post_status = %s AND post_date < %s",
            'auto-draft',
            gmdate('Y-m-d H:i:s', time() - DAY_IN_SECONDS)
        )
    );
}

function xtools_maybe_schedule_cleanup_event()
{
    if (!wp_next_scheduled('xtools_cleanup_drafts_revisions_event')) {
        wp_schedule_event(time() + HOUR_IN_SECONDS, 'twicedaily', 'xtools_cleanup_drafts_revisions_event');
    }
}

function xtools_clear_cleanup_event()
{
    $timestamp = wp_next_scheduled('xtools_cleanup_drafts_revisions_event');
    if ($timestamp) {
        wp_unschedule_event($timestamp, 'xtools_cleanup_drafts_revisions_event');
    }
}

/**
 * 去除分类链接中的 category
 */
function xtools_remove_category_base($permalink)
{
    // 确保是分类链接
    if (strpos($permalink, '/category/') !== false) {
        $category_base = get_option('category_base');

        if (empty($category_base)) {
            $category_base = 'category';
        }

        // 移除 category 基础
        $permalink = str_replace('/' . $category_base . '/', '/', $permalink);
    }

    return $permalink;
}

/**
 * 头像 CDN 加速
 */
function xtools_avatar_cdn($avatar, $id_or_email, $size, $default, $alt)
{
    $options = get_option('xtools_options');
    $cdn_url = $options['cdn_url'] ?? '';
    $custom_cdn_url = $options['custom_cdn_url'] ?? '';

    if (!empty($cdn_url) && $cdn_url !== 'custom') {
        $avatar = str_replace(
            array('www.gravatar.com', '0.gravatar.com', '1.gravatar.com', '2.gravatar.com', 'secure.gravatar.com'),
            $cdn_url,
            $avatar
        );
    } elseif ($cdn_url === 'custom' && !empty($custom_cdn_url)) {
        $avatar = str_replace(
            array('www.gravatar.com', '0.gravatar.com', '1.gravatar.com', '2.gravatar.com', 'secure.gravatar.com'),
            $custom_cdn_url,
            $avatar
        );
    }

    return $avatar;
}

/**
 * CDN 加速：获取有效 CDN 地址
 */
function xtools_get_cdn_replace_value($options, $key, $custom_key)
{
    $value = $options[$key] ?? '';
    if ($value === 'custom') {
        return trim($options[$custom_key] ?? '');
    }
    return $value;
}

/**
 * CDN 加速：检查是否有配置
 */
function xtools_has_cdn_replacements($options)
{
    return !empty(xtools_get_cdn_replace_value($options, 'cdn_fonts', 'cdn_fonts_custom'))
        || !empty(xtools_get_cdn_replace_value($options, 'cdn_jsdelivr', 'cdn_jsdelivr_custom'))
        || !empty(xtools_get_cdn_replace_value($options, 'cdn_cdnjs', 'cdn_cdnjs_custom'));
}

/**
 * CDN 加速：构建替换映射表（静态缓存）
 */
function xtools_get_cdn_replacements()
{
    static $map = null;
    if ($map !== null) {
        return $map;
    }

    $options = get_option('xtools_options');
    $map = array();

    $fonts = xtools_get_cdn_replace_value($options, 'cdn_fonts', 'cdn_fonts_custom');
    if (!empty($fonts)) {
        $map['fonts.googleapis.com'] = $fonts;
        $map['fonts.gstatic.com'] = $fonts;
    }

    $jsdelivr = xtools_get_cdn_replace_value($options, 'cdn_jsdelivr', 'cdn_jsdelivr_custom');
    if (!empty($jsdelivr)) {
        $map['cdn.jsdelivr.net'] = $jsdelivr;
    }

    $cdnjs = xtools_get_cdn_replace_value($options, 'cdn_cdnjs', 'cdn_cdnjs_custom');
    if (!empty($cdnjs)) {
        $map['cdnjs.cloudflare.com'] = $cdnjs;
    }

    return $map;
}

/**
 * CDN 加速：替换 enqueue 的脚本/样式 URL
 */
function xtools_cdn_replace_src($src)
{
    $map = xtools_get_cdn_replacements();
    if (!empty($map)) {
        $src = str_replace(array_keys($map), array_values($map), $src);
    }
    return $src;
}

/**
 * CDN 加速：前端输出缓冲（替换硬编码的 CDN 地址）
 */
function xtools_cdn_ob_start()
{
    ob_start('xtools_cdn_replace_html');
}

function xtools_cdn_replace_html($html)
{
    $map = xtools_get_cdn_replacements();
    if (!empty($map)) {
        $html = str_replace(array_keys($map), array_values($map), $html);
    }
    return $html;
}

add_action('admin_enqueue_scripts', 'xtools_admin_scripts');
function xtools_admin_scripts($hook)
{
    if ('tools_page_xtools' != $hook) {
        return;
    }

    wp_enqueue_style('xtools-admin', plugin_dir_url(__FILE__) . 'assets/css/admin.css', array(), XTOOLS_VERSION);
    wp_enqueue_script('xtools-admin', plugin_dir_url(__FILE__) . 'assets/js/admin.js', array('jquery'), XTOOLS_VERSION, true);

    wp_localize_script('xtools-admin', 'xtoolsData', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'cleanNonce' => wp_create_nonce('xtools_clean_database'),
        'testEmailNonce' => wp_create_nonce('xtools_test_email'),
        'mailTemplateNonce' => wp_create_nonce('xtools_mail_template_manage'),
        'replaceIdNonce' => wp_create_nonce('xtools_replace_post_id'),
        'replaceListNonce' => wp_create_nonce('xtools_replace_id_list'),
        'dbManagerNonce' => wp_create_nonce('xtools_db_manager')
    ));
}

/**
 * 添加自定义重写规则
 */
add_action('init', 'xtools_category_rewrite_rules', 0);
function xtools_category_rewrite_rules()
{
    $options = get_option('xtools_options');

    if ($options['remove_category'] ?? false) {
        // 获取所有分类
        $categories = get_categories(array('hide_empty' => false));

        if ($categories) {
            foreach ($categories as $category) {
                // 添加自定义重写规则
                add_rewrite_rule(
                    $category->slug . '/?$',
                    'index.php?category_name=' . $category->slug,
                    'top'
                );

                // 添加分页支持
                add_rewrite_rule(
                    $category->slug . '/page/?([0-9]{1,})/?$',
                    'index.php?category_name=' . $category->slug . '&paged=$matches[1]',
                    'top'
                );
            }
        }
    }
}

/**
 * 在创建/编辑分类时刷新重写规则
 */
add_action('created_category', 'xtools_flush_rules');
add_action('edited_category', 'xtools_flush_rules');
add_action('delete_category', 'xtools_flush_rules');

function xtools_flush_rules()
{
    $options = get_option('xtools_options');

    if ($options['remove_category'] ?? false) {
        flush_rewrite_rules();
    }
}

/**
 * 清理数据库回调函数
 */
add_action('wp_ajax_xtools_clean_database', 'xtools_clean_database_callback');
function xtools_clean_database_callback()
{
    // 验证安全性
    check_ajax_referer('xtools_clean_database', 'nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error('权限不足');
    }

    global $wpdb;
    $clean_stats = array();

    // 清理文章修订
    $revisions = $wpdb->query("DELETE FROM $wpdb->posts WHERE post_type = 'revision'");
    $clean_stats['revisions'] = $revisions;

    // 清理自动草稿
    $auto_drafts = $wpdb->query("DELETE FROM $wpdb->posts WHERE post_status = 'auto-draft'");
    $clean_stats['auto_drafts'] = $auto_drafts;

    // 清理垃圾评论
    $spam_comments = $wpdb->query("DELETE FROM $wpdb->comments WHERE comment_approved = 'spam'");
    $clean_stats['spam_comments'] = $spam_comments;

    // 清理未链接的评论元数据
    $orphan_comment_meta = $wpdb->query("DELETE cm FROM $wpdb->commentmeta cm LEFT JOIN $wpdb->comments c ON c.comment_ID = cm.comment_id WHERE c.comment_ID IS NULL");
    $clean_stats['orphan_comment_meta'] = $orphan_comment_meta;

    // 清理未链接的文章元数据
    $orphan_post_meta = $wpdb->query("DELETE pm FROM $wpdb->postmeta pm LEFT JOIN $wpdb->posts p ON p.ID = pm.post_id WHERE p.ID IS NULL");
    $clean_stats['orphan_post_meta'] = $orphan_post_meta;

    // 优化数据表
    $tables = $wpdb->get_results("SHOW TABLES LIKE '$wpdb->prefix%'");
    foreach ($tables as $table) {
        $table_name = current((array)$table);
        $wpdb->query("OPTIMIZE TABLE $table_name");
    }

    // 返回清理结果
    $message = sprintf(
        '清理完成！共清理：%d 个文章修订、%d 个自动草稿、%d 条垃圾评论、%d 条孤立的评论元数据、%d 条孤立的文章元数据。',
        $clean_stats['revisions'],
        $clean_stats['auto_drafts'],
        $clean_stats['spam_comments'],
        $clean_stats['orphan_comment_meta'],
        $clean_stats['orphan_post_meta']
    );

    wp_send_json_success($message);
}
