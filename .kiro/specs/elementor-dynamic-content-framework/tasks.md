 # Implementation Plan: Elementor Dynamic Content Framework

## Overview

本实现计划将 Elementor Dynamic Content Framework 插件分解为可执行的开发任务。插件采用三层架构（数据层/布局层/渲染层），通过 PHP 实现 WordPress 插件系统，深度集成 Elementor 页面构建器。实现将按照数据层 → 布局层 → 渲染层的顺序进行，确保每一层都经过充分测试后再进入下一层。

## Tasks

- [x] 1. 设置插件基础结构和核心文件
  - 创建主插件文件 `elementor-dynamic-content-framework.php` 包含插件头信息
  - 创建插件激活/停用处理类 `DCF_Activator` 和 `DCF_Deactivator`
  - 创建钩子加载器 `DCF_Loader` 和国际化类 `DCF_i18n`
  - 设置插件目录结构（includes/, assets/, languages/, templates/）
  - 加载文本域用于多语言支持
  - _Requirements: 15.1, 15.2, 15.3_

- [x] 2. 实现数据库层核心功能
  - [x] 2.1 创建数据库管理器和表结构
    - 实现 `DCF_Database` 类的 `create_tables()` 方法
    - 创建 `wp_dcf_group_types` 表包含所有必需字段和索引
    - 创建 `wp_dcf_groups` 表包含外键约束
    - 创建 `wp_dcf_group_items` 表包含级联删除约束
    - 实现表存在性检查和版本管理方法
    - _Requirements: 1.1, 1.2, 1.3, 1.4, 1.5_

  - [x] 2.2 实现 Schema 解析和验证系统
    - 实现 `DCF_Schema_Parser::parse()` 方法解析 JSON 字符串
    - 实现 `DCF_Schema_Parser::is_valid_json()` 和错误处理
    - 实现 `DCF_Schema_Validator::validate()` 验证 Schema 数组
    - 实现 `DCF_Schema_Validator::validate_field()` 验证单个字段
    - 实现 `DCF_Schema_Validator::check_repeater_depth()` 检查嵌套深度不超过 3 层
    - 实现 `DCF_Schema_Printer::print()` 序列化 Schema 为 JSON
    - _Requirements: 21.1, 21.2, 21.3, 21.4, 21.5, 21.6, 21.7_

  - [ ]* 2.3 编写 Schema 解析器的单元测试
    - 测试有效 JSON 解析
    - 测试无效 JSON 错误处理
    - 测试 Schema 验证规则
    - 测试 repeater 嵌套深度限制
    - 测试 round-trip 属性（解析 → 序列化 → 解析）
    - _Requirements: 21.5_

  - [x] 2.4 实现 Content Item 数据序列化器
    - 实现 `DCF_Data_Serializer::serialize()` 序列化数据为 JSON
    - 实现 `DCF_Data_Serializer::deserialize()` 反序列化 JSON 为数组
    - 实现 `DCF_Data_Serializer::sanitize_value()` 处理特殊字符和 Unicode
    - 处理 WordPress shortcodes 的保留
    - _Requirements: 22.1, 22.2, 22.3, 22.4, 22.5, 22.6, 22.7, 22.8_

  - [ ]* 2.5 编写数据序列化器的单元测试
    - 测试基本数据类型序列化
    - 测试 Unicode 和特殊字符处理
    - 测试 shortcode 保留
    - 测试 round-trip 属性（序列化 → 反序列化 → 序列化）
    - _Requirements: 22.6_

- [x] 3. 实现数据模型层
  - [x] 3.1 实现 DCF_Group_Type 模型
    - 实现 `create()` 方法包含 Schema 验证
    - 实现 `get()`, `get_by_slug()`, `get_all()` 查询方法
    - 实现 `update()` 方法保持数据完整性
    - 实现 `delete()` 方法包含关联检查
    - 实现 `get_groups_count()` 统计方法
    - 使用 prepared statements 防止 SQL 注入
    - _Requirements: 2.5, 2.7, 16.8_

  - [x] 3.2 实现 DCF_Group 模型
    - 实现 `create()` 方法验证 type_id 存在性
    - 实现 `get()`, `get_all()` 查询方法支持筛选和分页
    - 实现 `update()` 方法更新 updated_at 时间戳
    - 实现 `delete()` 方法级联删除所有 Content Items
    - 实现 `get_items()` 和 `get_items_count()` 方法
    - _Requirements: 4.2, 4.3, 4.5, 4.6, 9.1_

  - [x] 3.3 实现 DCF_Group_Item 模型
    - 实现 `create()` 方法序列化数据为 JSON
    - 实现 `get()`, `get_by_group()` 查询方法按 sort_order 排序
    - 实现 `update()` 方法包含数据验证
    - 实现 `delete()` 方法
    - 实现 `update_order()` 批量更新排序
    - 实现 `duplicate()` 复制内容项
    - _Requirements: 5.1, 5.2, 5.4, 5.6, 5.8_

  - [ ]* 3.4 编写数据模型的集成测试
    - 测试 Group Type 的 CRUD 操作
    - 测试 Group 的 CRUD 操作和级联删除
    - 测试 Group Item 的 CRUD 操作和排序
    - 测试外键约束和数据完整性
    - _Requirements: 1.1, 1.2, 1.3_

