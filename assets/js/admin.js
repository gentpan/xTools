jQuery(document).ready(function($) {
    $('#clean_database').on('click', function() {
        var $button = $(this);
        var $spinner = $button.next('.spinner');
        var $result = $('#clean_result');
        
        // 禁用按钮
        $button.prop('disabled', true);
        // 显示加载动画
        $spinner.css('visibility', 'visible');
        // 清空结果
        $result.html('');
        
        $.ajax({
            url: wpStarterKit.ajaxurl,
            type: 'POST',
            data: {
                action: 'wp_starter_kit_clean_database',
                nonce: wpStarterKit.nonce
            },
            success: function(response) {
                if (response.success) {
                    $result.html('<div class="notice notice-success"><p>' + response.data + '</p></div>');
                } else {
                    $result.html('<div class="notice notice-error"><p>' + response.data + '</p></div>');
                }
            },
            error: function() {
                $result.html('<div class="notice notice-error"><p>处理请求时发生错误</p></div>');
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