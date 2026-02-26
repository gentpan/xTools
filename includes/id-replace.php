<?php
/**
 * ID 替换工具
 *
 * @package WP Starter Kit
 */

defined('ABSPATH') || exit;

/**
 * 渲染 ID 替换工具选项卡。
 */
function wp_starter_kit_render_replace_post_id_tab()
{
    $post_types = wp_starter_kit_get_replaceable_post_types();
    $list_post_types = array('all' => '全部类型') + $post_types;
    $statuses = get_post_stati(array(), 'objects');
    ?>
    <h2>ID 替换</h2>
    <table class="form-table" role="presentation">
        <tr valign="top">
            <th scope="row"><label for="replace_old_id">旧 ID</label></th>
            <td><input type="number" id="replace_old_id" class="small-text" min="1" step="1"></td>
        </tr>
        <tr valign="top">
            <th scope="row"><label for="replace_new_id">新 ID</label></th>
            <td><input type="number" id="replace_new_id" class="small-text" min="1" step="1"></td>
        </tr>
        <tr valign="top">
            <th scope="row"><label for="replace_post_type">文章类型</label></th>
            <td>
                <select id="replace_post_type">
                    <?php foreach ($post_types as $post_type => $label) : ?>
                        <option value="<?php echo esc_attr($post_type); ?>"><?php echo esc_html($label); ?></option>
                    <?php endforeach; ?>
                </select>
                <p class="description">仅替换指定文章类型的 ID，避免误操作。</p>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row">执行替换</th>
            <td>
                <button type="button" id="replace_id_btn" class="button button-primary">立即替换</button>
                <span class="spinner" id="replace_id_spinner"></span>
                <div id="replace_id_result"></div>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row">执行日志</th>
            <td>
                <pre id="replace_id_log"></pre>
                <p>
                    <button type="button" id="replace_log_txt" class="button" disabled>下载 TXT 日志</button>
                    <button type="button" id="replace_log_csv" class="button" disabled>下载 CSV 日志</button>
                </p>
            </td>
        </tr>
    </table>

    <hr>

    <h2>ID 检查与可视化</h2>
    <table class="form-table" role="presentation">
        <tr valign="top">
            <th scope="row"><label for="replace_list_post_type">文章类型</label></th>
            <td>
                <select id="replace_list_post_type">
                    <?php foreach ($list_post_types as $post_type => $label) : ?>
                        <option value="<?php echo esc_attr($post_type); ?>"><?php echo esc_html($label); ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row"><label for="replace_list_status">状态</label></th>
            <td>
                <select id="replace_list_status">
                    <option value="all">全部状态</option>
                    <?php foreach ($statuses as $status => $status_obj) : ?>
                        <option value="<?php echo esc_attr($status); ?>"><?php echo esc_html($status_obj->label); ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row"><label for="replace_list_search">搜索</label></th>
            <td>
                <input type="text" id="replace_list_search" class="regular-text" placeholder="输入 ID 或标题关键字">
                <p class="description">支持按 ID 精确匹配，或按标题模糊搜索。</p>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row"><label for="replace_list_per_page">每页数量</label></th>
            <td>
                <select id="replace_list_per_page">
                    <option value="20">20</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row">加载列表</th>
            <td>
                <button type="button" id="replace_list_load" class="button button-secondary">查询数据</button>
                <span class="spinner" id="replace_list_spinner"></span>
            </td>
        </tr>
    </table>

    <div id="replace_list_summary"></div>
    <table class="widefat striped" id="replace_list_table">
        <thead>
            <tr>
                <th>ID</th>
                <th>标题</th>
                <th>类型</th>
                <th>状态</th>
                <th>日期</th>
                <th>父级</th>
                <th>评论</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="8">请点击“查询数据”加载列表。</td>
            </tr>
        </tbody>
    </table>
    <p>
        <button type="button" id="replace_list_prev" class="button" disabled>上一页</button>
        <button type="button" id="replace_list_next" class="button" disabled>下一页</button>
        <span id="replace_list_page_info"></span>
    </p>
    <?php
}

/**
 * 可替换的 post type 列表。
 */
function wp_starter_kit_get_replaceable_post_types()
{
    $result = array();
    $objects = get_post_types(array('show_ui' => true), 'objects');

    foreach ($objects as $post_type => $object) {
        if (!empty($object->labels->singular_name)) {
            $result[$post_type] = $object->labels->singular_name;
        } else {
            $result[$post_type] = $post_type;
        }
    }

    if (empty($result)) {
        $result['post'] = 'Post';
        $result['page'] = 'Page';
    }

    return $result;
}

