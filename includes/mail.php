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
    // 修改默认端口为465
    $port = isset($options['port']) ? esc_attr($options['port']) : '465';
    // 修改默认加密方式为SSL  
    $encryption = isset($options['encryption']) ? esc_attr($options['encryption']) : 'ssl';
    ?>
        <?php settings_fields( 'wp_starter_kit_smtp_group' ); ?>
        <?php do_settings_sections( 'wp_starter_kit_smtp_group' ); ?>
        <tr valign="top">
            <th scope="row">SMTP 主机</th>
            <td><input type="text" name="wp_starter_kit_smtp_options[host]" class="regular-text" value="<?php echo esc_attr( $options['host'] ?? '' ); ?>"></td>
        </tr>
        <tr valign="top">
            <th scope="row">端口</th>
            <td>
                <input type="number" name="wp_starter_kit_smtp_options[port]" id="smtp_port" 
                    class="small-text" value="<?php echo $port; ?>">
                <p class="description">常用端口: SSL-465, TLS-587, 无加密-25</p>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row">加密方式</th>
            <td>
                <select name="wp_starter_kit_smtp_options[encryption]" id="smtp_encryption">
                    <option value="" <?php selected($encryption, ''); ?>>无 (端口 25)</option>
                    <option value="ssl" <?php selected($encryption, 'ssl'); ?>>SSL (端口 465)</option>
                    <option value="tls" <?php selected($encryption, 'tls'); ?>>TLS (端口 587)</option>
                </select>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row">邮箱账号</th>
            <td><input type="text" name="wp_starter_kit_smtp_options[username]" class="regular-text" value="<?php echo esc_attr( $options['username'] ?? '' ); ?>"></td>
        </tr>
        <tr valign="top">
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
                    <input type="email" id="test_email" name="test_email" placeholder="收件人邮箱"
                    class="regular-text" style="width: 220px; display:inline-block;" />
                    <button type="button" id="send_test_email" class="button button-secondary" style="display:inline-block; margin-left: 10px;">
                        <span class="dashicons dashicons-email" style="vertical-align: middle;"></span>
                        发送测试邮件
                    </button>
                    <span class="spinner" style="float: none; margin-top: 0;"></span>
                    <div id="test_email_result" style="margin-top: 10px;"></div>
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

    $sanitized['host']      = sanitize_text_field( $input['host'] );
    $sanitized['port']      = absint( $input['port'] );
    $sanitized['encryption']  = sanitize_text_field( $input['encryption'] );
    $sanitized['username']  = sanitize_text_field( $input['username'] );
    $sanitized['password']  = sanitize_text_field( $input['password'] );
    $sanitized['from']      = sanitize_email( $input['from'] );
    $sanitized['from_name'] = sanitize_text_field( $input['from_name'] );

    return $sanitized;
}

/**
 * SMTP 配置 hook
 */
add_action('phpmailer_init', 'wp_starter_kit_smtp_configure_phpmailer');
function wp_starter_kit_smtp_configure_phpmailer( $phpmailer ) {
    $options = get_option( 'wp_starter_kit_smtp_options' );

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

    $subject = '这是一封测试邮件';
    $message = '恭喜你，SMTP 配置成功！这是一封测试邮件。';
    $headers = array('Content-Type: text/html; charset=UTF-8');

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

/**
 * 修改测试邮件部分的 HTML
 */
function wp_starter_kit_smtp_test_email_field() {
    ?>
    <tr valign="top">
        <th scope="row">测试发送邮件</th>
        <td>
            <div id="test_email_form">
                <?php wp_nonce_field('wp_starter_kit_test_email', 'wp_starter_kit_test_email_nonce'); ?>
                <input type="email" id="test_email" name="test_email" placeholder="收件人邮箱"
                    class="regular-text" style="width: 220px; display:inline-block;" />
                <button type="button" id="send_test_email" class="button button-secondary" style="display:inline-block; margin-left: 10px;">
                    <span class="dashicons dashicons-email" style="vertical-align: middle;"></span>
                    发送测试邮件
                </button>
                <span class="spinner" style="float: none; margin-top: 0;"></span>
                <div id="test_email_result" style="margin-top: 10px;"></div>
            </div>
        </td>
    </tr>
    <?php
}

/**
 * 修改 JavaScript 代码
 */
add_action('admin_footer', 'wp_starter_kit_smtp_test_email_js');
function wp_starter_kit_smtp_test_email_js() {
    if (get_current_screen()->id !== 'tools_page_wp-starter-kit') {
        return;
    }
    ?>
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        $('#send_test_email').on('click', function() {
            var emailField = $('#test_email');
            var email = emailField.val();
            
            if (!email) {
                $('#test_email_result').html('<div class="notice notice-error"><p>请输入收件人邮箱地址</p></div>');
                return;
            }
            
            var $button = $(this);
            var $spinner = $('.spinner');
            var $result = $('#test_email_result');
            
            // 禁用按钮
            $button.prop('disabled', true);
            // 显示加载动画
            $spinner.css('visibility', 'visible');
            // 清空结果
            $result.html('');
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'wp_starter_kit_send_test_email',
                    test_email: email,
                    nonce: $('#wp_starter_kit_test_email_nonce').val()
                },
                success: function(response) {
                    if (response.success) {
                        $result.html('<div class="notice notice-success"><p>' + response.data + '</p></div>');
                    } else {
                        $result.html('<div class="notice notice-error"><p>' + response.data + '</p></div>');
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    $result.html('<div class="notice notice-error"><p>发送请求时发生错误: ' + textStatus + '</p></div>');
                },
                complete: function() {
                    // 启用按钮
                    $button.prop('disabled', false);
                    // 隐藏加载动画
                    $spinner.css('visibility', 'hidden');
                }
            });
        });
    });
    </script>
    <?php
}

// 添加端口和加密方式联动的 JavaScript
add_action('admin_footer', 'wp_starter_kit_smtp_port_js');
function wp_starter_kit_smtp_port_js() {
    if (get_current_screen()->id !== 'tools_page_wp-starter-kit') {
        return;
    }
    ?>
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        $('#smtp_encryption').on('change', function() {
            var port = '25'; // 默认端口
            
            switch($(this).val()) {
                case 'ssl':
                    port = '465';
                    break;
                case 'tls':
                    port = '587';
                    break;
            }
            
            $('#smtp_port').val(port);
        });
    });
    </script>
    <?php
}

register_setting(
    'wp_starter_kit_smtp_group',
    'wp_starter_kit_smtp_options',
    'wp_starter_kit_smtp_sanitize'
);