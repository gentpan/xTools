<?php
/**
 * SMTP 设置选项卡
 *
 * 提供 WordPress SMTP 邮件发送功能的配置界面
 * 支持常见邮件服务商的 SMTP 设置
 * 包含邮件发送测试功能
 *
 * @package WP Starter Kit
 * @subpackage Mail
 * @license GPL v2 or later
 */

function wp_starter_kit_smtp_settings_tab() {
    $options = get_option('wp_starter_kit_smtp_options');
    $mode = wp_starter_kit_get_mail_mode($options);
    // 修改默认端口为465
    $port = isset($options['port']) ? esc_attr($options['port']) : '465';
    // 修改默认加密方式为SSL  
    $encryption = isset($options['encryption']) ? esc_attr($options['encryption']) : 'ssl';
    $resend_api_key = esc_attr($options['resend_api_key'] ?? '');
    $resend_from = esc_attr($options['resend_from'] ?? '');
    $test_subject = esc_attr($options['test_subject'] ?? '这是一封测试邮件');
    $test_message = (string)($options['test_message'] ?? '恭喜你，邮件发送配置成功！这是一封测试邮件。');
    $test_content_type = esc_attr($options['test_content_type'] ?? 'html');
    $test_template_id = esc_attr($options['test_template_id'] ?? '');
    $test_template_vars = (string)($options['test_template_vars'] ?? '');
    $templates = function_exists('wp_starter_kit_get_mail_templates') ? wp_starter_kit_get_mail_templates() : array();
    ?>
        <?php settings_fields( 'wp_starter_kit_smtp_group' ); ?>
        <?php do_settings_sections( 'wp_starter_kit_smtp_group' ); ?>
        <tr valign="top">
            <th scope="row">邮件发送模式</th>
            <td>
                <select name="wp_starter_kit_smtp_options[mode]" id="mail_send_mode">
                    <option value="smtp" <?php selected($mode, 'smtp'); ?>>SMTP</option>
                    <option value="resend_api" <?php selected($mode, 'resend_api'); ?>>Resend API</option>
                </select>
                <p class="description">选择邮件发送方式：SMTP 或 Resend API。</p>
            </td>
        </tr>
        <tr valign="top" class="wp-starter-kit-resend-row <?php echo $mode === 'resend_api' ? '' : 'hidden'; ?>">
            <th scope="row">Resend API Key</th>
            <td>
                <input type="password" name="wp_starter_kit_smtp_options[resend_api_key]" class="regular-text" value="<?php echo $resend_api_key; ?>">
                <p class="description">在 Resend 控制台创建 API Key，格式示例：re_xxx。</p>
            </td>
        </tr>
        <tr valign="top" class="wp-starter-kit-resend-row <?php echo $mode === 'resend_api' ? '' : 'hidden'; ?>">
            <th scope="row">Resend 发件人邮箱</th>
            <td>
                <input type="email" name="wp_starter_kit_smtp_options[resend_from]" class="regular-text" value="<?php echo $resend_from; ?>">
                <p class="description">必须是 Resend 已验证域名下的发件人邮箱。</p>
            </td>
        </tr>
        <tr valign="top" class="wp-starter-kit-smtp-row <?php echo $mode === 'smtp' ? '' : 'hidden'; ?>">
            <th scope="row">SMTP 主机</th>
            <td><input type="text" name="wp_starter_kit_smtp_options[host]" class="regular-text" value="<?php echo esc_attr( $options['host'] ?? '' ); ?>"></td>
        </tr>
        <tr valign="top" class="wp-starter-kit-smtp-row <?php echo $mode === 'smtp' ? '' : 'hidden'; ?>">
            <th scope="row">端口</th>
            <td>
                <input type="number" name="wp_starter_kit_smtp_options[port]" id="smtp_port" 
                    class="small-text" value="<?php echo $port; ?>">
                <p class="description">常用端口: SSL-465, TLS-587, 无加密-25</p>
            </td>
        </tr>
        <tr valign="top" class="wp-starter-kit-smtp-row <?php echo $mode === 'smtp' ? '' : 'hidden'; ?>">
            <th scope="row">加密方式</th>
            <td>
                <select name="wp_starter_kit_smtp_options[encryption]" id="smtp_encryption">
                    <option value="" <?php selected($encryption, ''); ?>>无 (端口 25)</option>
                    <option value="ssl" <?php selected($encryption, 'ssl'); ?>>SSL (端口 465)</option>
                    <option value="tls" <?php selected($encryption, 'tls'); ?>>TLS (端口 587)</option>
                </select>
            </td>
        </tr>
        <tr valign="top" class="wp-starter-kit-smtp-row <?php echo $mode === 'smtp' ? '' : 'hidden'; ?>">
            <th scope="row">邮箱账号</th>
            <td><input type="text" name="wp_starter_kit_smtp_options[username]" class="regular-text" value="<?php echo esc_attr( $options['username'] ?? '' ); ?>"></td>
        </tr>
        <tr valign="top" class="wp-starter-kit-smtp-row <?php echo $mode === 'smtp' ? '' : 'hidden'; ?>">
            <th scope="row">邮箱密码</th>
            <td><input type="password" name="wp_starter_kit_smtp_options[password]" class="regular-text" value="<?php echo esc_attr( $options['password'] ?? '' ); ?>"></td>
        </tr>
        <tr valign="top">
            <th scope="row">发件人邮箱</th>
            <td><input type="email" name="wp_starter_kit_smtp_options[from]" class="regular-text" value="<?php echo esc_attr( $options['from'] ?? '' ); ?>"></td>
        </tr>
        <tr valign="top">
            <th scope="row">发件人名称</th>
            <td><input type="text" name="wp_starter_kit_smtp_options[from_name]" class="regular-text" value="<?php echo esc_attr( $options['from_name'] ?? '' ); ?>"></td>
        </tr>
        <tr valign="top">
            <th scope="row">测试发送邮件</th>
            <td>
                <div id="test_email_form">
                    <?php wp_nonce_field('wp_starter_kit_test_email', 'wp_starter_kit_test_email_nonce'); ?>
                    <input type="email" id="test_email" name="test_email" placeholder="收件人邮箱" class="regular-text" />
                    <?php if (!empty($templates)) : ?>
                        <p>
                            <select id="test_email_template_id" name="wp_starter_kit_smtp_options[test_template_id]">
                                <option value="">不使用模板（使用下方主题/正文）</option>
                                <?php foreach ($templates as $template) : ?>
                                    <option value="<?php echo esc_attr($template['id']); ?>" <?php selected($test_template_id, $template['id']); ?>>
                                        <?php echo esc_html($template['name']); ?><?php echo !empty($template['is_default']) ? '（默认）' : ''; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </p>
                        <p>
                            <textarea id="test_email_template_vars" name="wp_starter_kit_smtp_options[test_template_vars]" rows="4" class="large-text code" placeholder='模板变量 JSON，例如 {"user_name":"访客"}'><?php echo esc_textarea($test_template_vars); ?></textarea>
                        </p>
                    <?php endif; ?>
                    <p>
                        <input type="text" id="test_email_subject" name="wp_starter_kit_smtp_options[test_subject]" class="regular-text" placeholder="测试邮件主题" value="<?php echo $test_subject; ?>">
                    </p>
                    <p>
                        <select id="test_email_content_type" name="wp_starter_kit_smtp_options[test_content_type]">
                            <option value="html" <?php selected($test_content_type, 'html'); ?>>HTML</option>
                            <option value="plain" <?php selected($test_content_type, 'plain'); ?>>纯文本</option>
                        </select>
                    </p>
                    <p>
                        <textarea id="test_email_message" name="wp_starter_kit_smtp_options[test_message]" rows="6" class="large-text" placeholder="测试邮件正文"><?php echo esc_textarea($test_message); ?></textarea>
                    </p>
                    <button type="button" id="send_test_email" class="button button-secondary">
                        <span class="dashicons dashicons-email"></span>
                        发送测试邮件
                    </button>
                    <span class="spinner"></span>
                    <div id="test_email_result"></div>
                </div>
            </td>
        </tr>
    <?php
}

