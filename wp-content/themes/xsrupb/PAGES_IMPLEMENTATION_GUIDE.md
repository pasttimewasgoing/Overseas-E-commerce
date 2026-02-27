# XSRUPB 主题页面实施指南

## 当前状态

### 已完成页面
1. ✅ 首页 (`front-page.php`)
2. ✅ 产品列表页 (`woocommerce/archive-product.php`)
3. ✅ 产品详情页 (`woocommerce/single-product.php`)

### 待完成页面
1. ⏳ 购物车页面 (30% 完成)
2. ⏳ 关于我们
3. ⏳ 技术支持
4. ⏳ 联系我们
5. ⏳ 登录注册

## 快速实施步骤

### 步骤 1: 购物车页面完善

购物车页面已有基础实现，需要完善样式和功能。

**文件位置:**
- 模板: `woocommerce/cart/cart.php`
- 样式: `assets/css/cart.css`
- 脚本: `assets/js/cart.js`

**需要实现的功能:**
1. 购物车项目列表
2. 数量调整
3. 删除项目
4. 优惠券应用
5. 订单摘要
6. 推荐产品

**原型图参考:** `原型图/购物车/index.html`

### 步骤 2: 关于我们页面

**创建文件:**
```bash
# 模板文件
wp-content/themes/xsrupb/page-about.php

# 样式文件
wp-content/themes/xsrupb/assets/css/about.css

# 脚本文件
wp-content/themes/xsrupb/assets/js/about.js
```

**页面结构:**
1. 页面标题
2. 公司简介
3. 发展历程
4. 团队介绍
5. 企业文化
6. 资质认证

**原型图参考:** `原型图/关于我们/index.html`

**实施代码模板:**
```php
<?php
/**
 * Template Name: 关于我们
 */

get_header();
?>

<section class="about-section">
    <div class="container">
        <!-- 根据原型图实现内容 -->
    </div>
</section>

<?php
get_footer();
```

### 步骤 3: 技术支持页面

**创建文件:**
```bash
# 列表页模板
wp-content/themes/xsrupb/page-support.php

# 详情页模板（如果需要）
wp-content/themes/xsrupb/single-support.php

# 样式文件
wp-content/themes/xsrupb/assets/css/support.css

# 脚本文件
wp-content/themes/xsrupb/assets/js/support.js
```

**页面结构:**
1. 搜索框
2. 常见问题分类
3. 问题列表
4. 详情页面（可选）

**原型图参考:** 
- 列表: `原型图/技术支持/index.html`
- 详情: `原型图/技术支持/detail.html`

### 步骤 4: 联系我们页面

**创建文件:**
```bash
# 模板文件
wp-content/themes/xsrupb/page-contact.php

# 样式文件
wp-content/themes/xsrupb/assets/css/contact.css

# 脚本文件
wp-content/themes/xsrupb/assets/js/contact.js
```

**页面结构:**
1. 联系表单
2. 联系信息
3. 地图（可选）
4. 社交媒体链接

**原型图参考:** `原型图/联系我们/index.html`

### 步骤 5: 登录注册页面

**创建文件:**
```bash
# 登录页模板
wp-content/themes/xsrupb/page-login.php

# 注册页模板
wp-content/themes/xsrupb/page-register.php

# 样式文件
wp-content/themes/xsrupb/assets/css/auth.css

# 脚本文件
wp-content/themes/xsrupb/assets/js/auth.js
```

**页面结构:**
1. 登录表单
2. 注册表单
3. 忘记密码
4. 社交登录（可选）

**原型图参考:** `原型图/登录注册/index.html`

## 资源加载配置

需要在 `inc/class-asset-manager.php` 中添加条件加载：