- [x] 4. 实现缓存系统
  - [x] 4.1 创建缓存管理器
    - 实现 `DCF_Cache_Manager::get()` 使用 wp_cache_get
    - 实现 `DCF_Cache_Manager::set()` 使用 wp_cache_set
    - 实现 `DCF_Cache_Manager::delete()` 和 `flush_all()` 方法
    - 实现 `invalidate_group()` 使内容组缓存失效
    - 实现 `get_stats()` 跟踪命中率
    - 在 Group 和 Group Item 的 CUD 操作中集成缓存失效
    - _Requirements: 9.2, 9.3, 9.4, 9.5, 9.6, 20.2_

  - [x] 4.2 在数据查询中集成缓存
    - 在 `DCF_Group::get_items()` 中实现缓存检查
    - 设置 1 小时缓存过期时间（可配置）
    - 在数据变更时自动失效相关缓存
    - _Requirements: 9.1, 9.2, 9.3, 9.4_

- [x] 5. Checkpoint - 数据层验证
  - 确保所有数据层测试通过
  - 验证数据库表正确创建
  - 验证 Schema 解析和验证功能正常
  - 验证缓存系统工作正常
  - 询问用户是否有问题

- [x] 6. 实现布局引擎核心
  - [x] 6.1 创建布局注册系统
    - 实现 `DCF_Layout_Engine::register_layout()` 方法
    - 实现 `DCF_Layout_Registry` 维护布局注册表
    - 实现 `get_layouts()` 和 `get_layout()` 查询方法
    - 验证布局配置参数（slug, name, render_callback, supports）
    - 处理重复 slug 注册并触发警告
    - _Requirements: 6.1, 6.2, 6.3, 6.4, 6.5, 6.6_

  - [x] 6.2 实现布局渲染引擎
    - 实现 `DCF_Layout_Engine::render()` 方法
    - 实现 `load_template()` 加载布局模板
    - 实现 `locate_template()` 支持主题覆盖模板
    - 应用 `dcf_layout_render_args` 和 `dcf_layout_output` 过滤器钩子
    - 实现异常捕获和用户友好错误消息
    - _Requirements: 6.7, 13.2, 13.3, 13.5, 13.6, 19.2_

- [x] 7. 实现内置布局
  - [x] 7.1 实现 Slider 布局
    - 创建 `DCF_Slider_Layout` 类实现 `DCF_Layout_Interface`
    - 实现 `get_config()` 定义布局设置（autoplay, speed, loop, navigation, pagination）
    - 实现 `render()` 方法生成 Swiper 兼容的 HTML 结构
    - 实现 `get_assets()` 返回 Swiper CSS/JS 依赖
    - 创建 `templates/slider.php` 模板文件
    - _Requirements: 7.1, 7.6_

  - [x] 7.2 实现 Grid 布局
    - 创建 `DCF_Grid_Layout` 类
    - 实现响应式列数设置（desktop, tablet, mobile）
    - 实现 gap 间距设置
    - 创建 `templates/grid.php` 模板文件
    - _Requirements: 7.2, 7.7_

  - [x] 7.3 实现 Masonry 布局
    - 创建 `DCF_Masonry_Layout` 类
    - 实现瀑布流列数和间距设置
    - 创建 `templates/masonry.php` 模板文件
    - _Requirements: 7.3, 7.8_

  - [x] 7.4 实现 List 和 Popup 布局
    - 创建 `DCF_List_Layout` 类实现垂直列表
    - 创建 `DCF_Popup_Layout` 类实现模态弹窗
    - 实现 Popup 的缩略图和动画设置
    - 创建对应的模板文件
    - _Requirements: 7.4, 7.5, 7.9_

  - [x] 7.5 注册所有内置布局
    - 在插件初始化时注册所有 5 个内置布局
    - 触发 `dcf_register_layouts` 钩子允许第三方扩展
    - _Requirements: 13.1_

