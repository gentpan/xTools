<?php
/**
 * 邮件模板中心
 *
 * @package WP Starter Kit
 */

defined('ABSPATH') || exit;

function wp_starter_kit_get_mail_templates_option_name()
{
    return 'wp_starter_kit_mail_templates';
}

function wp_starter_kit_get_default_mail_template()
{
    return array(
        'id' => 'default_notice',
        'name' => '默认通知模板',
        'subject' => '【{{site_name}}】通知',
        'content_type' => 'html',
        'html_body' => "<h2>您好，{{user_name}}</h2>\n<p>这里是 {{site_name}} 的通知邮件。</p>\n<p>当前时间：{{date}}</p>",
        'plain_body' => "您好，{{user_name}}\n这里是 {{site_name}} 的通知邮件。\n当前时间：{{date}}",
        'enabled' => 1,
        'is_default' => 1,
        'updated_at' => current_time('mysql'),
    );
}

function wp_starter_kit_get_mail_templates()
{
    $option_name = wp_starter_kit_get_mail_templates_option_name();
    $templates = get_option($option_name, array());

    if (!is_array($templates) || empty($templates)) {
        $templates = array(wp_starter_kit_get_default_mail_template());
        update_option($option_name, $templates);
    }

    $normalized = array();
    foreach ($templates as $template) {
        $sanitized = wp_starter_kit_sanitize_single_mail_template($template);
        if (!empty($sanitized['id'])) {
            $normalized[] = $sanitized;
        }
    }

    if (empty($normalized)) {
        $normalized = array(wp_starter_kit_get_default_mail_template());
    }

    $has_default = false;
    foreach ($normalized as $template) {
        if (!empty($template['is_default'])) {
            $has_default = true;
            break;
        }
    }

    if (!$has_default) {
        $normalized[0]['is_default'] = 1;
    }

    update_option($option_name, $normalized);
    return $normalized;
}

function wp_starter_kit_sanitize_single_mail_template($template)
{
    $allowed_content_types = array('html', 'plain');

    $id = sanitize_key($template['id'] ?? '');
    if ($id === '') {
        $id = 'tpl_' . wp_generate_password(8, false, false);
    }

    $content_type = sanitize_key($template['content_type'] ?? 'html');
    if (!in_array($content_type, $allowed_content_types, true)) {
        $content_type = 'html';
    }

    return array(
        'id' => $id,
        'name' => sanitize_text_field($template['name'] ?? ''),
        'subject' => sanitize_text_field($template['subject'] ?? ''),
        'content_type' => $content_type,
        'html_body' => wp_kses_post($template['html_body'] ?? ''),
        'plain_body' => sanitize_textarea_field($template['plain_body'] ?? ''),
        'enabled' => !empty($template['enabled']) ? 1 : 0,
        'is_default' => !empty($template['is_default']) ? 1 : 0,
        'updated_at' => sanitize_text_field($template['updated_at'] ?? current_time('mysql')),
    );
}