/**
 * 验证和清理 SMTP 设置
 */
function wp_starter_kit_smtp_sanitize( $input ) {
    $sanitized = array();
    $mode = sanitize_text_field($input['mode'] ?? 'smtp');
    $sanitized['mode'] = in_array($mode, array('smtp', 'resend_api'), true) ? $mode : 'smtp';

    $sanitized['host'] = sanitize_text_field($input['host'] ?? '');
    $sanitized['port'] = absint($input['port'] ?? 0);
    $sanitized['encryption'] = sanitize_text_field($input['encryption'] ?? '');
    $sanitized['username'] = sanitize_text_field($input['username'] ?? '');
    $sanitized['password'] = sanitize_text_field($input['password'] ?? '');
    $sanitized['from'] = sanitize_email($input['from'] ?? '');
    $sanitized['from_name'] = sanitize_text_field($input['from_name'] ?? '');
    $sanitized['resend_api_key'] = sanitize_text_field($input['resend_api_key'] ?? '');
    $sanitized['resend_from'] = sanitize_email($input['resend_from'] ?? '');
    $sanitized['test_subject'] = sanitize_text_field($input['test_subject'] ?? '');
    $sanitized['test_content_type'] = ($input['test_content_type'] ?? '') === 'plain' ? 'plain' : 'html';
    $sanitized['test_message'] = wp_kses_post($input['test_message'] ?? '');
    $sanitized['test_template_id'] = sanitize_key($input['test_template_id'] ?? '');
    $sanitized['test_template_vars'] = sanitize_textarea_field($input['test_template_vars'] ?? '');

    return $sanitized;
}