- [x] 8. 实现 Elementor Widget
  - [x] 8.1 创建动态内容组件基础
    - 创建 `DCF_Elementor_Widget` 类继承 `\Elementor\Widget_Base`
    - 实现 `get_name()`, `get_title()`, `get_icon()`, `get_categories()` 方法
    - 注册 Widget 到 Elementor
    - _Requirements: 8.1_

  - [x] 8.2 实现 Widget 控件
    - 在 `register_controls()` 中添加 Content Group 下拉选择器
    - 添加 Layout 下拉选择器动态加载已注册布局
    - 根据选中的布局动态加载布局特定设置
    - 实现响应式控件支持
    - _Requirements: 8.2, 8.3, 8.6_

  - [x] 8.3 实现 Widget 渲染
    - 实现 `render()` 方法获取选中的 Content Group 数据
    - 调用 Layout Engine 渲染内容
    - 实现编辑器占位符消息
    - 实现 `content_template()` 支持实时预览
    - _Requirements: 8.4, 8.5, 8.7, 8.8_

- [x] 9. 实现前端资源管理
  - [x] 9.1 创建资源加载系统
    - 实现条件加载：仅在页面包含 Widget 时加载资源
    - 注册和排队 CSS 文件（frontend.css, frontend.min.css）
    - 注册和排队 JavaScript 文件（frontend.js, frontend.min.js）
    - 注册 Swiper 库作为依赖
    - _Requirements: 12.1, 12.4, 12.8_

  - [x] 9.2 实现图片优化
    - 为所有 img 标签添加 `loading="lazy"` 属性
    - 生成响应式 srcset 属性
    - 提供设置选项启用/禁用 lazy loading
    - _Requirements: 12.2, 12.3, 12.7_

  - [x] 9.3 实现资源压缩
    - 创建 CSS 和 JS 的 minified 版本
    - 在生产模式下使用 minified 文件
    - _Requirements: 12.5, 12.6_

- [x] 10. Checkpoint - 布局和渲染层验证
  - 确保所有布局正确渲染
  - 验证 Elementor Widget 在编辑器中正常工作
  - 验证前端资源正确加载
  - 测试响应式布局
  - 询问用户是否有问题

- [x] 11. 实现管理界面
  - [x] 11.1 创建管理菜单和导航
    - 实现 `DCF_Admin_Menu` 类注册主菜单和子菜单
    - 添加 "Dynamic Content Framework" 主菜单项
    - 添加子菜单：Group Types, Groups, Settings, Import/Export, System Status
    - 验证 `manage_options` 和 `edit_posts` 权限
    - _Requirements: 2.1, 16.1, 16.2_

  - [x] 11.2 实现内容组类型管理界面
    - 创建 `DCF_Group_Type_List` 类显示类型列表
    - 创建 `DCF_Group_Type_Editor` 类实现类型编辑器
    - 实现可视化 Schema 构建器（schema-builder.js）
    - 支持添加、编辑、删除字段
    - 实现 slug 自动生成和唯一性验证
    - 实现删除前的关联检查警告
    - _Requirements: 2.2, 2.3, 2.4, 2.5, 2.6, 2.7_

  - [x] 11.3 实现内容组管理界面
    - 创建 `DCF_Group_List` 类显示内容组列表
    - 实现按类型和状态筛选
    - 创建 `DCF_Group_Editor` 类实现内容组编辑器
    - 显示内容项数量
    - 实现状态切换功能
    - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5, 4.7_

  - [x] 11.4 实现内容项编辑器
    - 创建 `DCF_Item_Editor` 类动态生成字段表单
    - 根据 Schema 定义渲染不同字段类型的输入控件
    - 实现拖拽排序功能（item-editor.js）
    - 集成 WordPress 媒体库上传
    - 实现 repeater 字段的添加/删除/排序控件
    - 实现批量操作（删除、复制）
    - _Requirements: 5.1, 5.2, 5.3, 5.4, 5.5, 5.7, 5.8, 5.9_

  - [ ]* 11.5 编写管理界面的集成测试
    - 测试菜单注册和权限检查
    - 测试 Schema 构建器功能
    - 测试内容项编辑和排序
    - _Requirements: 16.1, 16.2_

- [x] 12. 实现默认内容组类型
  - [x] 12.1 创建默认类型安装器
    - 在 `DCF_Activator` 中实现默认类型创建逻辑
    - 检查设置选项是否启用默认类型创建
    - 创建 9 个默认内容组类型（banner-slider, logo-showcase, image-gallery, video-module, feature-list, testimonials, faq-module, team-members, timeline）
    - 为每个类型定义完整的 Schema
    - _Requirements: 10.1, 10.2, 10.3, 10.4, 10.5, 10.6, 10.7, 10.8, 10.9, 10.10_