add_action('wp_ajax_wp_starter_kit_replace_post_id', 'wp_starter_kit_replace_post_id_callback');
function wp_starter_kit_replace_post_id_callback()
{
    check_ajax_referer('wp_starter_kit_replace_post_id', 'nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('log' => array('权限不足')));
    }

    $old_id = absint($_POST['old_id'] ?? 0);
    $new_id = absint($_POST['new_id'] ?? 0);
    $post_type = sanitize_key($_POST['post_type'] ?? 'post');

    $result = wp_starter_kit_replace_post_id_logic($old_id, $new_id, $post_type);
    if ($result['success']) {
        wp_send_json_success(array('log' => $result['log']));
    }

    wp_send_json_error(array('log' => $result['log']));
}

add_action('wp_ajax_wp_starter_kit_replace_id_list', 'wp_starter_kit_replace_id_list_callback');
function wp_starter_kit_replace_id_list_callback()
{
    check_ajax_referer('wp_starter_kit_replace_id_list', 'nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => '权限不足'));
    }

    $post_type = sanitize_key($_POST['post_type'] ?? 'all');
    $status = sanitize_key($_POST['status'] ?? 'all');
    $search = sanitize_text_field($_POST['search'] ?? '');
    $page = max(1, absint($_POST['page'] ?? 1));
    $per_page = absint($_POST['per_page'] ?? 20);
    $per_page = in_array($per_page, array(20, 50, 100), true) ? $per_page : 20;

    $result = wp_starter_kit_get_replace_list_data($post_type, $status, $search, $page, $per_page);
    wp_send_json_success($result);
}

/**
 * 执行 ID 替换逻辑。
 */
function wp_starter_kit_replace_post_id_logic($old_id, $new_id, $post_type)
{
    global $wpdb;

    $log = array();
    $replaceable_types = wp_starter_kit_get_replaceable_post_types();

    if ($old_id <= 0 || $new_id <= 0 || $old_id === $new_id) {
        return array(
            'success' => false,
            'log' => array('旧 ID 和新 ID 必须为不同的正整数。'),
        );
    }

    if (!isset($replaceable_types[$post_type])) {
        return array(
            'success' => false,
            'log' => array('文章类型无效。'),
        );
    }

    $log[] = sprintf('开始替换：旧 ID = %d，新 ID = %d，类型 = %s', $old_id, $new_id, $post_type);

    $current_type = $wpdb->get_var($wpdb->prepare("SELECT post_type FROM {$wpdb->posts} WHERE ID = %d", $old_id));
    if (empty($current_type)) {
        return array(
            'success' => false,
            'log' => array_merge($log, array('旧 ID 对应内容不存在。')),
        );
    }

    if ($current_type !== $post_type) {
        return array(
            'success' => false,
            'log' => array_merge($log, array(sprintf('旧 ID 当前类型为 %s，与选择的 %s 不一致。', $current_type, $post_type))),
        );
    }

    $new_exists = (int) $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->posts} WHERE ID = %d", $new_id));
    if ($new_exists > 0) {
        return array(
            'success' => false,
            'log' => array_merge($log, array(sprintf('新 ID %d 已存在，请更换。', $new_id))),
        );
    }

    $term_conflict = (int) $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->term_relationships} WHERE object_id = %d", $new_id));
    if ($term_conflict > 0) {
        $log[] = sprintf('警告：新 ID %d 已有关联分类/标签，可能冲突。', $new_id);
    }

    $post_update = $wpdb->update($wpdb->posts, array('ID' => $new_id), array('ID' => $old_id), array('%d'), array('%d'));
    if ($post_update === false) {
        return array(
            'success' => false,
            'log' => array_merge($log, array('主表替换失败。')),
        );
    }
    $log[] = '主表 ID 替换成功。';

    $meta_update = $wpdb->update($wpdb->postmeta, array('post_id' => $new_id), array('post_id' => $old_id), array('%d'), array('%d'));
    $log[] = $meta_update === false ? 'postmeta 替换失败。' : 'postmeta 替换完成。';

    $term_update = $wpdb->query($wpdb->prepare("UPDATE {$wpdb->term_relationships} SET object_id = %d WHERE object_id = %d", $new_id, $old_id));
    $log[] = $term_update === false ? 'term_relationships 替换失败。' : 'term_relationships 替换完成。';

    $comment_update = $wpdb->update($wpdb->comments, array('comment_post_ID' => $new_id), array('comment_post_ID' => $old_id), array('%d'), array('%d'));
    $log[] = $comment_update === false ? '评论关联替换失败。' : '评论关联替换完成。';

    $parent_update = $wpdb->update($wpdb->posts, array('post_parent' => $new_id), array('post_parent' => $old_id), array('%d'), array('%d'));
    $log[] = $parent_update === false ? '子内容父级替换失败。' : '子内容父级替换完成。';

    $meta_ref_update = $wpdb->query(
        $wpdb->prepare(
            "UPDATE {$wpdb->postmeta} SET meta_value = %d WHERE meta_value = %d AND meta_key IN ('_thumbnail_id','_menu_item_object_id')",
            $new_id,
            $old_id
        )
    );
    $log[] = $meta_ref_update === false ? '缩略图/菜单引用更新失败。' : '缩略图/菜单引用更新完成。';

    $guid = $wpdb->get_var($wpdb->prepare("SELECT guid FROM {$wpdb->posts} WHERE ID = %d", $new_id));
    if (!empty($guid)) {
        $new_guid = str_replace(
            array("?p={$old_id}", "?page_id={$old_id}"),
            array("?p={$new_id}", "?page_id={$new_id}"),
            $guid
        );
        $guid_update = $wpdb->update($wpdb->posts, array('guid' => $new_guid), array('ID' => $new_id), array('%s'), array('%d'));
        $log[] = $guid_update === false ? 'GUID 更新失败。' : 'GUID 更新完成。';
    }

    clean_post_cache($old_id);
    clean_post_cache($new_id);
    $log[] = 'ID 替换完成，请检查前台访问与后台编辑。';

    return array(
        'success' => true,
        'log' => $log,
    );
}