function wp_starter_kit_render_mail_templates_tab()
{
    $templates = wp_starter_kit_get_mail_templates();
    ?>
    <h2>邮件模板中心</h2>
    <p class="description">支持多模板管理，可编辑 HTML 与纯文本正文，并在测试发送时按模板渲染。</p>

    <table class="form-table" role="presentation">
        <tr valign="top">
            <th scope="row"><label for="mail_template_select">模板列表</label></th>
            <td>
                <select id="mail_template_select">
                    <?php foreach ($templates as $template) : ?>
                        <option value="<?php echo esc_attr($template['id']); ?>" <?php selected(!empty($template['is_default']), true); ?>>
                            <?php echo esc_html($template['name']); ?><?php echo !empty($template['is_default']) ? '（默认）' : ''; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="button" id="mail_template_new" class="button button-secondary">新增模板</button>
                <button type="button" id="mail_template_duplicate" class="button">复制模板</button>
                <button type="button" id="mail_template_delete" class="button">删除模板</button>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row"><label for="mail_template_name">模板名称</label></th>
            <td><input type="text" id="mail_template_name" class="regular-text"></td>
        </tr>
        <tr valign="top">
            <th scope="row"><label for="mail_template_subject">邮件主题</label></th>
            <td><input type="text" id="mail_template_subject" class="regular-text"></td>
        </tr>
        <tr valign="top">
            <th scope="row"><label for="mail_template_content_type">正文类型</label></th>
            <td>
                <select id="mail_template_content_type">
                    <option value="html">HTML</option>
                    <option value="plain">纯文本</option>
                </select>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row"><label for="mail_template_html_body">HTML 正文</label></th>
            <td><textarea id="mail_template_html_body" rows="10" class="large-text code"></textarea></td>
        </tr>
        <tr valign="top">
            <th scope="row"><label for="mail_template_plain_body">纯文本正文</label></th>
            <td><textarea id="mail_template_plain_body" rows="8" class="large-text code"></textarea></td>
        </tr>
        <tr valign="top">
            <th scope="row">模板状态</th>
            <td>
                <label><input type="checkbox" id="mail_template_enabled" checked> 启用模板</label>
                <label><input type="checkbox" id="mail_template_default"> 设为默认模板</label>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row"><label for="mail_template_vars">模板变量（JSON）</label></th>
            <td>
                <textarea id="mail_template_vars" rows="5" class="large-text code" placeholder='{"user_name":"访客","user_email":"test@example.com"}'></textarea>
                <p class="description">可用变量：{{site_name}} {{site_url}} {{admin_email}} {{user_name}} {{user_email}} {{date}}</p>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row"><label for="mail_template_test_to">测试收件人</label></th>
            <td>
                <input type="email" id="mail_template_test_to" class="regular-text" placeholder="test@example.com">
                <button type="button" id="mail_template_send_test" class="button button-secondary">发送模板测试邮件</button>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row">操作</th>
            <td>
                <button type="button" id="mail_template_preview" class="button">预览渲染</button>
                <button type="button" id="mail_template_save" class="button button-primary">保存模板</button>
                <span class="spinner" id="mail_template_spinner"></span>
                <div id="mail_template_result"></div>
            </td>
        </tr>
    </table>
    <h3>预览结果</h3>
    <pre id="mail_template_preview_box"></pre>

    <script type="application/json" id="mail-template-initial-data"><?php echo wp_json_encode(array_values($templates)); ?></script>
    <?php
}

function wp_starter_kit_get_mail_template_by_id($template_id)
{
    $template_id = sanitize_key($template_id);
    $templates = wp_starter_kit_get_mail_templates();
    foreach ($templates as $template) {
        if ($template['id'] === $template_id) {
            return $template;
        }
    }

    return null;
}

function wp_starter_kit_get_default_mail_template_data()
{
    $templates = wp_starter_kit_get_mail_templates();
    foreach ($templates as $template) {
        if (!empty($template['is_default'])) {
            return $template;
        }
    }
    return $templates[0];
}

function wp_starter_kit_get_template_variables($custom_vars = array())
{
    $vars = array(
        'site_name' => get_bloginfo('name'),
        'site_url' => home_url('/'),
        'admin_email' => get_option('admin_email'),
        'user_name' => '访客',
        'user_email' => get_option('admin_email'),
        'date' => current_time('mysql'),
    );

    if (is_array($custom_vars)) {
        foreach ($custom_vars as $key => $value) {
            $k = sanitize_key($key);
            if ($k !== '') {
                $vars[$k] = sanitize_text_field((string) $value);
            }
        }
    }

    return $vars;
}

function wp_starter_kit_render_mail_template($template, $custom_vars = array())
{
    $vars = wp_starter_kit_get_template_variables($custom_vars);
    $replace = array();
    foreach ($vars as $key => $value) {
        $replace['{{' . $key . '}}'] = $value;
    }

    return array(
        'subject' => strtr((string)($template['subject'] ?? ''), $replace),
        'html_body' => strtr((string)($template['html_body'] ?? ''), $replace),
        'plain_body' => strtr((string)($template['plain_body'] ?? ''), $replace),
        'content_type' => $template['content_type'] ?? 'html',
    );
}

function wp_starter_kit_parse_template_vars_from_request($raw_json)
{
    if (!is_string($raw_json) || trim($raw_json) === '') {
        return array();
    }

    $decoded = json_decode(wp_unslash($raw_json), true);
    return is_array($decoded) ? $decoded : array();
}