```php
// 关于我们页面
if (is_page_template('page-about.php')) {
    wp_enqueue_style('xsrupb-about', get_template_directory_uri() . '/assets/css/about.css', array('xsrupb-main'), XSRUPB_VERSION);
    wp_enqueue_script('xsrupb-about', get_template_directory_uri() . '/assets/js/about.js', array('jquery'), XSRUPB_VERSION, true);
}

// 技术支持页面
if (is_page_template('page-support.php')) {
    wp_enqueue_style('xsrupb-support', get_template_directory_uri() . '/assets/css/support.css', array('xsrupb-main'), XSRUPB_VERSION);
    wp_enqueue_script('xsrupb-support', get_template_directory_uri() . '/assets/js/support.js', array('jquery'), XSRUPB_VERSION, true);
}

// 联系我们页面
if (is_page_template('page-contact.php')) {
    wp_enqueue_style('xsrupb-contact', get_template_directory_uri() . '/assets/css/contact.css', array('xsrupb-main'), XSRUPB_VERSION);
    wp_enqueue_script('xsrupb-contact', get_template_directory_uri() . '/assets/js/contact.js', array('jquery'), XSRUPB_VERSION, true);
}

// 登录注册页面
if (is_page_template('page-login.php') || is_page_template('page-register.php')) {
    wp_enqueue_style('xsrupb-auth', get_template_directory_uri() . '/assets/css/auth.css', array('xsrupb-main'), XSRUPB_VERSION);
    wp_enqueue_script('xsrupb-auth', get_template_directory_uri() . '/assets/js/auth.js', array('jquery'), XSRUPB_VERSION, true);
}
```

## 创建页面的 WordPress 步骤

1. **在 WordPress 后台创建页面:**
   - 进入 "页面" > "新建页面"
   - 输入页面标题
   - 选择对应的页面模板
   - 发布页面

2. **设置页面别名（Slug）:**
   - 关于我们: `about`
   - 技术支持: `support`
   - 联系我们: `contact`
   - 登录: `login`
   - 注册: `register`

3. **添加到导航菜单:**
   - 进入 "外观" > "菜单"
   - 添加创建的页面到主菜单
   - 保存菜单

## 通用页面模板结构

```php
<?php
/**
 * Template Name: 页面名称
 */

get_header();
?>

<!-- 面包屑导航（可选） -->
<section class="breadcrumb-section">
    <div class="container">
        <?php
        if (function_exists('woocommerce_breadcrumb')) {
            woocommerce_breadcrumb();
        }
        ?>
    </div>
</section>

<!-- 页面主体内容 -->
<section class="page-section">
    <div class="container">
        <?php
        while (have_posts()) :
            the_post();
            the_content();
        endwhile;
        ?>
    </div>
</section>

<?php
get_footer();
```

## 样式文件模板

```css
/* 页面名称样式 */

/* 页面主体 */
.page-section {
    padding: 60px 0;
    background: #fff;
}

/* 根据原型图添加具体样式 */

/* 响应式设计 */
@media (max-width: 1024px) {
    /* 平板样式 */
}

@media (max-width: 768px) {
    /* 移动端样式 */
}
```

## JavaScript 文件模板

```javascript
/**
 * 页面名称交互功能
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        // 页面特定的交互功能
    });

})(jQuery);
```

## 测试清单

每个页面完成后需要测试：

- [ ] 页面正常显示
- [ ] 样式与原型图一致
- [ ] 响应式布局正常
- [ ] 交互功能正常
- [ ] 表单提交正常（如有）
- [ ] 浏览器兼容性
- [ ] 性能优化

## 优先级建议

1. **高优先级:** 购物车（电商核心功能）
2. **中优先级:** 联系我们、关于我们（企业信息）
3. **低优先级:** 技术支持、登录注册（可后期完善）

## 注意事项

1. **保持一致性:** 所有页面使用相同的头部、尾部和导航
2. **响应式设计:** 确保所有页面在移动端正常显示
3. **性能优化:** 条件加载 CSS/JS，避免全局加载
4. **SEO 优化:** 使用适当的标题标签和 Meta 信息
5. **安全性:** 表单添加 nonce 验证和数据清理

## 需要帮助？

如果在实施过程中遇到问题，可以：
1. 参考已完成的首页和产品页面代码
2. 查看原型图的 HTML/CSS/JS 文件
3. 参考 WordPress 官方文档
4. 查看 WooCommerce 文档（购物车相关）

## 更新日期

2024-02-27
