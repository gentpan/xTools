<?php

/**
 * Plugin Name: WP Starter Kit
 * Plugin URI: https://xifeng.net/wp-starter-kit.html
 * Description: 新手必备工具集：邮件发送（SMTP/Resend+模板中心）、ID 替换工具、系统状态诊断、编辑器与链接优化。
 * Version: 1.5.1
 * Author: 西风
 * Author URI: https://xifeng.net
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * 
 * Copyright (c) 2023-2026 西风
 * WP Starter Kit is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 * 
 * WP Starter Kit is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 */

defined('ABSPATH') || exit;

/**
 * 注册设置菜单
 */
add_action('admin_menu', 'wp_starter_kit_add_options_page');
function wp_starter_kit_add_options_page()
{
    $menu_title = wp_starter_kit_get_menu_title();

    add_submenu_page(
        'tools.php',          // 父级菜单slug (工具菜单)
        'WP Starter Kit 设置', // 页面标题
        $menu_title,          // 菜单标题
        'manage_options',      // 权限
        'wp-starter-kit',     // 菜单slug
        'wp_starter_kit_options_page' // 回调函数
    );
}

/**
 * 菜单标题：中文显示“新手配置插件”，其他语言保持原英文。
 */
function wp_starter_kit_get_menu_title()
{
    $locale = function_exists('get_user_locale') ? get_user_locale() : get_locale();

    if (is_string($locale) && strpos($locale, 'zh_') === 0) {
        return '新手配置插件';
    }

    return 'WP Starter Kit';
}

require_once plugin_dir_path(__FILE__) . 'includes/mail.php';
$wp_starter_kit_mail_templates_file = plugin_dir_path(__FILE__) . 'includes/mail-templates.php';
if (file_exists($wp_starter_kit_mail_templates_file)) {
    require_once $wp_starter_kit_mail_templates_file;
}
$wp_starter_kit_id_replace_file = plugin_dir_path(__FILE__) . 'includes/id-replace.php';
if (file_exists($wp_starter_kit_id_replace_file)) {
    require_once $wp_starter_kit_id_replace_file;
}
$wp_starter_kit_system_status_file = plugin_dir_path(__FILE__) . 'includes/system-status.php';
if (file_exists($wp_starter_kit_system_status_file)) {
    require_once $wp_starter_kit_system_status_file;
}
$wp_starter_kit_db_manager_file = plugin_dir_path(__FILE__) . 'includes/db-manager.php';
if (file_exists($wp_starter_kit_db_manager_file)) {
    require_once $wp_starter_kit_db_manager_file;
}

/**
 * 设置页面输出
 */
function wp_starter_kit_options_page()
{
    $active_tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'general';
?>
    <div class="wrap">
        <h1>WP Starter Kit 设置</h1>

        <h2 class="nav-tab-wrapper">
            <a href="?page=wp-starter-kit&tab=general" class="nav-tab <?php echo $active_tab == 'general' ? 'nav-tab-active' : ''; ?>">常规设置</a>
            <a href="?page=wp-starter-kit&tab=smtp" class="nav-tab <?php echo $active_tab == 'smtp' ? 'nav-tab-active' : ''; ?>">SMTP 邮件设置</a>
            <a href="?page=wp-starter-kit&tab=mail-templates" class="nav-tab <?php echo $active_tab == 'mail-templates' ? 'nav-tab-active' : ''; ?>">邮件模板</a>
            <a href="?page=wp-starter-kit&tab=id-replace" class="nav-tab <?php echo $active_tab == 'id-replace' ? 'nav-tab-active' : ''; ?>">ID 替换工具</a>
            <a href="?page=wp-starter-kit&tab=db-manager" class="nav-tab <?php echo $active_tab == 'db-manager' ? 'nav-tab-active' : ''; ?>">数据库管理</a>
            <a href="?page=wp-starter-kit&tab=system-status" class="nav-tab <?php echo $active_tab == 'system-status' ? 'nav-tab-active' : ''; ?>">系统状态</a>
        </h2>

        <?php if ($active_tab === 'mail-templates') : ?>
            <?php if (function_exists('wp_starter_kit_render_mail_templates_tab')) : ?>
                <?php wp_starter_kit_render_mail_templates_tab(); ?>
            <?php else : ?>
                <div class="notice notice-error inline"><p>邮件模板模块未加载。</p></div>
            <?php endif; ?>
        <?php elseif ($active_tab === 'id-replace') : ?>
            <?php if (function_exists('wp_starter_kit_render_replace_post_id_tab')) : ?>
                <?php wp_starter_kit_render_replace_post_id_tab(); ?>
            <?php else : ?>
                <div class="notice notice-error inline"><p>ID 替换模块未加载。</p></div>
            <?php endif; ?>
        <?php elseif ($active_tab === 'db-manager') : ?>
            <?php if (function_exists('wp_starter_kit_render_db_manager_tab')) : ?>
                <?php wp_starter_kit_render_db_manager_tab(); ?>
            <?php else : ?>
                <div class="notice notice-error inline"><p>数据库管理模块未加载。</p></div>
            <?php endif; ?>
        <?php elseif ($active_tab === 'system-status') : ?>
            <?php if (function_exists('wp_starter_kit_render_system_status_tab')) : ?>
                <?php wp_starter_kit_render_system_status_tab(); ?>
            <?php else : ?>
                <div class="notice notice-error inline"><p>系统状态模块未加载。</p></div>
            <?php endif; ?>
        <?php else : ?>
            <form method="post" action="options.php">
                <table class="form-table">
                    <?php
                    if ($active_tab == 'general') {
                        // 常规设置
                        wp_starter_kit_general_settings_tab();
                    } elseif ($active_tab == 'smtp') {
                        // SMTP 设置
                        wp_starter_kit_smtp_settings_tab();
                    }
                    ?>
                </table>
                <?php submit_button('保存设置'); ?>
            </form>
        <?php endif; ?>
    </div>
<?php
}

