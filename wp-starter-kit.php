<?php
/**
 * Plugin Name: WP Starter Kit
 * Plugin URI: https://westlife.net/wp-starter-kit.html
 * Description: 新手必备工具集：邮件发送、回复默认编辑器、头像CDN加速、去除分类链接category。
 * Version: 1.0.0
 * Author: 西风
 * Author URI: https://westlife.net
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * 
 * Copyright (c) 2023-2025 西风
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
function wp_starter_kit_add_options_page() {
    add_submenu_page(
        'tools.php',          // 父级菜单slug (工具菜单)
        'WP Starter Kit 设置', // 页面标题
        'WP Starter Kit',     // 菜单标题
        'manage_options',      // 权限
        'wp-starter-kit',     // 菜单slug
        'wp_starter_kit_options_page' // 回调函数
    );
}

require_once plugin_dir_path( __FILE__ ) . 'includes/mail.php';

/**
 * 设置页面输出
 */
function wp_starter_kit_options_page() {
    $active_tab = $_GET['tab'] ?? 'general';
    ?>
    <div class="wrap">
        <h1>WP Starter Kit 设置</h1>

        <h2 class="nav-tab-wrapper">
            <a href="?page=wp-starter-kit&tab=general" class="nav-tab <?php echo $active_tab == 'general' ? 'nav-tab-active' : ''; ?>">常规设置</a>
            <a href="?page=wp-starter-kit&tab=smtp" class="nav-tab <?php echo $active_tab == 'smtp' ? 'nav-tab-active' : ''; ?>">SMTP 邮件设置</a>
        </h2>

        <form method="post" action="options.php">
            <table class="form-table">
            <?php
            if ( $active_tab == 'general' ) {
                // 常规设置
                wp_starter_kit_general_settings_tab();
            } elseif ( $active_tab == 'smtp' ) {
                // SMTP 设置
                wp_starter_kit_smtp_settings_tab();
            }
            ?>
            </table>
            <?php submit_button( '保存设置' ); ?>
        </form>
    </div>
    <?php
}

/**
 * 常规设置选项卡
 */
function wp_starter_kit_general_settings_tab() {
    $options = get_option( 'wp_starter_kit_options' );
    $cdn_url = $options['cdn_url'] ?? '';
    $custom_cdn_url = $options['custom_cdn_url'] ?? '';
    ?>
        <?php settings_fields( 'wp_starter_kit_group' ); ?>
        <?php do_settings_sections( 'wp_starter_kit_group' ); ?>
        <tr valign="top">
            <th scope="row">禁用 Gutenberg 编辑器</th>
            <td>
                <label class="switch">
                    <input type="checkbox" name="wp_starter_kit_options[disable_editor]" value="1" <?php checked( $options['disable_editor'] ?? '', 1 ); ?> />
                    <span class="slider round"></span>
                </label>
                禁用 Gutenberg 编辑器，恢复经典编辑器
            </td>
        </tr>
        <tr valign="top">
            <th scope="row">禁用 Gutenberg 小工具</th>
            <td>
                <label class="switch">
                    <input type="checkbox" name="wp_starter_kit_options[disable_widgets]" value="1" <?php checked( $options['disable_widgets'] ?? '', 1 ); ?> />
                    <span class="slider round"></span>
                </label>
                禁用 Gutenberg 小工具，恢复经典小工具
            </td>
        </tr>
        <tr valign="top">
            <th scope="row">启用友情链接</th>
            <td>
                <label class="switch">
                    <input type="checkbox" name="wp_starter_kit_options[enable_links]" value="1" <?php checked( $options['enable_links'] ?? '', 1 ); ?> />
                    <span class="slider round"></span>
                </label>
                启用友情链接
            </td>
        </tr>
        <tr valign="top">
            <th scope="row">去除分类链接中的 category</th>
            <td>
                <label class="switch">
                    <input type="checkbox" name="wp_starter_kit_options[remove_category]" value="1" <?php checked( $options['remove_category'] ?? '', 1 ); ?> />
                    <span class="slider round"></span>
                </label>
                去除分类链接中的 category
            </td>
        </tr>
        <tr valign="top">
            <th scope="row">头像 CDN 加速</th>
            <td>
                <select name="wp_starter_kit_options[cdn_url]" id="cdn_url_select">
                    <option value=""><?php esc_html_e( '系统默认', 'wp-starter-kit' ); ?></option>
                    <option value="gravatar.bluecdn.com" <?php selected( $cdn_url, 'gravatar.bluecdn.com' ); ?>>gravatar.bluecdn.com</option>
                    <option value="gravatar.cdn.ga" <?php selected( $cdn_url, 'gravatar.cdn.ga' ); ?>>gravatar.cdn.ga</option>
                    <option value="gravatar.ga" <?php selected( $cdn_url, 'gravatar.ga' ); ?>>gravatar.ga</option>
                    <option value="avatar.ga" <?php selected( $cdn_url, 'avatar.ga' ); ?>>avatar.ga</option>
                    <option value="gravatar.loli.net" <?php selected( $cdn_url, 'gravatar.loli.net' ); ?>>gravatar.loli.net</option>
                    <option value="cn.cravatar.com" <?php selected( $cdn_url, 'cn.cravatar.com' ); ?>>cn.cravatar.com</option>
                    <option value="weavatar.com" <?php selected( $cdn_url, 'weavatar.com' ); ?>>weavatar.com</option>
                    <option value="custom" <?php selected( $cdn_url, 'custom' ); ?>><?php esc_html_e( '自定义', 'wp-starter-kit' ); ?></option>
                </select>
                <p class="description">选择头像 CDN 加速地址，或自定义 CDN 地址。</p>
            </td>
        </tr>
        <?php if ( $cdn_url === 'custom' ) : ?>
            <tr valign="top" id="custom_cdn_url_row">
                <th scope="row">自定义 CDN 地址</th>
                <td>
                    <input type="text" name="wp_starter_kit_options[custom_cdn_url]" class="regular-text" value="<?php echo esc_attr( $custom_cdn_url ); ?>">
                    <p class="description">请输入自定义 CDN 地址，例如：cdn.example.com</p>
                </td>
            </tr>
        <?php endif; ?>
    <?php
}