add_action('wp_ajax_wp_starter_kit_mail_template_manage', 'wp_starter_kit_mail_template_manage_callback');
function wp_starter_kit_mail_template_manage_callback()
{
    check_ajax_referer('wp_starter_kit_mail_template_manage', 'nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => '权限不足'));
    }

    $op = sanitize_key($_POST['op'] ?? '');
    $templates = wp_starter_kit_get_mail_templates();

    if ($op === 'list') {
        wp_send_json_success(array('templates' => array_values($templates)));
    }

    if ($op === 'save') {
        $raw = $_POST['template'] ?? array();
        $template = wp_starter_kit_sanitize_single_mail_template($raw);
        $template['updated_at'] = current_time('mysql');

        $found = false;
        foreach ($templates as $index => $item) {
            if ($item['id'] === $template['id']) {
                $templates[$index] = $template;
                $found = true;
                break;
            }
        }
        if (!$found) {
            $templates[] = $template;
        }

        if (!empty($template['is_default'])) {
            foreach ($templates as $index => $item) {
                if ($item['id'] !== $template['id']) {
                    $templates[$index]['is_default'] = 0;
                }
            }
        }

        update_option(wp_starter_kit_get_mail_templates_option_name(), array_values($templates));
        wp_send_json_success(array('templates' => array_values($templates), 'message' => '模板已保存'));
    }

    if ($op === 'delete') {
        $template_id = sanitize_key($_POST['template_id'] ?? '');
        $templates = array_values(array_filter($templates, function ($item) use ($template_id) {
            return $item['id'] !== $template_id;
        }));

        if (empty($templates)) {
            $templates = array(wp_starter_kit_get_default_mail_template());
        }

        $has_default = false;
        foreach ($templates as $item) {
            if (!empty($item['is_default'])) {
                $has_default = true;
                break;
            }
        }
        if (!$has_default) {
            $templates[0]['is_default'] = 1;
        }

        update_option(wp_starter_kit_get_mail_templates_option_name(), array_values($templates));
        wp_send_json_success(array('templates' => array_values($templates), 'message' => '模板已删除'));
    }

    if ($op === 'duplicate') {
        $template_id = sanitize_key($_POST['template_id'] ?? '');
        $source = wp_starter_kit_get_mail_template_by_id($template_id);
        if (!$source) {
            wp_send_json_error(array('message' => '模板不存在'));
        }

        $source['id'] = 'tpl_' . wp_generate_password(8, false, false);
        $source['name'] = $source['name'] . ' - 副本';
        $source['is_default'] = 0;
        $source['updated_at'] = current_time('mysql');
        $templates[] = $source;
        update_option(wp_starter_kit_get_mail_templates_option_name(), array_values($templates));
        wp_send_json_success(array('templates' => array_values($templates), 'message' => '模板已复制'));
    }

    if ($op === 'preview') {
        $template_id = sanitize_key($_POST['template_id'] ?? '');
        $template = wp_starter_kit_get_mail_template_by_id($template_id);
        if (!$template) {
            $template = wp_starter_kit_get_default_mail_template_data();
        }
        $vars = wp_starter_kit_parse_template_vars_from_request($_POST['vars_json'] ?? '');
        $rendered = wp_starter_kit_render_mail_template($template, $vars);
        wp_send_json_success(array('rendered' => $rendered));
    }

    if ($op === 'send_test') {
        $template_id = sanitize_key($_POST['template_id'] ?? '');
        $to = sanitize_email($_POST['to'] ?? '');
        if (empty($to)) {
            wp_send_json_error(array('message' => '收件人邮箱不能为空'));
        }

        $template = wp_starter_kit_get_mail_template_by_id($template_id);
        if (!$template) {
            $template = wp_starter_kit_get_default_mail_template_data();
        }
        $vars = wp_starter_kit_parse_template_vars_from_request($_POST['vars_json'] ?? '');
        $vars['user_email'] = $to;
        $rendered = wp_starter_kit_render_mail_template($template, $vars);

        $headers = array(
            $rendered['content_type'] === 'plain'
                ? 'Content-Type: text/plain; charset=UTF-8'
                : 'Content-Type: text/html; charset=UTF-8'
        );

        $message = $rendered['content_type'] === 'plain' ? $rendered['plain_body'] : $rendered['html_body'];
        $sent = wp_mail($to, $rendered['subject'], $message, $headers);
        if ($sent) {
            wp_send_json_success(array('message' => '模板测试邮件发送成功'));
        }

        wp_send_json_error(array('message' => '模板测试邮件发送失败'));
    }

    wp_send_json_error(array('message' => '不支持的操作'));
}
