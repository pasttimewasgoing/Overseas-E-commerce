=== 主题删除管理器 ===
Contributors: cyf
Tags: theme, delete, manager, remove
Requires at least: 5.0
Tested up to: 6.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

为WordPress主题页面添加删除功能，方便管理不需要的主题。

== Description ==

主题删除管理器为WordPress主题管理页面添加删除功能。

主要功能：
* 在主题操作中添加"删除"链接
* 删除前弹出确认对话框
* 不允许删除当前激活的主题
* 安全的权限检查
* 删除成功后显示提示信息

== Installation ==

1. 上传插件文件到 `/wp-content/plugins/theme-delete-manager/` 目录
2. 在WordPress后台"插件"菜单中激活插件
3. 进入"外观" > "主题"即可看到删除功能

== Frequently Asked Questions ==

= 可以删除当前激活的主题吗？ =

不可以。为了安全起见，不允许删除当前正在使用的主题。

= 删除的主题可以恢复吗？ =

不可以。删除操作会永久删除主题文件，无法恢复。请谨慎操作。

= 需要什么权限才能删除主题？ =

需要 delete_themes 权限，通常只有管理员才有此权限。

== Changelog ==

= 1.0.0 =
* 初始版本发布
* 添加主题删除功能
* 添加删除确认对话框