/**
 * 常规设置选项卡
 */
function wp_starter_kit_general_settings_tab()
{
    $options = get_option('wp_starter_kit_options');
    $cdn_url = $options['cdn_url'] ?? '';
    $custom_cdn_url = $options['custom_cdn_url'] ?? '';
?>
    <?php settings_fields('wp_starter_kit_group'); ?>
    <?php do_settings_sections('wp_starter_kit_group'); ?>
    <tr valign="top">
        <th scope="row">禁用 Gutenberg 编辑器</th>
        <td>
            <label>
                <input type="checkbox" name="wp_starter_kit_options[disable_editor]" value="1" <?php checked($options['disable_editor'] ?? '', 1); ?> />
                禁用 Gutenberg 编辑器，恢复经典编辑器
            </label>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row">禁用 Gutenberg 小工具</th>
        <td>
            <label>
                <input type="checkbox" name="wp_starter_kit_options[disable_widgets]" value="1" <?php checked($options['disable_widgets'] ?? '', 1); ?> />
                禁用 Gutenberg 小工具，恢复经典小工具
            </label>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row">启用友情链接</th>
        <td>
            <label>
                <input type="checkbox" name="wp_starter_kit_options[enable_links]" value="1" <?php checked($options['enable_links'] ?? '', 1); ?> />
                启用友情链接
            </label>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row">去除分类链接中的 category</th>
        <td>
            <label>
                <input type="checkbox" name="wp_starter_kit_options[remove_category]" value="1" <?php checked($options['remove_category'] ?? '', 1); ?> />
                去除分类链接中的 category
            </label>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row">禁用 Emoji 转换</th>
        <td>
            <label>
                <input type="checkbox" name="wp_starter_kit_options[disable_emoji]" value="1" <?php checked($options['disable_emoji'] ?? '', 1); ?> />
                禁用 Emoji 表情转换为图片功能,提高页面加载速度
            </label>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row">禁止版本修订</th>
        <td>
            <label>
                <input type="checkbox" name="wp_starter_kit_options[disable_revisions]" value="1" <?php checked($options['disable_revisions'] ?? '', 1); ?> />
                禁止保存文章的历史版本,减少数据库存储空间
            </label>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row">数据库优化</th>
        <td>
            <button type="button" id="clean_database" class="button button-secondary">
                <span class="dashicons dashicons-database"></span>
                清理数据库
            </button>
            <span class="spinner"></span>
            <div id="clean_result"></div>
            <p class="description">清理数据库中的垃圾数据，包括文章修订、自动草稿、垃圾评论等。</p>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row">头像 CDN 加速</th>
        <td>
            <select name="wp_starter_kit_options[cdn_url]" id="cdn_url_select">
                <option value=""><?php esc_html_e('系统默认', 'wp-starter-kit'); ?></option>
                <option value="gravatar.bluecdn.com" <?php selected($cdn_url, 'gravatar.bluecdn.com'); ?>>gravatar.bluecdn.com</option>
                <option value="gravatar.loli.net" <?php selected($cdn_url, 'gravatar.loli.net'); ?>>gravatar.loli.net</option>
                <option value="cn.cravatar.com" <?php selected($cdn_url, 'cn.cravatar.com'); ?>>cn.cravatar.com</option>
                <option value="weavatar.com" <?php selected($cdn_url, 'weavatar.com'); ?>>weavatar.com</option>
                <option value="custom" <?php selected($cdn_url, 'custom'); ?>><?php esc_html_e('自定义', 'wp-starter-kit'); ?></option>
            </select>
            <p class="description">选择头像 CDN 加速地址，或自定义 CDN 地址。</p>
        </td>
    </tr>
    <tr valign="top" id="custom_cdn_url_row" class="<?php echo $cdn_url === 'custom' ? '' : 'hidden'; ?>">
        <th scope="row">自定义 CDN 地址</th>
        <td>
            <input type="text" name="wp_starter_kit_options[custom_cdn_url]" class="regular-text" value="<?php echo esc_attr($custom_cdn_url); ?>">
            <p class="description">请输入自定义 CDN 地址，例如：cdn.example.com</p>
        </td>
    </tr>
<?php
}