function wp_starter_kit_get_replace_list_data($post_type, $status, $search, $page, $per_page)
{
    global $wpdb;

    $allowed_types = wp_starter_kit_get_replaceable_post_types();
    $allowed_statuses = get_post_stati(array(), 'names');

    $where = array('1=1');
    $params = array();

    if ($post_type !== 'all' && isset($allowed_types[$post_type])) {
        $where[] = 'p.post_type = %s';
        $params[] = $post_type;
    }

    if ($status !== 'all' && in_array($status, $allowed_statuses, true)) {
        $where[] = 'p.post_status = %s';
        $params[] = $status;
    }

    if ($search !== '') {
        if (ctype_digit($search)) {
            $where[] = 'p.ID = %d';
            $params[] = (int) $search;
        } else {
            $where[] = 'p.post_title LIKE %s';
            $params[] = '%' . $wpdb->esc_like($search) . '%';
        }
    }

    $where_sql = implode(' AND ', $where);
    $offset = ($page - 1) * $per_page;

    $count_sql = "SELECT COUNT(*) FROM {$wpdb->posts} p WHERE {$where_sql}";
    if (!empty($params)) {
        $count_sql = $wpdb->prepare($count_sql, $params);
    }
    $total = (int) $wpdb->get_var($count_sql);
    $total_pages = max(1, (int) ceil($total / $per_page));

    $list_sql = "SELECT p.ID, p.post_title, p.post_type, p.post_status, p.post_date, p.post_parent, p.comment_count
        FROM {$wpdb->posts} p
        WHERE {$where_sql}
        ORDER BY p.ID DESC
        LIMIT %d OFFSET %d";
    $list_params = $params;
    $list_params[] = $per_page;
    $list_params[] = $offset;
    $list_sql = $wpdb->prepare($list_sql, $list_params);

    $rows = $wpdb->get_results($list_sql);
    $items = array();
    foreach ((array) $rows as $row) {
        $items[] = array(
            'id' => (int) $row->ID,
            'title' => $row->post_title !== '' ? $row->post_title : '(无标题)',
            'type' => $row->post_type,
            'status' => $row->post_status,
            'date' => $row->post_date,
            'parent' => (int) $row->post_parent,
            'comment_count' => (int) $row->comment_count,
        );
    }

    $db_total = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts}");
    $max_id = (int) $wpdb->get_var("SELECT MAX(ID) FROM {$wpdb->posts}");
    $id_gaps = max(0, $max_id - $db_total);

    $type_counts_rows = $wpdb->get_results("SELECT post_type, COUNT(*) AS total FROM {$wpdb->posts} GROUP BY post_type ORDER BY total DESC");
    $type_counts = array();
    foreach ((array) $type_counts_rows as $type_row) {
        $type_counts[] = array(
            'post_type' => $type_row->post_type,
            'total' => (int) $type_row->total,
        );
    }

    return array(
        'filters' => array(
            'post_type' => $post_type,
            'status' => $status,
            'search' => $search,
            'page' => $page,
            'per_page' => $per_page,
        ),
        'pagination' => array(
            'total' => $total,
            'total_pages' => $total_pages,
            'page' => $page,
            'per_page' => $per_page,
            'has_prev' => $page > 1,
            'has_next' => $page < $total_pages,
        ),
        'db_summary' => array(
            'posts_total' => $db_total,
            'max_id' => $max_id,
            'estimated_id_gaps' => $id_gaps,
            'type_counts' => $type_counts,
        ),
        'items' => $items,
    );
}
