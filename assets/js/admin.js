jQuery(document).ready(function($) {
    $('#clean_database').on('click', function() {
        var $button = $(this);
        var $spinner = $button.next('.spinner');
        var $result = $('#clean_result');
        
        // 禁用按钮
        $button.prop('disabled', true);
        // 显示加载动画
        $spinner.addClass('is-active');
        // 清空结果
        $result.html('');
        
        $.ajax({
            url: wpStarterKit.ajaxurl,
            type: 'POST',
            data: {
                action: 'wp_starter_kit_clean_database',
                nonce: wpStarterKit.cleanNonce
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
                $spinner.removeClass('is-active');
            }
        });
    });

    $('#send_test_email').on('click', function() {
        var $button = $(this);
        var $form = $('#test_email_form');
        var $emailField = $('#test_email');
        var $spinner = $form.find('.spinner');
        var $result = $('#test_email_result');
        var email = $emailField.val();
        var subject = $('#test_email_subject').val();
        var message = $('#test_email_message').val();
        var contentType = $('#test_email_content_type').val();
        var nonce = wpStarterKit.testEmailNonce || $('#wp_starter_kit_test_email_nonce').val();

        if (!email) {
            $result.html('<div class="notice notice-error inline"><p>请输入收件人邮箱地址</p></div>');
            return;
        }

        $button.prop('disabled', true);
        $spinner.addClass('is-active');
        $result.html('');

        $.ajax({
            url: wpStarterKit.ajaxurl,
            type: 'POST',
            data: {
                action: 'wp_starter_kit_send_test_email',
                test_email: email,
                test_subject: subject,
                test_message: message,
                test_content_type: contentType,
                nonce: nonce
            },
            success: function(response) {
                if (response.success) {
                    $result.html('<div class="notice notice-success inline"><p>' + response.data + '</p></div>');
                } else {
                    $result.html('<div class="notice notice-error inline"><p>' + response.data + '</p></div>');
                }
            },
            error: function(jqXHR, textStatus) {
                $result.html('<div class="notice notice-error inline"><p>发送请求时发生错误: ' + textStatus + '</p></div>');
            },
            complete: function() {
                $button.prop('disabled', false);
                $spinner.removeClass('is-active');
            }
        });
    });

    $('#smtp_encryption').on('change', function() {
        var port = '25';

        switch ($(this).val()) {
            case 'ssl':
                port = '465';
                break;
            case 'tls':
                port = '587';
                break;
        }

        $('#smtp_port').val(port);
    });

    function setMailModeUI(mode) {
        var showSmtp = mode === 'smtp';
        $('.wp-starter-kit-smtp-row').toggleClass('hidden', !showSmtp);
        $('.wp-starter-kit-resend-row').toggleClass('hidden', showSmtp);
    }

    $('#mail_send_mode').on('change', function() {
        setMailModeUI($(this).val());
    });

    if ($('#mail_send_mode').length) {
        setMailModeUI($('#mail_send_mode').val());
    }

    function setReplaceLog(lines) {
        $('#replace_id_log').text((lines || []).join('\n'));
    }

    function setReplaceResult(lines, success) {
        var noticeClass = success ? 'notice notice-success inline' : 'notice notice-error inline';
        $('#replace_id_result').html('<div class="' + noticeClass + '"><p>' + (lines || []).join('<br>') + '</p></div>');
    }

    function downloadText(lines, filename, mime) {
        var blob = new Blob([lines.join('\n')], { type: mime });
        var url = URL.createObjectURL(blob);
        var link = document.createElement('a');
        link.href = url;
        link.download = filename;
        link.click();
        URL.revokeObjectURL(url);
    }

    $('#replace_id_btn').on('click', function() {
        var $button = $(this);
        var $spinner = $('#replace_id_spinner');
        var oldId = parseInt($('#replace_old_id').val(), 10) || 0;
        var newId = parseInt($('#replace_new_id').val(), 10) || 0;
        var postType = $('#replace_post_type').val();

        if (!oldId || !newId || oldId === newId) {
            setReplaceResult(['旧 ID 和新 ID 必须为不同的正整数。'], false);
            return;
        }

        $button.prop('disabled', true);
        $spinner.addClass('is-active');
        $('#replace_id_result').empty();
        setReplaceLog(['正在执行替换，请稍候...']);

        $.ajax({
            url: wpStarterKit.ajaxurl,
            type: 'POST',
            data: {
                action: 'wp_starter_kit_replace_post_id',
                nonce: wpStarterKit.replaceIdNonce,
                old_id: oldId,
                new_id: newId,
                post_type: postType
            },
            success: function(response) {
                var log = (response && response.data && response.data.log) ? response.data.log : ['执行失败。'];
                setReplaceLog(log);
                setReplaceResult(log, !!response.success);
                $('#replace_log_txt, #replace_log_csv').prop('disabled', false).data('log', log);
            },
            error: function() {
                var log = ['请求失败，请检查网络或登录状态。'];
                setReplaceLog(log);
                setReplaceResult(log, false);
            },
            complete: function() {
                $button.prop('disabled', false);
                $spinner.removeClass('is-active');
            }
        });
    });

    $('#replace_log_txt').on('click', function() {
        var log = $(this).data('log') || [];
        if (!log.length) {
            return;
        }
        downloadText(log, 'replace-id-log.txt', 'text/plain');
    });

    $('#replace_log_csv').on('click', function() {
        var log = $(this).data('log') || [];
        if (!log.length) {
            return;
        }
        var csv = log.map(function(line) {
            return '"' + String(line).replace(/"/g, '""') + '"';
        });
        downloadText(csv, 'replace-id-log.csv', 'text/csv');
    });

    function renderReplaceListSummary(data) {
        var pagination = data.pagination || {};
        var summary = data.db_summary || {};
        var typeCounts = summary.type_counts || [];
        var typeText = typeCounts.slice(0, 8).map(function(item) {
            return item.post_type + ': ' + item.total;
        }).join(' | ');

        var html = '<div class="notice notice-info inline"><p>' +
            '筛选结果：' + (pagination.total || 0) + ' 条；' +
            '当前页：' + (pagination.page || 1) + '/' + (pagination.total_pages || 1) + '；' +
            '数据库总记录：' + (summary.posts_total || 0) + '；' +
            '最大 ID：' + (summary.max_id || 0) + '；' +
            'ID 空洞估算：' + (summary.estimated_id_gaps || 0) +
            '</p>' +
            (typeText ? '<p>按类型统计（前8项）：' + typeText + '</p>' : '') +
            '</div>';

        $('#replace_list_summary').html(html);
    }

    function renderReplaceListTable(items) {
        var $tbody = $('#replace_list_table tbody');
        if (!items || !items.length) {
            $tbody.html('<tr><td colspan="8">没有匹配的数据。</td></tr>');
            return;
        }

        var rows = items.map(function(item) {
            return '<tr>' +
                '<td>' + item.id + '</td>' +
                '<td>' + item.title + '</td>' +
                '<td>' + item.type + '</td>' +
                '<td>' + item.status + '</td>' +
                '<td>' + item.date + '</td>' +
                '<td>' + item.parent + '</td>' +
                '<td>' + item.comment_count + '</td>' +
                '<td><button type="button" class="button button-small replace-use-id" data-id="' + item.id + '">设为旧 ID</button></td>' +
                '</tr>';
        });

        $tbody.html(rows.join(''));
    }

    function updateReplacePagination(pagination) {
        pagination = pagination || {};
        $('#replace_list_prev').prop('disabled', !pagination.has_prev);
        $('#replace_list_next').prop('disabled', !pagination.has_next);
        $('#replace_list_page_info').text('第 ' + (pagination.page || 1) + ' / ' + (pagination.total_pages || 1) + ' 页');
    }

    function loadReplaceList(page) {
        var $spinner = $('#replace_list_spinner');
        var postType = $('#replace_list_post_type').val();
        var status = $('#replace_list_status').val();
        var search = $('#replace_list_search').val();
        var perPage = parseInt($('#replace_list_per_page').val(), 10) || 20;
        var currentPage = page || 1;

        $spinner.addClass('is-active');
        $('#replace_list_load, #replace_list_prev, #replace_list_next').prop('disabled', true);

        $.ajax({
            url: wpStarterKit.ajaxurl,
            type: 'POST',
            data: {
                action: 'wp_starter_kit_replace_id_list',
                nonce: wpStarterKit.replaceListNonce,
                post_type: postType,
                status: status,
                search: search,
                per_page: perPage,
                page: currentPage
            },
            success: function(response) {
                if (!response.success || !response.data) {
                    $('#replace_list_summary').html('<div class="notice notice-error inline"><p>列表加载失败。</p></div>');
                    return;
                }
                var data = response.data;
                renderReplaceListSummary(data);
                renderReplaceListTable(data.items || []);
                updateReplacePagination(data.pagination || {});
                $('#replace_list_prev').data('page', (data.pagination.page || 1) - 1);
                $('#replace_list_next').data('page', (data.pagination.page || 1) + 1);
            },
            error: function() {
                $('#replace_list_summary').html('<div class="notice notice-error inline"><p>列表请求失败，请稍后重试。</p></div>');
            },
            complete: function() {
                $spinner.removeClass('is-active');
                $('#replace_list_load').prop('disabled', false);
            }
        });
    }

    $('#replace_list_load').on('click', function() {
        loadReplaceList(1);
    });

    $('#replace_list_prev').on('click', function() {
        var page = $(this).data('page') || 1;
        if (page > 0) {
            loadReplaceList(page);
        }
    });

    $('#replace_list_next').on('click', function() {
        var page = $(this).data('page') || 1;
        loadReplaceList(page);
    });

    $(document).on('click', '.replace-use-id', function() {
        var id = $(this).data('id');
        $('#replace_old_id').val(id);
        if ($('#replace_new_id').length) {
            $('#replace_new_id').trigger('focus');
        }
    });
});