/**
 * 注册设置
 */
add_action('admin_init', 'wp_starter_kit_register_settings');
function wp_starter_kit_register_settings() {
    register_setting(
        'wp_starter_kit_group', // Option group
        'wp_starter_kit_options', // Option name
        'wp_starter_kit_sanitize' // Sanitize
    );
}

/**
 * 验证和清理设置
 */
function wp_starter_kit_sanitize( $input ) {
    $sanitized = array();
    $sanitized['disable_editor'] = isset( $input['disable_editor'] ) ? 1 : 0;
    $sanitized['disable_widgets'] = isset( $input['disable_widgets'] ) ? 1 : 0;
    $sanitized['enable_links'] = isset( $input['enable_links'] ) ? 1 : 0;
    $sanitized['remove_category'] = isset( $input['remove_category'] ) ? 1 : 0;
    $sanitized['cdn_url'] = sanitize_text_field( $input['cdn_url'] );
    $sanitized['custom_cdn_url'] = sanitize_text_field( $input['custom_cdn_url'] ?? '' );
    return $sanitized;
}

/**
 * 初始化设置
 */
register_activation_hook( __FILE__, 'wp_starter_kit_activate' );
function wp_starter_kit_activate() {
    // 插件激活时，重新加载设置
    $default_options = array(
        'disable_editor' => 1,
        'disable_widgets' => 1,
        'enable_links' => 1,
        'remove_category' => 1,
        'cdn_url' => '',
        'custom_cdn_url' => '',
    );
    update_option( 'wp_starter_kit_options', $default_options );

    $default_smtp_options = array(
        'host' => '',
        'port' => '587',
        'encryption' => '',
        'username' => '',
        'password' => '',
        'from' => '',
        'from_name' => ''
    );
    update_option( 'wp_starter_kit_smtp_options', $default_smtp_options );

    // 激活时启用友情链接
    add_filter( 'pre_option_link_manager_enabled', '__return_true' );
    update_option( 'link_manager_enabled', 1 );

    // 激活时刷新重写规则
    if (get_option('wp_starter_kit_options')['remove_category'] ?? false) {
        add_option('wp_starter_kit_flush_rewrite_rules', true);
    }
}

// 在 init 钩子中检查并刷新重写规则
add_action('init', 'wp_starter_kit_check_flush_rules');
function wp_starter_kit_check_flush_rules() {
    if (get_option('wp_starter_kit_flush_rewrite_rules')) {
        flush_rewrite_rules();
        delete_option('wp_starter_kit_flush_rewrite_rules');
    }
}

/**
 * 插件停用时
 */