/**
 * 注册设置
 */
add_action('admin_init', 'wp_starter_kit_register_settings');
function wp_starter_kit_register_settings()
{
    register_setting(
        'wp_starter_kit_group', // Option group
        'wp_starter_kit_options', // Option name
        'wp_starter_kit_sanitize' // Sanitize
    );
}

/**
 * 验证和清理设置
 */
function wp_starter_kit_sanitize($input)
{
    $sanitized = array();
    $sanitized['disable_editor'] = isset($input['disable_editor']) ? 1 : 0;
    $sanitized['disable_widgets'] = isset($input['disable_widgets']) ? 1 : 0;
    $sanitized['enable_links'] = isset($input['enable_links']) ? 1 : 0;
    $sanitized['remove_category'] = isset($input['remove_category']) ? 1 : 0;
    $sanitized['disable_emoji'] = isset( $input['disable_emoji'] ) ? 1 : 0;
    $sanitized['cdn_url'] = sanitize_text_field($input['cdn_url']);
    $sanitized['custom_cdn_url'] = sanitize_text_field($input['custom_cdn_url'] ?? '');
    $sanitized['disable_revisions'] = isset($input['disable_revisions']) ? 1 : 0;
    return $sanitized;
}

/**
 * 初始化设置
 */
register_activation_hook(__FILE__, 'wp_starter_kit_activate');
function wp_starter_kit_activate()
{
    $default_options = array(
        'disable_editor' => 0,
        'disable_widgets' => 0,
        'enable_links' => 0,
        'remove_category' => 0,
        'disable_emoji' => 0,
        'cdn_url' => '',
        'custom_cdn_url' => '',
        'disable_revisions' => 0,
    );
    update_option('wp_starter_kit_options', $default_options);

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
        'test_subject' => '这是一封测试邮件',
        'test_message' => "恭喜你，邮件发送配置成功！这是一封测试邮件。",
        'test_content_type' => 'html'
    );
    update_option('wp_starter_kit_smtp_options', $default_smtp_options);
    if (function_exists('wp_starter_kit_get_default_mail_template')) {
        update_option('wp_starter_kit_mail_templates', array(wp_starter_kit_get_default_mail_template()));
    }

    // 激活时启用友情链接
    add_filter('pre_option_link_manager_enabled', '__return_true');
    update_option('link_manager_enabled', 1);

    // 激活时刷新重写规则
    if (get_option('wp_starter_kit_options')['remove_category'] ?? false) {
        add_option('wp_starter_kit_flush_rewrite_rules', true);
    }
}

// 在 init 钩子中检查并刷新重写规则
add_action('init', 'wp_starter_kit_check_flush_rules');
function wp_starter_kit_check_flush_rules()
{
    if (get_option('wp_starter_kit_flush_rewrite_rules')) {
        flush_rewrite_rules();
        delete_option('wp_starter_kit_flush_rewrite_rules');
    }
}

/**
 * 插件停用时
 */
register_deactivation_hook(__FILE__, 'wp_starter_kit_deactivate');
function wp_starter_kit_deactivate()
{
    // 停用时移除友情链接
    remove_filter('pre_option_link_manager_enabled', '__return_true');
    delete_option('link_manager_enabled');
    wp_starter_kit_clear_cleanup_event();
}

/**
 * 插件卸载时
 */