/**
 * SMTP 配置 hook
 */
add_action('phpmailer_init', 'wp_starter_kit_smtp_configure_phpmailer');
function wp_starter_kit_smtp_configure_phpmailer( $phpmailer ) {
    $options = get_option( 'wp_starter_kit_smtp_options' );
    $mode = wp_starter_kit_get_mail_mode($options);

    if ($mode !== 'smtp') {
        return;
    }

    if ( ! isset( $options['host'] ) || empty( $options['host'] ) ) {
        return;
    }

    $phpmailer->isSMTP();
    $phpmailer->Host       = $options['host'];
    $phpmailer->SMTPAuth   = true;
    $phpmailer->Port       = intval( $options['port'] );
    $phpmailer->Username   = $options['username'];
    $phpmailer->Password   = $options['password'];
    $phpmailer->SMTPSecure = $options['encryption'] ?: '';
    $phpmailer->From       = $options['from'] ?: $options['username'];
    $phpmailer->FromName   = $options['from_name'] ?: get_bloginfo( 'name' );
}

add_filter('pre_wp_mail', 'wp_starter_kit_send_via_resend_api', 10, 2);
function wp_starter_kit_send_via_resend_api($pre_wp_mail, $atts) {
    $options = get_option('wp_starter_kit_smtp_options');
    $mode = wp_starter_kit_get_mail_mode($options);

    if ($mode !== 'resend_api') {
        return $pre_wp_mail;
    }

    $api_key = trim((string)($options['resend_api_key'] ?? ''));
    $from_email = sanitize_email($options['resend_from'] ?? '');
    if (empty($from_email)) {
        $from_email = sanitize_email($options['from'] ?? '');
    }

    if (empty($api_key) || empty($from_email)) {
        return false;
    }

    $from_name = sanitize_text_field($options['from_name'] ?? '');
    $from = $from_name ? sprintf('%s <%s>', $from_name, $from_email) : $from_email;

    $headers = wp_starter_kit_parse_mail_headers($atts['headers'] ?? array());
    $to = wp_starter_kit_normalize_recipients($atts['to'] ?? '');
    if (empty($to)) {
        return false;
    }

    $content_type = strtolower($headers['content_type']);
    $payload = array(
        'from' => $from,
        'to' => $to,
        'subject' => (string)($atts['subject'] ?? ''),
    );

    if (strpos($content_type, 'text/plain') !== false) {
        $payload['text'] = wp_strip_all_tags((string)($atts['message'] ?? ''));
    } else {
        $payload['html'] = (string)($atts['message'] ?? '');
    }

    if (!empty($headers['cc'])) {
        $payload['cc'] = $headers['cc'];
    }
    if (!empty($headers['bcc'])) {
        $payload['bcc'] = $headers['bcc'];
    }
    if (!empty($headers['reply_to'])) {
        $payload['reply_to'] = $headers['reply_to'];
    }

    $response = wp_remote_post(
        'https://api.resend.com/emails',
        array(
            'timeout' => 20,
            'headers' => array(
                'Authorization' => 'Bearer ' . $api_key,
                'Content-Type' => 'application/json',
            ),
            'body' => wp_json_encode($payload),
        )
    );

    if (is_wp_error($response)) {
        return false;
    }

    $status = wp_remote_retrieve_response_code($response);
    return $status >= 200 && $status < 300;
}

function wp_starter_kit_get_mail_mode($options = null) {
    if (!is_array($options)) {
        $options = get_option('wp_starter_kit_smtp_options');
    }

    $mode = $options['mode'] ?? 'smtp';
    return in_array($mode, array('smtp', 'resend_api'), true) ? $mode : 'smtp';
}

