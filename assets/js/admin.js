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
        var templateId = $('#test_email_template_id').val();
        var templateVars = $('#test_email_template_vars').val();
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
                test_template_id: templateId,
                test_template_vars: templateVars,
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

    function setCustomCdnRowUI() {
        var isCustom = $('#cdn_url_select').val() === 'custom';
        $('#custom_cdn_url_row').toggleClass('hidden', !isCustom);
    }

    $('#cdn_url_select').on('change', setCustomCdnRowUI);
    if ($('#cdn_url_select').length) {
        setCustomCdnRowUI();
    }

    var templateState = {
        templates: [],
        currentId: ''
    };

    function readTemplateInitialData() {
        var node = document.getElementById('mail-template-initial-data');
        if (!node) {
            return;
        }
        try {
            var parsed = JSON.parse(node.textContent || '[]');
            templateState.templates = Array.isArray(parsed) ? parsed : [];
            if (templateState.templates.length) {
                templateState.currentId = templateState.templates[0].id;
            }
        } catch (e) {
            templateState.templates = [];
        }
    }

    function renderTemplateSelect() {
        var $select = $('#mail_template_select');
        if (!$select.length) {
            return;
        }

        var html = templateState.templates.map(function(tpl) {
            var label = tpl.name + (tpl.is_default ? '（默认）' : '');
            return '<option value="' + tpl.id + '">' + label + '</option>';
        }).join('');
        $select.html(html);
        if (templateState.currentId) {
            $select.val(templateState.currentId);
        }
    }

    function getCurrentTemplate() {
        var id = templateState.currentId || $('#mail_template_select').val();
        var item = templateState.templates.find(function(tpl) { return tpl.id === id; });
        return item || null;
    }

    function fillTemplateForm(template) {
        if (!template) {
            return;
        }
        $('#mail_template_name').val(template.name || '');
        $('#mail_template_subject').val(template.subject || '');
        $('#mail_template_content_type').val(template.content_type || 'html');
        $('#mail_template_html_body').val(template.html_body || '');
        $('#mail_template_plain_body').val(template.plain_body || '');
        $('#mail_template_enabled').prop('checked', !!template.enabled);
        $('#mail_template_default').prop('checked', !!template.is_default);
    }

    function collectTemplateForm() {
        var id = templateState.currentId || ('tpl_' + Date.now());
        return {
            id: id,
            name: $('#mail_template_name').val(),
            subject: $('#mail_template_subject').val(),
            content_type: $('#mail_template_content_type').val(),
            html_body: $('#mail_template_html_body').val(),
            plain_body: $('#mail_template_plain_body').val(),
            enabled: $('#mail_template_enabled').is(':checked') ? 1 : 0,
            is_default: $('#mail_template_default').is(':checked') ? 1 : 0
        };
    }

    function setTemplateResult(message, success) {
        var noticeClass = success ? 'notice notice-success inline' : 'notice notice-error inline';
        $('#mail_template_result').html('<div class="' + noticeClass + '"><p>' + message + '</p></div>');
    }

    function syncTemplateState(templates, fallbackId) {
        templateState.templates = Array.isArray(templates) ? templates : [];
        if (!templateState.templates.length) {
            templateState.currentId = '';
            return;
        }
        if (fallbackId && templateState.templates.some(function(tpl) { return tpl.id === fallbackId; })) {
            templateState.currentId = fallbackId;
        } else {
            templateState.currentId = templateState.templates[0].id;
        }
        renderTemplateSelect();
        fillTemplateForm(getCurrentTemplate());
    }

    function templateAjax(op, extraData, done) {
        var $spinner = $('#mail_template_spinner');
        if ($spinner.length) {
            $spinner.addClass('is-active');
        }

        var payload = $.extend({
            action: 'wp_starter_kit_mail_template_manage',
            nonce: wpStarterKit.mailTemplateNonce,
            op: op
        }, extraData || {});

        $.ajax({
            url: wpStarterKit.ajaxurl,
            type: 'POST',
            data: payload,
            success: function(response) {
                if (done) {
                    done(response);
                }
            },
            error: function() {
                setTemplateResult('请求失败，请稍后重试。', false);
            },
            complete: function() {
                if ($spinner.length) {
                    $spinner.removeClass('is-active');
                }
            }
        });
    }

    readTemplateInitialData();
    if (templateState.templates.length && $('#mail_template_select').length) {
        renderTemplateSelect();
        fillTemplateForm(getCurrentTemplate());
    }

    $('#mail_template_select').on('change', function() {
        templateState.currentId = $(this).val();
        fillTemplateForm(getCurrentTemplate());
    });

    $('#mail_template_new').on('click', function() {
        var id = 'tpl_' + Date.now();
        var tpl = {
            id: id,
            name: '新模板',
            subject: '【{{site_name}}】通知',
            content_type: 'html',
            html_body: '<p>Hello {{user_name}}</p>',
            plain_body: 'Hello {{user_name}}',
            enabled: 1,
            is_default: 0
        };
        templateState.templates.unshift(tpl);
        templateState.currentId = id;
        renderTemplateSelect();
        fillTemplateForm(tpl);
        setTemplateResult('已创建草稿模板，点击“保存模板”生效。', true);
    });

    $('#mail_template_duplicate').on('click', function() {
        var current = getCurrentTemplate();
        if (!current) {
            setTemplateResult('请先选择模板。', false);
            return;
        }
        templateAjax('duplicate', { template_id: current.id }, function(response) {
            if (!response.success) {
                setTemplateResult((response.data && response.data.message) || '复制失败', false);
                return;
            }
            syncTemplateState(response.data.templates);
            setTemplateResult(response.data.message || '复制成功', true);
        });
    });

    $('#mail_template_delete').on('click', function() {
        var current = getCurrentTemplate();
        if (!current) {
            setTemplateResult('请先选择模板。', false);
            return;
        }
        if (!window.confirm('确定删除该模板吗？')) {
            return;
        }
        templateAjax('delete', { template_id: current.id }, function(response) {
            if (!response.success) {
                setTemplateResult((response.data && response.data.message) || '删除失败', false);
                return;
            }
            syncTemplateState(response.data.templates);
            setTemplateResult(response.data.message || '删除成功', true);
        });
    });

    $('#mail_template_save').on('click', function() {
        var template = collectTemplateForm();
        templateState.currentId = template.id;
        templateAjax('save', { template: template }, function(response) {
            if (!response.success) {
                setTemplateResult((response.data && response.data.message) || '保存失败', false);
                return;
            }
            syncTemplateState(response.data.templates, template.id);
            setTemplateResult(response.data.message || '保存成功', true);
        });
    });

    $('#mail_template_preview').on('click', function() {
        var current = getCurrentTemplate();
        if (!current) {
            setTemplateResult('请先选择模板。', false);
            return;
        }
        templateAjax('preview', {
            template_id: current.id,
            vars_json: $('#mail_template_vars').val()
        }, function(response) {
            if (!response.success) {
                setTemplateResult((response.data && response.data.message) || '预览失败', false);
                return;
            }
            var rendered = response.data.rendered || {};
            $('#mail_template_preview_box').text(
                'Subject: ' + (rendered.subject || '') + '\n\n' +
                'HTML:\n' + (rendered.html_body || '') + '\n\n' +
                'Plain:\n' + (rendered.plain_body || '')
            );
            setTemplateResult('预览成功', true);
        });
    });

    $('#mail_template_send_test').on('click', function() {
        var current = getCurrentTemplate();
        var to = $('#mail_template_test_to').val();
        if (!current) {
            setTemplateResult('请先选择模板。', false);
            return;
        }
        if (!to) {
            setTemplateResult('请填写测试收件人邮箱。', false);
            return;
        }
        templateAjax('send_test', {
            template_id: current.id,
            to: to,
            vars_json: $('#mail_template_vars').val()
        }, function(response) {
            if (!response.success) {
                setTemplateResult((response.data && response.data.message) || '发送失败', false);
                return;
            }
            setTemplateResult(response.data.message || '发送成功', true);
        });
    });

    var dbmState = {
        table: '',
        page: 1,
        perPage: 20,
        primaryKey: '',
        rows: [],
        backups: []
    };

    function dbmEscape(text) {
        return String(text === null || typeof text === 'undefined' ? '' : text)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }

    function dbmNotice(message, success) {
        var klass = success ? 'notice notice-success inline' : 'notice notice-error inline';
        $('#dbm_result').html('<div class="' + klass + '"><p>' + dbmEscape(message) + '</p></div>');
    }

    function dbmBackupNotice(message, success) {
        dbmNotice(message, success);
    }

    function dbmAjax(op, data, done) {
        var $spinner = $('#dbm_spinner');
        $spinner.addClass('is-active');
        $.ajax({
            url: wpStarterKit.ajaxurl,
            type: 'POST',
            data: $.extend({
                action: 'wp_starter_kit_db_manager',
                nonce: wpStarterKit.dbManagerNonce,
                op: op
            }, data || {}),
            success: function(response) {
                if (done) {
                    done(response);
                }
            },
            error: function() {
                dbmNotice('数据库操作请求失败。', false);
            },
            complete: function() {
                $spinner.removeClass('is-active');
            }
        });
    }

    function dbmRenderTables(tables) {
        var $select = $('#dbm_table_select');
        if (!$select.length) {
            return;
        }
        var options = (tables || []).map(function(table) {
            return '<option value="' + dbmEscape(table) + '">' + dbmEscape(table) + '</option>';
        });
        $select.html(options.join(''));
        dbmState.table = $select.val() || '';
    }

    function dbmRenderRows(data) {
        var columns = data.columns || [];
        var rows = data.rows || [];
        dbmState.primaryKey = data.primary_key || '';
        dbmState.rows = rows;

        var headHtml = columns.map(function(col) {
            return '<th>' + dbmEscape(col) + '</th>';
        }).join('') + '<th>操作</th>';
        $('#dbm_rows_head').html(headHtml);

        var bodyHtml = '';
        if (!rows.length) {
            bodyHtml = '<tr><td colspan="' + (columns.length + 1) + '">当前页没有数据。</td></tr>';
        } else {
            bodyHtml = rows.map(function(row, idx) {
                var tds = columns.map(function(col) {
                    return '<td>' + dbmEscape(row[col]) + '</td>';
                }).join('');
                return '<tr>' + tds + '<td><button type="button" class="button button-small dbm-edit-row" data-row-index="' + idx + '">编辑</button></td></tr>';
            }).join('');
        }
        $('#dbm_rows_table tbody').html(bodyHtml);

        var p = data.pagination || {};
        $('#dbm_prev_page').prop('disabled', !p.has_prev);
        $('#dbm_next_page').prop('disabled', !p.has_next);
        $('#dbm_page_info').text('第 ' + (p.page || 1) + ' / ' + (p.total_pages || 1) + ' 页，共 ' + (p.total || 0) + ' 条');
    }

    function dbmRenderBackups(backups) {
        dbmState.backups = backups || [];
        var $select = $('#dbm_backup_select');
        if (!$select.length) {
            return;
        }
        if (!dbmState.backups.length) {
            $select.html('<option value="">暂无备份</option>');
            return;
        }
        var options = dbmState.backups.map(function(item) {
            var sizeKb = Math.round((item.size || 0) / 1024);
            var label = item.name + ' (' + sizeKb + 'KB, ' + item.modified + ' UTC)';
            return '<option value="' + dbmEscape(item.name) + '">' + dbmEscape(label) + '</option>';
        });
        $select.html(options.join(''));
    }

    function dbmLoadBackups() {
        var $spinner = $('#dbm_backup_spinner');
        $spinner.addClass('is-active');
        dbmAjax('backup_list', {}, function(response) {
            if (!response.success) {
                dbmBackupNotice((response.data && response.data.message) || '读取备份列表失败', false);
                $spinner.removeClass('is-active');
                return;
            }
            dbmRenderBackups(response.data.backups || []);
            dbmBackupNotice('备份列表已更新。', true);
            $spinner.removeClass('is-active');
        });
    }

    function dbmLoadTables() {
        dbmAjax('tables', {}, function(response) {
            if (!response.success) {
                dbmNotice((response.data && response.data.message) || '读取表失败', false);
                return;
            }
            dbmRenderTables(response.data.tables || []);
            dbmNotice('数据表读取成功。', true);
        });
    }

    function dbmLoadRows(page) {
        var table = $('#dbm_table_select').val();
        if (!table) {
            dbmNotice('请先选择数据表。', false);
            return;
        }
        dbmState.table = table;
        dbmState.page = page || 1;
        dbmState.perPage = parseInt($('#dbm_per_page').val(), 10) || 20;

        dbmAjax('rows', {
            table: dbmState.table,
            page: dbmState.page,
            per_page: dbmState.perPage
        }, function(response) {
            if (!response.success) {
                dbmNotice((response.data && response.data.message) || '读取记录失败', false);
                return;
            }
            dbmRenderRows(response.data);
            dbmNotice('记录读取成功。', true);
        });
    }

    if ($('#dbm_table_select').length) {
        dbmLoadTables();
        dbmLoadBackups();
    }

    $('#dbm_backup_refresh').on('click', function() {
        dbmLoadBackups();
    });

    $('#dbm_backup_create').on('click', function() {
        var $spinner = $('#dbm_backup_spinner');
        $spinner.addClass('is-active');
        dbmAjax('backup_create', {}, function(response) {
            if (!response.success) {
                dbmBackupNotice((response.data && response.data.message) || '备份失败', false);
                $spinner.removeClass('is-active');
                return;
            }
            dbmRenderBackups((response.data && response.data.backups) || []);
            dbmBackupNotice((response.data && response.data.message) || '备份成功', true);
            $spinner.removeClass('is-active');
        });
    });

    $('#dbm_backup_restore').on('click', function() {
        var fileName = $('#dbm_backup_select').val();
        if (!fileName) {
            dbmBackupNotice('请先选择备份文件。', false);
            return;
        }

        if (!window.confirm('恢复会覆盖当前数据库数据，确定继续吗？')) {
            return;
        }

        var $spinner = $('#dbm_backup_spinner');
        $spinner.addClass('is-active');
        dbmAjax('backup_restore', { file_name: fileName }, function(response) {
            if (!response.success) {
                dbmBackupNotice((response.data && response.data.message) || '恢复失败', false);
                $spinner.removeClass('is-active');
                return;
            }
            dbmRenderBackups((response.data && response.data.backups) || []);
            dbmBackupNotice((response.data && response.data.message) || '恢复成功', true);
            dbmLoadRows(1);
            $spinner.removeClass('is-active');
        });
    });

    $('#dbm_reload_tables').on('click', function() {
        dbmLoadTables();
    });

    $('#dbm_load_rows').on('click', function() {
        dbmLoadRows(1);
    });

    $('#dbm_prev_page').on('click', function() {
        if (dbmState.page > 1) {
            dbmLoadRows(dbmState.page - 1);
        }
    });

    $('#dbm_next_page').on('click', function() {
        dbmLoadRows(dbmState.page + 1);
    });

    $(document).on('click', '.dbm-edit-row', function() {
        var idx = parseInt($(this).data('row-index'), 10);
        var row = dbmState.rows[idx];
        if (!row) {
            return;
        }
        var pk = dbmState.primaryKey;
        $('#dbm_edit_pk').val(row[pk] || '');
        var editable = $.extend({}, row);
        delete editable[pk];
        $('#dbm_edit_json').val(JSON.stringify(editable, null, 2));
    });

    $('#dbm_save_row').on('click', function() {
        var table = dbmState.table || $('#dbm_table_select').val();
        var pk = dbmState.primaryKey;
        var pkValue = $('#dbm_edit_pk').val();
        var jsonText = $('#dbm_edit_json').val();
        var $spinner = $('#dbm_save_spinner');

        if (!table || !pk || !pkValue) {
            dbmNotice('请先从列表选择一条记录再编辑。', false);
            return;
        }

        try {
            JSON.parse(jsonText);
        } catch (e) {
            dbmNotice('字段 JSON 格式错误。', false);
            return;
        }

        $spinner.addClass('is-active');
        dbmAjax('update', {
            table: table,
            primary_key: pk,
            primary_value: pkValue,
            update_json: jsonText
        }, function(response) {
            if (!response.success) {
                dbmNotice((response.data && response.data.message) || '更新失败', false);
                $spinner.removeClass('is-active');
                return;
            }
            dbmNotice((response.data && response.data.message) || '更新成功', true);
            dbmLoadRows(dbmState.page || 1);
            $spinner.removeClass('is-active');
        });
    });

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