register_uninstall_hook(__FILE__, 'wp_starter_kit_uninstall');
function wp_starter_kit_uninstall()
{
    // 插件卸载时，删除数据库中的所有插件数据
    delete_option('wp_starter_kit_options');
    delete_option('wp_starter_kit_smtp_options');
    delete_option('wp_starter_kit_mail_templates');
    delete_option('wp_starter_kit_flush_rewrite_rules');
}

/**
 * 应用设置
 */
add_action('init', 'wp_starter_kit_apply_settings');
function wp_starter_kit_apply_settings()
{
    $options = get_option('wp_starter_kit_options');

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
        add_filter('category_link', 'wp_starter_kit_remove_category_base');

        // 刷新重写规则
        if (get_option('wp_starter_kit_flush_rewrite_rules')) {
            flush_rewrite_rules();
            delete_option('wp_starter_kit_flush_rewrite_rules');
        }
    }

    // 头像 CDN 加速
    add_filter('get_avatar', 'wp_starter_kit_avatar_cdn', 10, 5);

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

    // 增强：禁止修订并持续清理自动草稿/历史修订
    if ($options['disable_revisions'] ?? false) {
        add_filter('wp_revisions_to_keep', 'wp_starter_kit_disable_revisions_keep', 99, 2);
        wp_starter_kit_maybe_schedule_cleanup_event();
    } else {
        wp_starter_kit_clear_cleanup_event();
    }
}

function wp_starter_kit_disable_revisions_keep($num, $post)
{
    return 0;
}

add_action('wp_starter_kit_cleanup_drafts_revisions_event', 'wp_starter_kit_cleanup_drafts_revisions_job');
function wp_starter_kit_cleanup_drafts_revisions_job()
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

function wp_starter_kit_maybe_schedule_cleanup_event()
{
    if (!wp_next_scheduled('wp_starter_kit_cleanup_drafts_revisions_event')) {
        wp_schedule_event(time() + HOUR_IN_SECONDS, 'twicedaily', 'wp_starter_kit_cleanup_drafts_revisions_event');
    }
}

function wp_starter_kit_clear_cleanup_event()
{
    $timestamp = wp_next_scheduled('wp_starter_kit_cleanup_drafts_revisions_event');
    if ($timestamp) {
        wp_unschedule_event($timestamp, 'wp_starter_kit_cleanup_drafts_revisions_event');
    }
}

/**
 * 去除分类链接中的 category
 */
function wp_starter_kit_remove_category_base($permalink)
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
function wp_starter_kit_avatar_cdn($avatar, $id_or_email, $size, $default, $alt)
{
    $options = get_option('wp_starter_kit_options');
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

add_action('admin_enqueue_scripts', 'wp_starter_kit_admin_scripts');
function wp_starter_kit_admin_scripts($hook)
{
    if ('tools_page_wp-starter-kit' != $hook) {
        return;
    }

    wp_enqueue_script('wp-starter-kit-admin', plugin_dir_url(__FILE__) . 'assets/js/admin.js', array('jquery'), '1.0.0', true);

    wp_localize_script('wp-starter-kit-admin', 'wpStarterKit', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'cleanNonce' => wp_create_nonce('wp_starter_kit_clean_database'),
        'testEmailNonce' => wp_create_nonce('wp_starter_kit_test_email'),
        'mailTemplateNonce' => wp_create_nonce('wp_starter_kit_mail_template_manage'),
        'replaceIdNonce' => wp_create_nonce('wp_starter_kit_replace_post_id'),
        'replaceListNonce' => wp_create_nonce('wp_starter_kit_replace_id_list'),
        'dbManagerNonce' => wp_create_nonce('wp_starter_kit_db_manager')
    ));
}

/**
 * 添加自定义重写规则
 */
add_action('init', 'wp_starter_kit_category_rewrite_rules', 0);
function wp_starter_kit_category_rewrite_rules()
{
    $options = get_option('wp_starter_kit_options');

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
add_action('created_category', 'wp_starter_kit_flush_rules');
add_action('edited_category', 'wp_starter_kit_flush_rules');
add_action('delete_category', 'wp_starter_kit_flush_rules');

function wp_starter_kit_flush_rules()
{
    $options = get_option('wp_starter_kit_options');

    if ($options['remove_category'] ?? false) {
        flush_rewrite_rules();
    }
}

/**
 * 清理数据库回调函数
 */
add_action('wp_ajax_wp_starter_kit_clean_database', 'wp_starter_kit_clean_database_callback');
function wp_starter_kit_clean_database_callback()
{
    // 验证安全性
    check_ajax_referer('wp_starter_kit_clean_database', 'nonce');

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
