<?php
/**
 * 数据库管理（读取与可控修改）
 *
 * @package WP Starter Kit
 */

defined('ABSPATH') || exit;

function wp_starter_kit_render_db_manager_tab()
{
    ?>
    <h2>数据库管理</h2>
    <p class="description">可读取表字段与记录，并对单条记录进行受控修改。请先备份数据库。</p>
    <div class="notice notice-warning inline">
        <p><strong>高风险操作提示：</strong>本页支持直接写入数据库。建议先执行“一键备份”，再进行任何修改。</p>
    </div>
    <div class="notice notice-info inline">
        <p>建议流程：1) 一键备份 2) 执行修改 3) 如异常，一键恢复到备份。</p>
    </div>

    <table class="form-table" role="presentation">
        <tr valign="top">
            <th scope="row">数据库备份</th>
            <td>
                <button type="button" id="dbm_backup_create" class="button button-primary">一键备份</button>
                <span class="spinner" id="dbm_backup_spinner"></span>
                <p class="description">备份内容为当前站点前缀表（JSON 文件），保存在 uploads 目录下。</p>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row">数据库恢复</th>
            <td>
                <select id="dbm_backup_select"></select>
                <button type="button" id="dbm_backup_refresh" class="button">刷新备份列表</button>
                <button type="button" id="dbm_backup_restore" class="button button-secondary">一键恢复</button>
                <p class="description">恢复会覆盖当前数据库前缀表数据，请谨慎操作。</p>
            </td>
        </tr>
    </table>

    <table class="form-table" role="presentation">
        <tr valign="top">
            <th scope="row"><label for="dbm_table_select">数据表</label></th>
            <td>
                <select id="dbm_table_select"></select>
                <button type="button" id="dbm_reload_tables" class="button">刷新表列表</button>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row">分页</th>
            <td>
                <select id="dbm_per_page">
                    <option value="20">20</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <button type="button" id="dbm_load_rows" class="button button-secondary">读取数据</button>
                <span class="spinner" id="dbm_spinner"></span>
            </td>
        </tr>
    </table>

    <div id="dbm_result"></div>
    <table class="widefat striped" id="dbm_rows_table">
        <thead><tr id="dbm_rows_head"></tr></thead>
        <tbody><tr><td>请先读取数据。</td></tr></tbody>
    </table>
    <p>
        <button type="button" id="dbm_prev_page" class="button" disabled>上一页</button>
        <button type="button" id="dbm_next_page" class="button" disabled>下一页</button>
        <span id="dbm_page_info"></span>
    </p>

    <h3>记录编辑</h3>
    <table class="form-table" role="presentation">
        <tr valign="top">
            <th scope="row"><label for="dbm_edit_pk">主键值</label></th>
            <td><input type="text" id="dbm_edit_pk" class="regular-text" readonly></td>
        </tr>
        <tr valign="top">
            <th scope="row"><label for="dbm_edit_json">字段 JSON</label></th>
            <td>
                <textarea id="dbm_edit_json" class="large-text code" rows="12" placeholder='{"post_title":"新标题"}'></textarea>
                <p class="description">仅修改你填入的字段。主键字段不会被修改。</p>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row">保存</th>
            <td>
                <button type="button" id="dbm_save_row" class="button button-primary">保存到数据库</button>
                <span class="spinner" id="dbm_save_spinner"></span>
            </td>
        </tr>
    </table>
    <?php
}

function wp_starter_kit_dbm_allowed_tables()
{
    global $wpdb;
    $rows = $wpdb->get_col($wpdb->prepare('SHOW TABLES LIKE %s', $wpdb->esc_like($wpdb->prefix) . '%'));
    return is_array($rows) ? $rows : array();
}

function wp_starter_kit_dbm_get_primary_key($table)
{
    global $wpdb;
    $key = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT COLUMN_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = %s AND CONSTRAINT_NAME = 'PRIMARY' LIMIT 1",
            $table
        )
    );
    if (!empty($key)) {
        return $key;
    }

    $first_column = $wpdb->get_var("SHOW COLUMNS FROM `{$table}`");
    return $first_column ?: '';
}

function wp_starter_kit_dbm_validate_table($table)
{
    $table = sanitize_text_field($table);
    return in_array($table, wp_starter_kit_dbm_allowed_tables(), true) ? $table : '';
}

function wp_starter_kit_dbm_backup_dir()
{
    $upload = wp_get_upload_dir();
    $dir = trailingslashit($upload['basedir']) . 'wp-starter-kit-backups';
    if (!file_exists($dir)) {
        wp_mkdir_p($dir);
    }
    return $dir;
}

function wp_starter_kit_dbm_backup_file_name()
{
    return 'db-backup-' . gmdate('Ymd-His') . '.json';
}

