# WordPress Media Library Fix Tools

## 🎯 Quick Start (快速开始)

Your WordPress media library is showing gray placeholders instead of images. Follow these steps:

您的 WordPress 媒体库显示灰色占位符而不是图片。按照以下步骤操作：

### Step 1: Diagnose (诊断)
Visit in browser (在浏览器中访问):
```
http://your-site.com/check-media.php
```

### Step 2: Fix (修复)
Visit in browser (在浏览器中访问):
```
http://your-site.com/fix-media-urls.php
```

### Step 3: Clear Cache (清除缓存)
- WordPress cache (WordPress 缓存)
- Browser cache: Ctrl+Shift+Delete (浏览器缓存)

### Step 4: Verify (验证)
Check your media library (检查媒体库)

## 📁 Files Created (已创建的文件)

### 1. `check-media.php` - Diagnostic Tool (诊断工具)
**Purpose (用途)**: Comprehensive media library diagnostics
- Check site URL configuration (检查站点 URL 配置)
- Check upload directory permissions (检查上传目录权限)
- List recent images (列出最近的图片)
- Analyze URL paths (分析 URL 路径)
- Test image access (测试图片访问)

### 2. `fix-media-urls.php` - Fix Tool (修复工具)
**Purpose (用途)**: Automatically fix image URLs
- Detect wrong URL patterns (检测错误的 URL 模式)
- Update database URLs (更新数据库 URL)
- Fix localhost/domain issues (修复 localhost/域名问题)

### 3. `媒体库修复说明.md` - Chinese Guide (中文指南)
Complete troubleshooting guide in Chinese (完整的中文故障排除指南)

### 4. `MEDIA-FIX-README.md` - This File (本文件)
Quick reference guide (快速参考指南)

## 🔍 Common Issues (常见问题)

### Issue 1: Wrong URLs (URL 不正确)
**Symptom (症状)**: URLs contain localhost or old domain
**Solution (解决方案)**: Use fix-media-urls.php

### Issue 2: File Permissions (文件权限)
**Symptom (症状)**: Cannot upload images
**Solution (解决方案)**:
```bash
chmod -R 755 wp-content/uploads
```

### Issue 3: .htaccess Blocking (htaccess 阻止)
**Symptom (症状)**: 403/404 errors when accessing images directly
**Solution (解决方案)**: Check wp-content/uploads/.htaccess

### Issue 4: Cache Issues (缓存问题)
**Symptom (症状)**: Still seeing placeholders after fix
**Solution (解决方案)**: Clear all caches

## ⚠️ Important Notes (重要提示)

### Before Fixing (修复前)
1. **Backup your database** (备份数据库)
2. **Note your current site URL** (记录当前站点 URL)
3. **Check if files exist on server** (检查文件是否存在于服务器)

### After Fixing (修复后)
1. **Clear all caches** (清除所有缓存)
2. **Test media library** (测试媒体库)
3. **Delete fix tools for security** (删除修复工具以确保安全)

## 🗑️ Cleanup (清理)

After successful fix, delete these files (修复成功后删除这些文件):
```bash
rm check-media.php
rm fix-media-urls.php
rm 媒体库修复说明.md
rm MEDIA-FIX-README.md
```

## 📊 What the Tools Check (工具检查内容)

### check-media.php checks (检查):
- ✅ Site URL configuration (站点 URL 配置)
- ✅ Upload directory exists (上传目录存在)
- ✅ Directory is writable (目录可写)
- ✅ Recent images list (最近图片列表)
- ✅ File existence (文件存在性)
- ✅ URL patterns (URL 模式)
- ✅ Image preview (图片预览)

### fix-media-urls.php fixes (修复):
- 🔧 localhost URLs → actual domain
- 🔧 127.0.0.1 URLs → actual domain
- 🔧 Old domain → new domain
- 🔧 Wrong upload paths
- 🔧 Attachment metadata

## 🎓 Understanding the Problem (理解问题)

### Why Images Don't Show (为什么图片不显示)

1. **URL Mismatch (URL 不匹配)**
   - Database has old URLs (数据库有旧 URL)
   - Site URL changed (站点 URL 改变了)
   - Migration from localhost (从 localhost 迁移)

2. **File Issues (文件问题)**
   - Files don't exist (文件不存在)
   - Wrong permissions (权限错误)
   - Corrupted uploads (上传损坏)

3. **Server Issues (服务器问题)**
   - .htaccess blocking (htaccess 阻止)
   - Server misconfiguration (服务器配置错误)
   - Missing PHP extensions (缺少 PHP 扩展)

4. **Cache Issues (缓存问题)**
   - Old cached data (旧缓存数据)
   - CDN cache (CDN 缓存)
   - Browser cache (浏览器缓存)

## 🔐 Security (安全)

These tools access your database and should be:
这些工具访问您的数据库，应该：

- ✅ Used only when needed (仅在需要时使用)
- ✅ Deleted after use (使用后删除)
- ✅ Not left on production server (不要留在生产服务器上)
- ✅ Protected by admin authentication (受管理员身份验证保护)

## 📞 Need Help? (需要帮助？)

If issues persist, provide (如果问题仍然存在，请提供):

1. Screenshot from check-media.php (check-media.php 的截图)
2. Browser console errors (F12) (浏览器控制台错误)
3. Direct image URL test result (直接图片 URL 测试结果)
4. Server error logs (服务器错误日志)
5. System info (系统信息):
   - WordPress version
   - PHP version
   - Server type (Apache/Nginx)
   - Hosting provider

## 📝 Manual SQL Fix (手动 SQL 修复)

If tools don't work, use phpMyAdmin (如果工具不起作用，使用 phpMyAdmin):

```sql
-- Backup first! (先备份！)

-- Fix post URLs (修复文章 URL)
UPDATE wp_posts 
SET guid = REPLACE(guid, 'http://localhost', 'http://your-actual-domain.com') 
WHERE post_type = 'attachment';

-- Fix metadata (修复元数据)
UPDATE wp_postmeta 
SET meta_value = REPLACE(meta_value, 'http://localhost', 'http://your-actual-domain.com') 
WHERE meta_key = '_wp_attached_file';

-- Fix content URLs (修复内容 URL)
UPDATE wp_posts 
SET post_content = REPLACE(post_content, 'http://localhost', 'http://your-actual-domain.com');

-- Fix options (修复选项)
UPDATE wp_options 
SET option_value = REPLACE(option_value, 'http://localhost', 'http://your-actual-domain.com') 
WHERE option_name IN ('siteurl', 'home');
```

## ✅ Success Checklist (成功检查清单)

- [ ] Ran check-media.php (运行了 check-media.php)
- [ ] Identified the problem (确定了问题)
- [ ] Ran fix-media-urls.php (运行了 fix-media-urls.php)
- [ ] Cleared WordPress cache (清除了 WordPress 缓存)
- [ ] Cleared browser cache (清除了浏览器缓存)
- [ ] Verified images show in media library (验证图片在媒体库中显示)
- [ ] Tested on frontend (在前端测试)
- [ ] Deleted fix tools (删除了修复工具)

---

**Created**: 2026-02-28  
**Version**: 1.0  
**Compatibility**: WordPress 5.0+