function wp_starter_kit_parse_mail_headers($raw_headers) {
    $parsed = array(
        'content_type' => 'text/plain',
        'cc' => array(),
        'bcc' => array(),
        'reply_to' => array(),
    );

    if (empty($raw_headers)) {
        return $parsed;
    }

    if (!is_array($raw_headers)) {
        $raw_headers = explode("\n", str_replace("\r\n", "\n", $raw_headers));
    }

    foreach ($raw_headers as $header_line) {
        $header_line = trim((string)$header_line);
        if ($header_line === '' || strpos($header_line, ':') === false) {
            continue;
        }

        list($name, $value) = explode(':', $header_line, 2);
        $name = strtolower(trim($name));
        $value = trim($value);

        if ($name === 'content-type') {
            $parsed['content_type'] = $value;
        } elseif ($name === 'cc') {
            $parsed['cc'] = wp_starter_kit_normalize_recipients($value);
        } elseif ($name === 'bcc') {
            $parsed['bcc'] = wp_starter_kit_normalize_recipients($value);
        } elseif ($name === 'reply-to') {
            $parsed['reply_to'] = wp_starter_kit_normalize_recipients($value);
        }
    }

    return $parsed;
}

function wp_starter_kit_normalize_recipients($recipients) {
    if (!is_array($recipients)) {
        $recipients = explode(',', (string)$recipients);
    }

    $result = array();
    foreach ($recipients as $recipient) {
        $email = sanitize_email(trim((string)$recipient));
        if (!empty($email)) {
            $result[] = $email;
        }
    }

    return array_values(array_unique($result));
}

/**
 * 测试邮件发送回调函数
 */
function wp_starter_kit_send_test_email_callback() {
    // 验证安全性
    check_ajax_referer('wp_starter_kit_test_email', 'nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error('权限不足');
    }

    $to = sanitize_email($_POST['test_email']);
    if (empty($to)) {
        wp_send_json_error('邮箱地址不能为空');
    }

    $options = get_option('wp_starter_kit_smtp_options');
    $template_id = sanitize_key($_POST['test_template_id'] ?? ($options['test_template_id'] ?? ''));
    $template_vars_raw = sanitize_textarea_field($_POST['test_template_vars'] ?? ($options['test_template_vars'] ?? ''));
    $template_vars = array();
    if (!empty($template_vars_raw)) {
        $decoded = json_decode(wp_unslash($template_vars_raw), true);
        if (is_array($decoded)) {
            $template_vars = $decoded;
        }
    }

    if (!empty($template_id) && function_exists('wp_starter_kit_get_mail_template_by_id') && function_exists('wp_starter_kit_render_mail_template')) {
        $template = wp_starter_kit_get_mail_template_by_id($template_id);
        if ($template) {
            $template_vars['user_email'] = $to;
            $rendered = wp_starter_kit_render_mail_template($template, $template_vars);
            $subject = $rendered['subject'];
            $message = $rendered['content_type'] === 'plain' ? $rendered['plain_body'] : $rendered['html_body'];
            $headers = array(
                $rendered['content_type'] === 'plain'
                    ? 'Content-Type: text/plain; charset=UTF-8'
                    : 'Content-Type: text/html; charset=UTF-8'
            );
            $result = wp_mail($to, $subject, $message, $headers);

            if ($result) {
                wp_send_json_success('模板邮件发送成功！');
            }
            wp_send_json_error('模板邮件发送失败，请检查配置！');
        }
    }

    $subject = sanitize_text_field($_POST['test_subject'] ?? ($options['test_subject'] ?? '这是一封测试邮件'));
    $message = wp_kses_post((string)($_POST['test_message'] ?? ($options['test_message'] ?? '恭喜你，邮件发送配置成功！这是一封测试邮件。')));
    $content_type = sanitize_text_field($_POST['test_content_type'] ?? ($options['test_content_type'] ?? 'html'));
    $headers = array(
        $content_type === 'plain'
            ? 'Content-Type: text/plain; charset=UTF-8'
            : 'Content-Type: text/html; charset=UTF-8'
    );

    $result = wp_mail($to, $subject, $message, $headers);

    if ($result) {
        wp_send_json_success('邮件发送成功！');
    } else {
        wp_send_json_error('邮件发送失败，请检查配置！');
    }
}

/**
 * 添加 AJAX 动作
 */
add_action('wp_ajax_wp_starter_kit_send_test_email', 'wp_starter_kit_send_test_email_callback');

register_setting(
    'wp_starter_kit_smtp_group',
    'wp_starter_kit_smtp_options',
    'wp_starter_kit_smtp_sanitize'
);