function wp_starter_kit_dbm_backup_file_path($file_name)
{
    $safe = sanitize_file_name($file_name);
    return trailingslashit(wp_starter_kit_dbm_backup_dir()) . $safe;
}

function wp_starter_kit_dbm_list_backups()
{
    $dir = wp_starter_kit_dbm_backup_dir();
    $files = glob(trailingslashit($dir) . 'db-backup-*.json');
    if (!is_array($files)) {
        return array();
    }

    rsort($files);
    $result = array();
    foreach ($files as $file) {
        $result[] = array(
            'name' => basename($file),
            'size' => filesize($file),
            'modified' => gmdate('Y-m-d H:i:s', filemtime($file)),
        );
    }

    return $result;
}

function wp_starter_kit_dbm_create_backup()
{
    global $wpdb;

    $tables = wp_starter_kit_dbm_allowed_tables();
    if (empty($tables)) {
        return new WP_Error('no_tables', '未找到可备份数据表');
    }

    $backup = array(
        'meta' => array(
            'created_at' => gmdate('c'),
            'site_url' => home_url('/'),
            'db_prefix' => $wpdb->prefix,
            'plugin' => 'wp-starter-kit',
            'format' => 'json-v1',
        ),
        'tables' => array(),
    );

    foreach ($tables as $table) {
        $columns_rows = $wpdb->get_results("SHOW COLUMNS FROM `{$table}`", ARRAY_A);
        $columns = array_map(function ($row) {
            return $row['Field'];
        }, (array) $columns_rows);

        $rows = $wpdb->get_results("SELECT * FROM `{$table}`", ARRAY_A);
        $backup['tables'][$table] = array(
            'columns' => $columns,
            'rows' => is_array($rows) ? $rows : array(),
        );
    }

    $file_name = wp_starter_kit_dbm_backup_file_name();
    $file_path = wp_starter_kit_dbm_backup_file_path($file_name);
    $json = wp_json_encode($backup);
    if ($json === false) {
        return new WP_Error('encode_failed', '备份编码失败');
    }

    $written = file_put_contents($file_path, $json);
    if ($written === false) {
        return new WP_Error('write_failed', '备份文件写入失败');
    }

    return array(
        'name' => $file_name,
        'path' => $file_path,
        'size' => filesize($file_path),
    );
}

function wp_starter_kit_dbm_restore_backup($file_name)
{
    global $wpdb;

    $path = wp_starter_kit_dbm_backup_file_path($file_name);
    if (!file_exists($path) || !is_readable($path)) {
        return new WP_Error('file_missing', '备份文件不存在或不可读');
    }

    $raw = file_get_contents($path);
    $data = json_decode((string) $raw, true);
    if (!is_array($data) || empty($data['tables']) || !is_array($data['tables'])) {
        return new WP_Error('invalid_backup', '备份文件格式无效');
    }

    $allowed_tables = wp_starter_kit_dbm_allowed_tables();
    if (empty($allowed_tables)) {
        return new WP_Error('no_tables', '当前数据库未检测到可恢复数据表');
    }

    $wpdb->query('SET FOREIGN_KEY_CHECKS=0');
    $wpdb->query('START TRANSACTION');

    foreach ($data['tables'] as $table => $table_data) {
        if (!in_array($table, $allowed_tables, true)) {
            continue;
        }

        $rows = isset($table_data['rows']) && is_array($table_data['rows']) ? $table_data['rows'] : array();
        $wpdb->query("TRUNCATE TABLE `{$table}`");

        if (!empty($rows)) {
            $columns = $wpdb->get_col("SHOW COLUMNS FROM `{$table}`");
            foreach ($rows as $row) {
                if (!is_array($row)) {
                    continue;
                }
                $insert_data = array();
                $formats = array();
                foreach ($row as $column => $value) {
                    if (!in_array($column, $columns, true)) {
                        continue;
                    }
                    $insert_data[$column] = $value;
                    $formats[] = '%s';
                }
                if (!empty($insert_data)) {
                    $result = $wpdb->insert($table, $insert_data, $formats);
                    if ($result === false) {
                        $wpdb->query('ROLLBACK');
                        $wpdb->query('SET FOREIGN_KEY_CHECKS=1');
                        return new WP_Error('restore_failed', '恢复失败：表写入错误（' . $table . '）');
                    }
                }
            }
        }
    }

    $wpdb->query('COMMIT');
    $wpdb->query('SET FOREIGN_KEY_CHECKS=1');
    return true;
}

