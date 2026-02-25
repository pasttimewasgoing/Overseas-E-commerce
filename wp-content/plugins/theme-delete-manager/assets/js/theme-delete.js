jQuery(document).ready(function($) {
    // 删除主题确认
    $(document).on('click', '.tdm-delete-theme', function(e) {
        e.preventDefault();
        
        var $this = $(this);
        var themeName = $this.data('theme-name');
        var deleteUrl = $this.attr('href');
        
        // 第一次确认
        var firstConfirm = confirm(
            '⚠️ 警告：删除主题操作\n\n' +
            '您即将删除主题：' + themeName + '\n\n' +
            '此操作将永久删除该主题的所有文件，无法恢复！\n\n' +
            '确定要继续吗？'
        );
        
        if (!firstConfirm) {
            return false;
        }
        
        // 第二次确认
        var secondConfirm = confirm(
            '⚠️ 最后确认\n\n' +
            '请再次确认：您真的要删除主题 "' + themeName + '" 吗？\n\n' +
            '删除后将无法恢复！'
        );
        
        if (secondConfirm) {
            // 显示加载提示
            $this.text('删除中...').prop('disabled', true);
            window.location.href = deleteUrl;
        }
        
        return false;
    });
});
