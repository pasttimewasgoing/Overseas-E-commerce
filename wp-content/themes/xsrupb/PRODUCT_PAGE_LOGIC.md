# 产品页面逻辑说明

## 页面结构

### 1. 导航栏（Header）
- 显示主导航菜单
- "产品"下拉菜单显示所有产品分类
- 点击分类链接跳转到对应的产品页面

### 2. 头部横幅（Banner）
- 固定显示，不随分类变化
- 包含：
  - 背景图片占位符
  - 产品展示图片
  - 标题和描述
  - "热销产品"按钮（平滑滚动到产品区域）

### 3. 面包屑导航（Breadcrumb）
- 显示当前位置
- 格式：首页 / 产品分类 / 子分类

### 4. 产品主体区域

#### 左侧：分类导航
- 显示所有产品分类（从后台获取）
- 结构：
  ```
  产品类别
  ├─ 大分类1 🔸
  │  ├─ 小分类1-1
  │  ├─ 小分类1-2
  │  └─ 小分类1-3
  ├─ 大分类2 🔸
  │  ├─ 小分类2-1
  │  └─ 小分类2-2
  └─ 大分类3 🔸
  ```
- 行为：
  - 默认所有大分类折叠
  - 点击大分类展开/折叠子分类
  - 当前访问的分类自动展开并高亮
  - 点击分类链接跳转到对应页面

#### 右侧：产品列表
- 顶部：搜索框（右对齐）
- 产品网格：4列布局
- 产品卡片包含：
  - 产品图片（200px高）
  - 产品标题
  - 产品描述
  - 价格（红色）
  - 购买按钮（黑色）

## 产品显示逻辑

### 情况1：有产品
```php
if (woocommerce_product_loop()) {
    // 显示实际产品
    while (have_posts()) {
        the_post();
        // 显示产品卡片
    }
    
    // 如果产品数量超过每页显示数量，显示"查看更多"按钮
    if (total > per_page) {
        // 显示"查看更多产品"按钮
    }
}
```

### 情况2：无产品（显示占位符）
```php
else {
    // 显示12个占位符产品卡片
    for ($i = 0; $i < 12; $i++) {
        // 显示占位符卡片（灰色、禁用状态）
    }
    
    // 显示提示消息
    echo "该分类暂无产品，敬请期待";
}
```

## 占位符产品卡片

### 样式特点
- 半透明（opacity: 0.6）
- 图片区域：加载动画效果
- 文字：灰色
- 按钮：禁用状态
- 不可点击（pointer-events: none）

### 显示内容
- 图片：渐变加载动画
- 标题："产品名称"
- 描述："产品描述信息"
- 价格："¥0.00"
- 按钮："购买"（禁用）

## 分类导航逻辑

### 获取分类
```php
// 获取所有顶级分类
$product_categories = get_terms(array(
    'taxonomy'   => 'product_cat',
    'hide_empty' => false,  // 显示空分类
    'parent'     => 0,      // 只获取顶级分类
));

// 对每个顶级分类，获取子分类
$child_cats = get_terms(array(
    'taxonomy'   => 'product_cat',
    'hide_empty' => false,
    'parent'     => $category->term_id,
));
```

### 判断当前分类
```php
if (is_product_category()) {
    $current_cat = get_queried_object();
    $current_cat_id = $current_cat->term_id;
    $current_parent_id = $current_cat->parent;
}

// 判断是否为当前分类或其父级
$is_current_parent = ($current_cat_id == $category->term_id) 
                  || ($current_parent_id == $category->term_id);
```

### 展开/折叠行为
- 如果是当前分类或其父级：`style="display: block;"`
- 否则：`style="display: none;"`
- JavaScript 控制点击切换

## 搜索功能

### 搜索表单
```html
<form action="/" method="get">
    <input type="text" name="s" placeholder="搜索产品...">
    <input type="hidden" name="post_type" value="product">
    <button type="submit">搜索</button>
</form>
```

### 搜索结果
- 使用相同的产品页面布局
- 显示搜索关键词匹配的产品
- 如果无结果，显示占位符

## 分页功能

### WooCommerce 分页
- 使用 WooCommerce 内置分页
- 每页显示12个产品（可在后台设置）
- 分页样式：蓝色主题

### "查看更多"按钮
- 仅在产品总数 > 每页数量时显示
- 点击加载更多产品（可选实现 AJAX）

## 响应式布局

### 桌面端（>1024px）
- 产品网格：4列
- 分类侧边栏：280px宽
- 横幅：完整显示

### 平板端（768px-1024px）
- 产品网格：3列
- 分类侧边栏：全宽，移到顶部
- 横幅：垂直布局

### 移动端（<768px）
- 产品网格：2列
- 搜索框：全宽
- 横幅：简化显示

### 小屏幕（<480px）
- 产品网格：1列
- 所有元素垂直堆叠

## 性能优化

### 分类缓存
```php
$cache_key = 'xsrupb_product_categories';
$categories = get_transient($cache_key);

if (false === $categories) {
    $categories = get_terms(...);
    set_transient($cache_key, $categories, HOUR_IN_SECONDS);
}
```

### 图片懒加载
- 使用 WordPress 原生懒加载
- 或添加 Intersection Observer

### 条件加载
- 产品页面 CSS/JS 仅在产品页面加载
- 避免全局加载

## 用户体验

### 加载状态
- 占位符提供视觉反馈
- 加载动画表示内容即将到来

### 交互反馈
- 悬停效果：卡片上浮、阴影
- 点击反馈：按钮状态变化
- 平滑动画：0.3-0.4s 过渡

### 错误处理
- 无产品：显示友好提示
- 搜索无结果：显示占位符
- 加载失败：显示错误消息

## 测试清单

- [ ] 访问商店首页，显示所有产品
- [ ] 点击导航栏分类，跳转到分类页面
- [ ] 左侧分类正确显示和折叠
- [ ] 当前分类正确高亮
- [ ] 有产品时正确显示
- [ ] 无产品时显示占位符
- [ ] 搜索功能正常
- [ ] 分页功能正常
- [ ] 响应式布局正常
- [ ] 购买按钮正常工作

## 注意事项

1. **分类设置**
   - 在 WordPress 后台创建产品分类
   - 设置父子关系
   - 即使分类为空也会显示

2. **产品设置**
   - 为产品分配分类
   - 设置产品图片
   - 设置产品价格

3. **导航菜单**
   - 在"外观 > 菜单"中设置
   - 添加产品分类到菜单
   - 设置为下拉菜单

4. **性能考虑**
   - 使用缓存减少数据库查询
   - 优化图片大小
   - 启用 CDN（可选）

## 更新日期

2024-02-27
