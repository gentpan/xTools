<?php
/**
 * 系统状态页
 *
 * @package xTools
 */

defined('ABSPATH') || exit;

function xtools_render_system_status_tab()
{
    $rows = xtools_get_system_status_rows();
    ?>
    <h2>系统状态总览</h2>
    <p class="description">用于排查环境问题，数据为当前实时状态。</p>

    <?php foreach ($rows as $section => $items) : ?>
        <h3><?php echo esc_html($section); ?></h3>
        <table class="widefat striped" role="presentation">
            <tbody>
                <?php foreach ($items as $item) : ?>
                    <tr>
                        <td><strong><?php echo esc_html($item['label']); ?></strong></td>
                        <td><?php echo wp_kses_post($item['value']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endforeach; ?>
    <?php
}

function xtools_get_system_status_rows()
{
    global $wpdb;

    $php_version = phpversion();
    $mysql_version = $wpdb->db_version();
    $wp_version = get_bloginfo('version');
    $memory_limit = ini_get('memory_limit');
    $max_exec_time = ini_get('max_execution_time');

    $object_cache_enabled = wp_using_ext_object_cache();
    $advanced_cache_dropin = file_exists(WP_CONTENT_DIR . '/advanced-cache.php');

    $constants = array(
        'WP_DEBUG' => defined('WP_DEBUG') ? (WP_DEBUG ? 'true' : 'false') : '(未定义)',
        'WP_DEBUG_LOG' => defined('WP_DEBUG_LOG') ? (WP_DEBUG_LOG ? 'true' : 'false') : '(未定义)',
        'SCRIPT_DEBUG' => defined('SCRIPT_DEBUG') ? (SCRIPT_DEBUG ? 'true' : 'false') : '(未定义)',
        'WP_CACHE' => defined('WP_CACHE') ? (WP_CACHE ? 'true' : 'false') : '(未定义)',
        'DISALLOW_FILE_EDIT' => defined('DISALLOW_FILE_EDIT') ? (DISALLOW_FILE_EDIT ? 'true' : 'false') : '(未定义)',
        'WP_MEMORY_LIMIT' => defined('WP_MEMORY_LIMIT') ? WP_MEMORY_LIMIT : '(未定义)',
        'WP_MAX_MEMORY_LIMIT' => defined('WP_MAX_MEMORY_LIMIT') ? WP_MAX_MEMORY_LIMIT : '(未定义)',
    );

    $paths = array(
        'ABSPATH' => ABSPATH,
        'WP_CONTENT_DIR' => WP_CONTENT_DIR,
        'Uploads 目录' => wp_get_upload_dir()['basedir'],
        'Plugins 目录' => WP_PLUGIN_DIR,
        'Themes 目录' => get_theme_root(),
    );

    $path_rows = array();
    foreach ($paths as $label => $path) {
        $path_rows[] = array(
            'label' => $label,
            'value' => esc_html($path) . '<br><small>' . esc_html(xtools_get_path_permissions_text($path)) . '</small>',
        );
    }

    $constant_rows = array();
    foreach ($constants as $key => $value) {
        $constant_rows[] = array(
            'label' => $key,
            'value' => esc_html((string) $value),
        );
    }

    return array(
        '版本信息' => array(
            array('label' => 'WordPress', 'value' => esc_html($wp_version)),
            array('label' => 'PHP', 'value' => esc_html($php_version)),
            array('label' => 'MySQL', 'value' => esc_html($mysql_version)),
            array('label' => '内存限制', 'value' => esc_html((string) $memory_limit)),
            array('label' => '最大执行时间', 'value' => esc_html((string) $max_exec_time . ' 秒')),
        ),
        '缓存状态' => array(
            array('label' => '对象缓存', 'value' => $object_cache_enabled ? '已启用（外部对象缓存）' : '未启用（默认非持久缓存）'),
            array('label' => 'advanced-cache.php', 'value' => $advanced_cache_dropin ? '存在（可能启用页面缓存）' : '不存在'),
        ),
        '关键常量' => $constant_rows,
        '目录权限' => $path_rows,
    );
}

function xtools_get_path_permissions_text($path)
{
    if (empty($path) || !file_exists($path)) {
        return '路径不存在';
    }

    $readable = is_readable($path) ? '可读' : '不可读';
    $writable = is_writable($path) ? '可写' : '不可写';
    $perms = @fileperms($path);
    $perm_octal = $perms ? substr(sprintf('%o', $perms), -4) : '未知';

    return sprintf('%s / %s / 权限 %s', $readable, $writable, $perm_octal);
}