add_action('wp_ajax_wp_starter_kit_db_manager', 'wp_starter_kit_db_manager_callback');
function wp_starter_kit_db_manager_callback()
{
    check_ajax_referer('wp_starter_kit_db_manager', 'nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => '权限不足'));
    }

    $op = sanitize_key($_POST['op'] ?? '');
    if ($op === 'tables') {
        $tables = wp_starter_kit_dbm_allowed_tables();
        wp_send_json_success(array('tables' => array_values($tables)));
    }

    if ($op === 'rows') {
        global $wpdb;
        $table = wp_starter_kit_dbm_validate_table($_POST['table'] ?? '');
        if ($table === '') {
            wp_send_json_error(array('message' => '无效数据表'));
        }

        $page = max(1, absint($_POST['page'] ?? 1));
        $per_page = absint($_POST['per_page'] ?? 20);
        $per_page = in_array($per_page, array(20, 50, 100), true) ? $per_page : 20;
        $offset = ($page - 1) * $per_page;

        $total = (int) $wpdb->get_var("SELECT COUNT(*) FROM `{$table}`");
        $total_pages = max(1, (int) ceil($total / $per_page));

        $columns_result = $wpdb->get_results("SHOW COLUMNS FROM `{$table}`", ARRAY_A);
        $columns = array_map(function ($col) {
            return $col['Field'];
        }, (array) $columns_result);

        $rows = $wpdb->get_results($wpdb->prepare("SELECT * FROM `{$table}` LIMIT %d OFFSET %d", $per_page, $offset), ARRAY_A);
        $pk = wp_starter_kit_dbm_get_primary_key($table);

        wp_send_json_success(array(
            'table' => $table,
            'primary_key' => $pk,
            'columns' => $columns,
            'rows' => $rows ?: array(),
            'pagination' => array(
                'page' => $page,
                'per_page' => $per_page,
                'total' => $total,
                'total_pages' => $total_pages,
                'has_prev' => $page > 1,
                'has_next' => $page < $total_pages,
            ),
        ));
    }

    if ($op === 'backup_list') {
        wp_send_json_success(array(
            'backups' => wp_starter_kit_dbm_list_backups(),
        ));
    }

    if ($op === 'backup_create') {
        $result = wp_starter_kit_dbm_create_backup();
        if (is_wp_error($result)) {
            wp_send_json_error(array('message' => $result->get_error_message()));
        }
        wp_send_json_success(array(
            'message' => '备份创建成功',
            'backup' => $result,
            'backups' => wp_starter_kit_dbm_list_backups(),
        ));
    }

    if ($op === 'backup_restore') {
        $file_name = sanitize_file_name($_POST['file_name'] ?? '');
        if ($file_name === '') {
            wp_send_json_error(array('message' => '请选择备份文件'));
        }

        $result = wp_starter_kit_dbm_restore_backup($file_name);
        if (is_wp_error($result)) {
            wp_send_json_error(array('message' => $result->get_error_message()));
        }
        wp_send_json_success(array(
            'message' => '数据库恢复成功',
            'backups' => wp_starter_kit_dbm_list_backups(),
        ));
    }

    if ($op === 'update') {
        global $wpdb;
        $table = wp_starter_kit_dbm_validate_table($_POST['table'] ?? '');
        if ($table === '') {
            wp_send_json_error(array('message' => '无效数据表'));
        }

        $pk = sanitize_text_field($_POST['primary_key'] ?? '');
        $pk_value = sanitize_text_field($_POST['primary_value'] ?? '');
        if ($pk === '' || $pk_value === '') {
            wp_send_json_error(array('message' => '主键信息缺失'));
        }

        $allowed_columns = $wpdb->get_col("SHOW COLUMNS FROM `{$table}`");
        if (!is_array($allowed_columns) || !in_array($pk, $allowed_columns, true)) {
            wp_send_json_error(array('message' => '主键字段无效'));
        }

        $raw_json = wp_unslash($_POST['update_json'] ?? '');
        $decoded = json_decode($raw_json, true);
        if (!is_array($decoded) || empty($decoded)) {
            wp_send_json_error(array('message' => '更新 JSON 无效'));
        }

        $data = array();
        $formats = array();
        foreach ($decoded as $field => $value) {
            $field = sanitize_text_field($field);
            if ($field === '' || $field === $pk || !in_array($field, $allowed_columns, true)) {
                continue;
            }

            $data[$field] = is_scalar($value) ? (string) $value : wp_json_encode($value);
            $formats[] = '%s';
        }

        if (empty($data)) {
            wp_send_json_error(array('message' => '没有可更新字段'));
        }

        // 统一按字符串更新，避免类型猜测导致的误写。
        $result = $wpdb->update(
            $table,
            $data,
            array($pk => $pk_value),
            $formats,
            array('%s')
        );

        if ($result === false) {
            wp_send_json_error(array('message' => '数据库更新失败'));
        }

        wp_send_json_success(array('message' => '数据库更新成功', 'affected_rows' => $result));
    }

    wp_send_json_error(array('message' => '不支持的操作'));
}