- [x] 13. 实现 REST API
  - [x] 13.1 创建 REST API 控制器
    - 创建 `DCF_REST_API` 类注册命名空间 `dcf/v1`
    - 实现 `permissions_check()` 验证权限和认证
    - _Requirements: 11.1, 11.7, 16.8_

  - [x] 13.2 实现 REST API 端点
    - 创建 `DCF_REST_Group_Types` 实现 `GET /dcf/v1/group-types`
    - 创建 `DCF_REST_Groups` 实现 `GET /dcf/v1/groups` 支持筛选和分页
    - 实现 `GET /dcf/v1/groups/{id}` 返回内容组和所有内容项
    - 实现 `GET /dcf/v1/groups/{id}/items` 返回内容项列表
    - 创建 `DCF_REST_Layouts` 实现 `GET /dcf/v1/layouts`
    - 返回 JSON 格式数据和正确的 HTTP 状态码
    - 支持 CORS 头配置
    - _Requirements: 11.2, 11.3, 11.4, 11.5, 11.6, 11.8, 11.9_

- [x] 14. 实现设置页面
  - [x] 14.1 创建插件设置界面
    - 创建 `DCF_Settings` 类实现设置页面
    - 添加 lazy loading 启用/禁用设置
    - 添加缓存过期时间设置（小时）
    - 添加 REST API 启用/禁用设置
    - 添加默认类型创建启用/禁用设置
    - 添加前端资源压缩启用/禁用设置
    - 添加清空缓存按钮
    - 实现设置验证和保存
    - _Requirements: 18.1, 18.2, 18.3, 18.4, 18.5, 18.6, 18.7, 18.8_

- [x] 15. 实现导入导出功能
  - [x] 15.1 实现导出功能
    - 创建 `DCF_Import_Export` 类
    - 实现内容组导出为 JSON 文件
    - 包含 Group Type schema, Group metadata, 所有 Group Items
    - _Requirements: 17.1, 17.2_

  - [x] 15.2 实现导入功能
    - 实现 JSON 文件上传和解析
    - 验证文件结构和数据完整性
    - 处理 Group Type slug 冲突（合并或跳过）
    - 提供媒体文件导入选项
    - 记录导入操作日志
    - _Requirements: 17.3, 17.4, 17.5, 17.6, 17.7_

- [x] 16. 实现安全和数据清理
  - [x] 16.1 实现数据清理器
    - 创建 `DCF_Sanitizer` 类
    - 实现所有用户输入的清理函数
    - 实现所有输出的转义函数
    - 验证文件上传类型和大小
    - _Requirements: 16.3, 16.4, 16.5, 16.6, 16.7_

  - [x] 16.2 实现 CSRF 保护
    - 在所有表单中添加 nonce 验证
    - 在表单处理中验证 nonce
    - _Requirements: 16.3_

- [x] 17. 实现日志和错误处理
  - [x] 17.1 创建日志系统
    - 创建 `DCF_Logger` 类
    - 实现 `info()`, `error()`, `warning()`, `debug()` 方法
    - 使用 WordPress `error_log()` 函数
    - 实现调试模式设置
    - _Requirements: 19.1, 19.2, 19.3, 19.4, 19.5_

  - [x] 17.2 实现系统状态页面
    - 创建 `DCF_System_Status` 类
    - 显示 PHP, WordPress, Elementor 版本
    - 显示数据库表状态
    - 显示已注册布局数量
    - 显示活跃内容组数量
    - 集成性能监控数据
    - _Requirements: 19.6, 19.7, 20.4_

- [x] 18. 实现性能监控
  - [x] 18.1 创建性能监控器
    - 创建 `DCF_Performance` 类
    - 实现 `start()` 和 `end()` 计时方法
    - 实现 `log_query()` 跟踪数据库查询
    - 实现 `get_report()` 生成性能报告
    - 跟踪布局渲染时间
    - 实现性能阈值警告
    - _Requirements: 20.1, 20.2, 20.3, 20.5, 20.6, 20.7_

- [-] 19. 实现多语言支持
  - [ ] 19.1 完成国际化
    - 确保所有用户可见字符串使用翻译函数
    - 生成 `.pot` 模板文件
    - 测试 WPML 和 Polylang 兼容性
    - _Requirements: 15.1, 15.2, 15.3, 15.4, 15.5, 15.6_

- [ ] 20. Final Checkpoint - 完整系统测试
  - 测试完整的内容创建到渲染流程
  - 验证所有布局在不同设备上的响应式表现
  - 测试导入导出功能
  - 验证 REST API 端点
  - 测试缓存系统和性能
  - 检查安全性和权限控制
  - 验证多语言支持
  - 确保所有测试通过，询问用户是否有问题

## Notes

- 任务标记 `*` 为可选任务，可跳过以加快 MVP 开发
- 每个任务都引用了具体的需求条款以确保可追溯性
- Checkpoint 任务确保增量验证
- 实现顺序遵循依赖关系：数据层 → 布局层 → 渲染层 → 管理界面
- 所有数据库操作必须使用 prepared statements 防止 SQL 注入
- 所有用户输入必须经过清理和验证
- 所有输出必须经过转义
- 使用 WordPress 编码标准和最佳实践