register_deactivation_hook( __FILE__, 'wp_starter_kit_deactivate' );
function wp_starter_kit_deactivate() {
    // 停用时移除友情链接
    remove_filter( 'pre_option_link_manager_enabled', '__return_true' );
    delete_option( 'link_manager_enabled' );
}

/**
 * 插件卸载时
 */
register_uninstall_hook( __FILE__, 'wp_starter_kit_uninstall' );
function wp_starter_kit_uninstall() {
    // 插件卸载时，删除数据库中的所有插件数据
    delete_option( 'wp_starter_kit_options' );
    delete_option( 'wp_starter_kit_smtp_options' );
    delete_option( 'wp_starter_kit_flush_rewrite_rules' );
}

/**
 * 添加 CSS 样式
 */
add_action( 'admin_enqueue_scripts', 'wp_starter_kit_admin_style' );
function wp_starter_kit_admin_style( $hook ) {
    // 修改钩子判断,tools_page_ 是工具菜单下子页面的前缀
    if ( 'tools_page_wp-starter-kit' != $hook ) {
        return;
    }
    wp_enqueue_style( 'wp-starter-kit-admin', plugin_dir_url( __FILE__ ) . 'assets/css/admin.css', array(), '1.0.0' );
}
/**
 * 应用设置
 */
add_action( 'init', 'wp_starter_kit_apply_settings' );
function wp_starter_kit_apply_settings() {
    $options = get_option( 'wp_starter_kit_options' );

    // 禁用 Gutenberg 编辑器
    if ( $options['disable_editor'] ?? '' ) {
        add_filter( 'use_block_editor_for_post', '__return_false' );
    }

    // 禁用 Gutenberg 小工具
    if ( $options['disable_widgets'] ?? '' ) {
        add_filter( 'use_widgets_block_editor', '__return_false' );
    }

    // 启用友情链接
    if ( $options['enable_links'] ?? '' ) {
        add_filter( 'pre_option_link_manager_enabled', '__return_true' );
    }

    // 去除分类链接中的 category
    if ( $options['remove_category'] ?? '' ) {
        add_filter( 'category_link', 'wp_starter_kit_remove_category_base' );

        // 刷新重写规则
        if ( get_option( 'wp_starter_kit_flush_rewrite_rules' ) ) {
            flush_rewrite_rules();
            delete_option( 'wp_starter_kit_flush_rewrite_rules' );
        }
    }

    // 头像 CDN 加速
    add_filter( 'get_avatar', 'wp_starter_kit_avatar_cdn', 10, 5 );
}

/**
 * 去除分类链接中的 category
 */
function wp_starter_kit_remove_category_base( $permalink ) {
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
function wp_starter_kit_avatar_cdn( $avatar, $id_or_email, $size, $default, $alt ) {
    $options = get_option( 'wp_starter_kit_options' );
    $cdn_url = $options['cdn_url'] ?? '';
    $custom_cdn_url = $options['custom_cdn_url'] ?? '';

    if ( !empty( $cdn_url ) && $cdn_url !== 'custom' ) {
        $avatar = str_replace(
            array( 'www.gravatar.com', '0.gravatar.com', '1.gravatar.com', '2.gravatar.com', 'secure.gravatar.com' ),
            $cdn_url,
            $avatar
        );
    } elseif ( $cdn_url === 'custom' && !empty( $custom_cdn_url ) ) {
        $avatar = str_replace(
            array( 'www.gravatar.com', '0.gravatar.com', '1.gravatar.com', '2.gravatar.com', 'secure.gravatar.com' ),
            $custom_cdn_url,
            $avatar
        );
    }

    return $avatar;
}

add_action( 'admin_enqueue_scripts', 'wp_starter_kit_admin_scripts' );
function wp_starter_kit_admin_scripts( $hook ) {
    if ( 'toplevel_page_wp-starter-kit' != $hook ) {
        return;
    }
    wp_enqueue_style( 'wp-starter-kit-admin', plugin_dir_url( __FILE__ ) . 'assets/css/admin.css', array(), '1.0.0' );
}

/**
 * 添加自定义重写规则
 */
add_action('init', 'wp_starter_kit_category_rewrite_rules', 0);
function wp_starter_kit_category_rewrite_rules() {
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

function wp_starter_kit_flush_rules() {
    $options = get_option('wp_starter_kit_options');
    
    if ($options['remove_category'] ?? false) {
        flush_rewrite_rules();
    }
}