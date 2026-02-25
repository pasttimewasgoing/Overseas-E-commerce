jQuery(document).ready(function($) {
    var $checkboxes = $('input[name="amm_hidden_menus[]"]');
    
    // 全选功能
    $('#amm-select-all').on('click', function() {
        $checkboxes.prop('checked', true);
        updateCount();
    });
    
    // 取消全选功能
    $('#amm-deselect-all').on('click', function() {
        $checkboxes.prop('checked', false);
        updateCount();
    });
    
    // 监听复选框变化
    $checkboxes.on('change', function() {
        updateCount();
    });
    
    // 更新选中数量提示
    function updateCount() {
        var checkedCount = $checkboxes.filter(':checked').length;
        if (checkedCount > 0) {
            console.log('已选择隐藏 ' + checkedCount + ' 个菜单项');
        } else {
            console.log('未选择任何菜单项，所有菜单将显示');
        }
    }
});
