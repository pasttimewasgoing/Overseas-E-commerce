# Requirements Document

## Introduction

本文档定义了一个企业级 WordPress 动态内容框架插件的需求，该插件深度集成 Elementor，通过三层架构（数据层/布局层/渲染层）实现完全解耦的内容管理系统。系统支持无限扩展的内容组类型和布局方式，通过单一通用 Elementor 组件实现所有内容的渲染，适用于企业内部系统和商业化扩展场景。

## Glossary

- **Plugin**: elementor-dynamic-content-framework WordPress 插件系统
- **Content_Group_Type**: 内容组类型，定义字段结构的模板（如轮播组、Logo组）
- **Content_Group**: 内容组实例，基于某个类型创建的具体内容集合
- **Content_Item**: 内容项，属于某个内容组的单条数据记录
- **Schema**: 字段结构定义，以 JSON 格式存储的字段配置
- **Layout_Engine**: 布局引擎，负责将数据渲染为特定布局（如 Slider、Grid、Masonry）
- **Dynamic_Widget**: Elementor 通用动态组件，用于选择和渲染内容组
- **Field_Type**: 字段类型，支持的数据类型（text/textarea/image/video/url/icon/gallery/repeater）
- **Admin_Interface**: WordPress 后台管理界面
- **Database_Layer**: 数据层，负责内容结构和数据存储
- **Layout_Layer**: 布局层，可插拔的布局渲染系统
- **Render_Layer**: 渲染层，Elementor 组件桥接层
- **Cache_System**: 缓存系统，使用 wp_cache 优化性能
- **REST_API**: WordPress REST API 接口

## Requirements

### Requirement 1: 数据库架构

**User Story:** 作为系统架构师，我需要一个三表结构的数据库设计，以便存储内容组类型、内容组实例和内容项数据。

#### Acceptance Criteria

1. WHEN THE Plugin is activated, THE Database_Layer SHALL create a table named `wp_dcf_group_types` with columns: id (primary key), name (varchar 255), slug (varchar 100 unique), schema_json (longtext), created_at (datetime), updated_at (datetime)

2. WHEN THE Plugin is activated, THE Database_Layer SHALL create a table named `wp_dcf_groups` with columns: id (primary key), type_id (foreign key to group_types), title (varchar 255), status (enum: active/inactive/draft), created_at (datetime), updated_at (datetime)

3. WHEN THE Plugin is activated, THE Database_Layer SHALL create a table named `wp_dcf_group_items` with columns: id (primary key), group_id (foreign key to groups), data_json (longtext), sort_order (integer default 0), created_at (datetime), updated_at (datetime)

4. THE Database_Layer SHALL create indexes on type_id, group_id, status, and sort_order columns for query optimization

5. WHEN THE Plugin is deactivated, THE Database_Layer SHALL preserve all tables and data

### Requirement 2: 内容组类型管理

**User Story:** 作为管理员，我需要创建和管理内容组类型，以便定义不同类型内容的字段结构。

#### Acceptance Criteria

1. THE Admin_Interface SHALL provide a menu item "Dynamic Content Framework" in WordPress admin sidebar

2. WHEN an administrator accesses the content group types page, THE Admin_Interface SHALL display a list of all Content_Group_Type records with columns: name, slug, item count, created date

3. WHEN an administrator clicks "Add New Type", THE Admin_Interface SHALL display a form with fields: name (required), slug (auto-generated from name, editable), schema builder

4. THE Admin_Interface SHALL provide a visual schema builder interface for defining Field_Type configurations

5. WHEN an administrator saves a Content_Group_Type, THE Database_Layer SHALL validate that the slug is unique and the schema_json is valid JSON

6. WHEN an administrator deletes a Content_Group_Type, THE Admin_Interface SHALL display a warning if associated Content_Group instances exist

7. THE Admin_Interface SHALL support editing existing Content_Group_Type records while preserving data integrity

### Requirement 3: 字段系统

**User Story:** 作为内容管理员，我需要灵活的字段类型系统，以便为不同内容组定义合适的数据结构。

#### Acceptance Criteria

1. THE Schema SHALL support Field_Type: text with properties: label, default_value, placeholder, max_length

2. THE Schema SHALL support Field_Type: textarea with properties: label, default_value, placeholder, rows

3. THE Schema SHALL support Field_Type: image with properties: label, allowed_formats, max_size_mb

4. THE Schema SHALL support Field_Type: video with properties: label, allowed_formats, max_size_mb, allow_url

5. THE Schema SHALL support Field_Type: url with properties: label, placeholder, validation_pattern

6. THE Schema SHALL support Field_Type: icon with properties: label, icon_library (fontawesome/custom)

7. THE Schema SHALL support Field_Type: gallery with properties: label, max_images, allowed_formats

8. THE Schema SHALL support Field_Type: repeater with properties: label, sub_fields (nested field definitions), min_items, max_items

9. WHEN a Schema is saved, THE Database_Layer SHALL validate that all field definitions contain required properties

10. THE Schema SHALL store field definitions in schema_json column as a valid JSON array

### Requirement 4: 内容组实例管理

**User Story:** 作为内容编辑，我需要基于内容组类型创建具体的内容组实例，以便组织和管理实际内容数据。

#### Acceptance Criteria

1. WHEN an administrator selects a Content_Group_Type, THE Admin_Interface SHALL provide an "Add New Group" button

2. WHEN creating a new Content_Group, THE Admin_Interface SHALL display a form with fields: title (required), type (pre-selected), status (active/inactive/draft)

3. THE Admin_Interface SHALL display a list of all Content_Group instances with filters by type and status

4. WHEN an administrator clicks on a Content_Group, THE Admin_Interface SHALL navigate to the content items management page

5. WHEN an administrator changes a Content_Group status, THE Database_Layer SHALL update the status field and updated_at timestamp

6. WHEN an administrator deletes a Content_Group, THE Database_Layer SHALL also delete all associated Content_Item records

7. THE Admin_Interface SHALL display the count of Content_Item records for each Content_Group

### Requirement 5: 内容项数据管理

**User Story:** 作为内容编辑，我需要在内容组中添加、编辑和排序内容项，以便管理实际展示的数据。

#### Acceptance Criteria

1. WHEN an administrator accesses a Content_Group, THE Admin_Interface SHALL display all Content_Item records sorted by sort_order ascending

2. THE Admin_Interface SHALL dynamically generate input fields based on the Content_Group_Type Schema definition

3. WHEN an administrator adds a new Content_Item, THE Admin_Interface SHALL display a form with all fields defined in the Schema

4. WHEN an administrator saves a Content_Item, THE Database_Layer SHALL store all field values in data_json column as valid JSON

5. THE Admin_Interface SHALL provide drag-and-drop functionality to reorder Content_Item records

6. WHEN an administrator reorders Content_Item records, THE Database_Layer SHALL update the sort_order values accordingly

7. WHEN an administrator uploads media files, THE Plugin SHALL use WordPress media library integration

8. THE Admin_Interface SHALL provide bulk actions: delete, duplicate for Content_Item records

9. WHEN a Content_Item contains a repeater field, THE Admin_Interface SHALL provide add/remove/reorder controls for sub-items

### Requirement 6: 布局注册系统

**User Story:** 作为开发者，我需要一个可插拔的布局注册机制，以便扩展新的内容展示方式。

#### Acceptance Criteria

1. THE Layout_Engine SHALL provide a function `dcf_register_layout($slug, $args)` for registering new layouts

2. WHEN registering a layout, THE Layout_Engine SHALL require parameters: slug (unique identifier), name (display name), render_callback (callable function), supports (array of supported field types)

3. THE Layout_Engine SHALL maintain a registry of all registered layouts in memory

4. WHEN a layout with duplicate slug is registered, THE Layout_Engine SHALL trigger a warning and ignore the duplicate

5. THE Layout_Engine SHALL provide a function `dcf_get_layouts()` that returns all registered layouts

6. THE Layout_Engine SHALL provide a function `dcf_get_layout($slug)` that returns a specific layout configuration

7. WHEN a render_callback is invoked, THE Layout_Engine SHALL pass parameters: items (array of Content_Item data), settings (array of layout-specific settings)

### Requirement 7: 内置布局实现

**User Story:** 作为用户，我需要常用的内置布局方式，以便快速构建常见的内容展示效果。

#### Acceptance Criteria

1. THE Plugin SHALL register a layout with slug "slider" that renders Content_Item records as a carousel with navigation arrows and pagination dots

2. THE Plugin SHALL register a layout with slug "grid" that renders Content_Item records in a responsive grid with configurable columns (1-6)

3. THE Plugin SHALL register a layout with slug "masonry" that renders Content_Item records in a masonry waterfall layout

4. THE Plugin SHALL register a layout with slug "list" that renders Content_Item records as a vertical list

5. THE Plugin SHALL register a layout with slug "popup" that renders Content_Item records as clickable thumbnails opening in modal popups

6. WHEN rendering the slider layout, THE Layout_Engine SHALL support settings: autoplay (boolean), speed (milliseconds), loop (boolean), navigation (boolean), pagination (boolean)

7. WHEN rendering the grid layout, THE Layout_Engine SHALL support settings: columns_desktop (1-6), columns_tablet (1-4), columns_mobile (1-2), gap (pixels)

8. WHEN rendering the masonry layout, THE Layout_Engine SHALL support settings: columns (2-6), gap (pixels)

9. WHEN rendering the popup layout, THE Layout_Engine SHALL support settings: thumbnail_size (small/medium/large), animation (fade/slide/zoom)

### Requirement 8: Elementor 通用动态组件

**User Story:** 作为网站设计师，我需要一个 Elementor 组件来选择和展示内容组，以便在页面构建器中使用动态内容。

#### Acceptance Criteria

1. THE Plugin SHALL register an Elementor widget named "Dynamic Content" in the "General" category

2. WHEN the Dynamic_Widget is added to a page, THE Render_Layer SHALL display controls: content_group (dropdown of all active Content_Group instances), layout (dropdown of all registered layouts)

3. THE Dynamic_Widget SHALL dynamically load layout-specific settings based on the selected layout

4. WHEN a Content_Group is selected, THE Dynamic_Widget SHALL fetch all associated Content_Item records ordered by sort_order

5. WHEN rendering on the frontend, THE Dynamic_Widget SHALL invoke the selected layout's render_callback with the fetched Content_Item data

6. THE Dynamic_Widget SHALL support Elementor's responsive controls for layout settings

7. WHEN no Content_Group is selected, THE Dynamic_Widget SHALL display a placeholder message in the editor

8. THE Dynamic_Widget SHALL support Elementor's live preview functionality

### Requirement 9: 数据查询和缓存

**User Story:** 作为系统管理员，我需要高效的数据查询和缓存机制，以便优化插件性能。

#### Acceptance Criteria

1. THE Database_Layer SHALL provide a function `dcf_get_group_items($group_id)` that returns all Content_Item records for a given Content_Group

2. WHEN `dcf_get_group_items()` is called, THE Cache_System SHALL check for cached data with key `dcf_group_items_{$group_id}`

3. IF cached data exists and is not expired, THE Cache_System SHALL return the cached data without database query

4. IF cached data does not exist, THE Database_Layer SHALL query the database and THE Cache_System SHALL store the result with a 1-hour expiration

5. WHEN a Content_Item is created, updated, or deleted, THE Cache_System SHALL invalidate the cache for the associated Content_Group

6. WHEN a Content_Group status changes to inactive or draft, THE Cache_System SHALL invalidate the cache for that group

7. THE Plugin SHALL provide an admin option to clear all Dynamic Content Framework caches

8. THE Database_Layer SHALL use prepared statements for all SQL queries to prevent SQL injection

### Requirement 10: 默认内容组类型

**User Story:** 作为新用户，我需要预定义的常用内容组类型，以便快速开始使用插件。

#### Acceptance Criteria

1. WHEN THE Plugin is activated for the first time, THE Database_Layer SHALL create a Content_Group_Type with slug "banner-slider" and Schema containing fields: image (image), title (text), subtitle (textarea), button_text (text), button_url (url)

2. WHEN THE Plugin is activated for the first time, THE Database_Layer SHALL create a Content_Group_Type with slug "logo-showcase" and Schema containing fields: logo (image), company_name (text), website_url (url)

3. WHEN THE Plugin is activated for the first time, THE Database_Layer SHALL create a Content_Group_Type with slug "image-gallery" and Schema containing fields: images (gallery), caption (text)

4. WHEN THE Plugin is activated for the first time, THE Database_Layer SHALL create a Content_Group_Type with slug "video-module" and Schema containing fields: video (video), thumbnail (image), title (text), description (textarea)

5. WHEN THE Plugin is activated for the first time, THE Database_Layer SHALL create a Content_Group_Type with slug "feature-list" and Schema containing fields: icon (icon), title (text), description (textarea)

6. WHEN THE Plugin is activated for the first time, THE Database_Layer SHALL create a Content_Group_Type with slug "testimonials" and Schema containing fields: avatar (image), name (text), position (text), company (text), rating (text), testimonial (textarea)

7. WHEN THE Plugin is activated for the first time, THE Database_Layer SHALL create a Content_Group_Type with slug "faq-module" and Schema containing fields: question (text), answer (textarea), category (text)

8. WHEN THE Plugin is activated for the first time, THE Database_Layer SHALL create a Content_Group_Type with slug "team-members" and Schema containing fields: photo (image), name (text), position (text), bio (textarea), social_links (repeater with sub-fields: platform (text), url (url))

9. WHEN THE Plugin is activated for the first time, THE Database_Layer SHALL create a Content_Group_Type with slug "timeline" and Schema containing fields: date (text), title (text), description (textarea), image (image)

10. THE Plugin SHALL provide an option to skip default Content_Group_Type creation during activation

### Requirement 11: REST API 支持

**User Story:** 作为前端开发者，我需要 REST API 接口访问内容数据，以便支持 Headless WordPress 架构。

#### Acceptance Criteria

1. THE Plugin SHALL register a REST API namespace `dcf/v1`

2. THE REST_API SHALL provide an endpoint `GET /dcf/v1/group-types` that returns all Content_Group_Type records

3. THE REST_API SHALL provide an endpoint `GET /dcf/v1/groups` that returns all active Content_Group instances with query parameters: type (filter by type_id), per_page (pagination), page (pagination)

4. THE REST_API SHALL provide an endpoint `GET /dcf/v1/groups/{id}` that returns a specific Content_Group with all associated Content_Item records

5. THE REST_API SHALL provide an endpoint `GET /dcf/v1/groups/{id}/items` that returns all Content_Item records for a specific Content_Group

6. THE REST_API SHALL provide an endpoint `GET /dcf/v1/layouts` that returns all registered layouts with their configuration

7. WHEN a REST API request is made, THE REST_API SHALL respect WordPress authentication and capability checks

8. THE REST_API SHALL return data in JSON format with proper HTTP status codes

9. THE REST_API SHALL support CORS headers for cross-origin requests when configured

### Requirement 12: 前端资源优化

**User Story:** 作为网站访客，我需要优化的前端加载性能，以便获得流畅的浏览体验。

#### Acceptance Criteria

1. THE Render_Layer SHALL enqueue CSS and JavaScript files only when the Dynamic_Widget is present on the page

2. WHEN rendering images, THE Render_Layer SHALL add `loading="lazy"` attribute to all img tags

3. THE Render_Layer SHALL generate responsive image srcset attributes for image fields

4. WHEN rendering the slider layout, THE Render_Layer SHALL load the slider library (e.g., Swiper) only when needed

5. THE Render_Layer SHALL minify and concatenate CSS files in production mode

6. THE Render_Layer SHALL minify and concatenate JavaScript files in production mode

7. THE Plugin SHALL provide a setting to enable/disable lazy loading for images

8. WHEN multiple Dynamic_Widget instances use the same layout, THE Render_Layer SHALL load layout assets only once per page

### Requirement 13: 布局扩展机制

**User Story:** 作为插件开发者，我需要清晰的扩展机制，以便创建商业化布局扩展包。

#### Acceptance Criteria

1. THE Plugin SHALL provide action hook `dcf_register_layouts` that fires after core layouts are registered

2. THE Plugin SHALL provide filter hook `dcf_layout_render_args` that allows modification of render arguments before rendering

3. THE Plugin SHALL provide filter hook `dcf_layout_output` that allows modification of rendered HTML output

4. THE Plugin SHALL document the layout registration API in a developer guide

5. THE Layout_Engine SHALL support layout templates located in theme directory `dcf-layouts/{slug}.php`

6. WHEN a layout template exists in the theme directory, THE Layout_Engine SHALL use it instead of the plugin's default template

7. THE Plugin SHALL provide example code for creating custom layouts in documentation

### Requirement 14: 字段类型扩展

**User Story:** 作为插件开发者，我需要扩展新的字段类型，以便支持特殊的数据需求。

#### Acceptance Criteria

1. THE Plugin SHALL provide filter hook `dcf_field_types` that allows registration of custom Field_Type definitions

2. WHEN a custom Field_Type is registered, THE Admin_Interface SHALL render the appropriate input control in the content item form

3. THE Plugin SHALL provide action hook `dcf_render_field_{$field_type}` for custom field rendering logic

4. THE Plugin SHALL provide filter hook `dcf_sanitize_field_{$field_type}` for custom field data sanitization

5. THE Plugin SHALL provide filter hook `dcf_validate_field_{$field_type}` for custom field validation logic

6. THE Plugin SHALL document the field type extension API in a developer guide

### Requirement 15: 多语言支持准备

**User Story:** 作为国际化网站管理员，我需要插件支持多语言，以便在不同语言环境中使用。

#### Acceptance Criteria

1. THE Plugin SHALL load text domain `elementor-dynamic-content-framework` for translation

2. THE Plugin SHALL wrap all user-facing strings with translation functions (`__()`, `_e()`, `esc_html__()`)

3. THE Plugin SHALL provide a `.pot` template file in the `languages` directory

4. THE Plugin SHALL be compatible with WPML plugin for Content_Group and Content_Item translation

5. THE Plugin SHALL be compatible with Polylang plugin for Content_Group and Content_Item translation

6. THE Admin_Interface SHALL display translated strings based on WordPress locale setting

### Requirement 16: 安全和权限

**User Story:** 作为系统管理员，我需要适当的权限控制，以便保护内容管理功能的安全性。

#### Acceptance Criteria

1. THE Admin_Interface SHALL require `manage_options` capability to access content group type management

2. THE Admin_Interface SHALL require `edit_posts` capability to access content group and content item management

3. WHEN processing form submissions, THE Plugin SHALL verify WordPress nonces to prevent CSRF attacks

4. WHEN saving data, THE Database_Layer SHALL sanitize all user input using WordPress sanitization functions

5. WHEN outputting data, THE Render_Layer SHALL escape all output using WordPress escaping functions

6. THE Plugin SHALL validate file uploads to ensure only allowed file types are accepted

7. THE Plugin SHALL check file upload size limits against WordPress and PHP configuration

8. THE REST_API SHALL require authentication for all write operations

### Requirement 17: 导入导出功能

**User Story:** 作为内容管理员，我需要导入导出内容组数据，以便在不同网站间迁移内容或备份数据。

#### Acceptance Criteria

1. THE Admin_Interface SHALL provide an "Export" button for each Content_Group that generates a JSON file

2. WHEN exporting a Content_Group, THE Plugin SHALL include the Content_Group_Type schema, Content_Group metadata, and all Content_Item records in the JSON file

3. THE Admin_Interface SHALL provide an "Import" page that accepts JSON files

4. WHEN importing a JSON file, THE Plugin SHALL validate the file structure and data integrity

5. IF a Content_Group_Type with the same slug exists during import, THE Plugin SHALL prompt the user to merge or skip

6. WHEN importing Content_Item records with media files, THE Plugin SHALL provide an option to download and import media or reference existing media library items

7. THE Plugin SHALL log all import operations with success and error messages

### Requirement 18: 插件设置页面

**User Story:** 作为管理员，我需要一个设置页面来配置插件的全局选项，以便自定义插件行为。

#### Acceptance Criteria

1. THE Admin_Interface SHALL provide a "Settings" submenu under "Dynamic Content Framework"

2. THE Admin_Interface SHALL provide a setting to enable/disable image lazy loading (default: enabled)

3. THE Admin_Interface SHALL provide a setting to set cache expiration time in hours (default: 1 hour)

4. THE Admin_Interface SHALL provide a setting to enable/disable REST API endpoints (default: enabled)

5. THE Admin_Interface SHALL provide a setting to enable/disable default Content_Group_Type creation on activation (default: enabled)

6. THE Admin_Interface SHALL provide a button to clear all plugin caches

7. THE Admin_Interface SHALL provide a setting to enable/disable frontend asset minification (default: enabled)

8. WHEN settings are saved, THE Plugin SHALL validate all input values and display success or error messages

### Requirement 19: 错误处理和日志

**User Story:** 作为开发者，我需要完善的错误处理和日志记录，以便调试和监控插件运行状态。

#### Acceptance Criteria

1. WHEN a database operation fails, THE Database_Layer SHALL log the error using WordPress `error_log()` function

2. WHEN a layout render_callback throws an exception, THE Layout_Engine SHALL catch the exception and display a user-friendly error message

3. WHEN a Content_Item data_json contains invalid JSON, THE Database_Layer SHALL log the error and return an empty array

4. THE Plugin SHALL provide a debug mode setting that enables verbose logging

5. WHEN debug mode is enabled, THE Plugin SHALL log all database queries, cache operations, and layout rendering events

6. THE Admin_Interface SHALL display error messages using WordPress admin notices

7. THE Plugin SHALL provide a system status page showing: PHP version, WordPress version, Elementor version, database table status, registered layouts count, active content groups count

### Requirement 20: 性能监控

**User Story:** 作为系统管理员，我需要监控插件性能指标，以便识别和优化性能瓶颈。

#### Acceptance Criteria

1. THE Plugin SHALL track the number of database queries executed per page load

2. THE Plugin SHALL track cache hit/miss ratios for Content_Group queries

3. THE Plugin SHALL track average render time for each layout type

4. THE Admin_Interface SHALL display performance metrics on the system status page

5. WHEN debug mode is enabled, THE Plugin SHALL display query execution times in the debug log

6. THE Plugin SHALL provide a filter hook `dcf_performance_threshold` to set custom performance warning thresholds

7. WHEN a layout render time exceeds the threshold, THE Plugin SHALL log a performance warning

## 解析器和序列化器需求

### Requirement 21: Schema 解析和验证

**User Story:** 作为系统，我需要解析和验证 Schema JSON 数据，以便确保字段定义的正确性。

#### Acceptance Criteria

1. WHEN a Content_Group_Type is saved, THE Database_Layer SHALL parse the schema_json string into a PHP array

2. WHEN parsing schema_json fails, THE Database_Layer SHALL return a descriptive error message indicating the JSON syntax error location

3. THE Database_Layer SHALL provide a Schema_Validator that validates field definitions against allowed Field_Type specifications

4. THE Database_Layer SHALL provide a Schema_Printer that formats Schema arrays back into valid JSON strings with proper indentation

5. FOR ALL valid Schema arrays, parsing the JSON then printing then parsing SHALL produce an equivalent Schema structure (round-trip property)

6. WHEN a Schema contains invalid field types, THE Schema_Validator SHALL return an array of validation errors with field paths

7. THE Schema_Validator SHALL verify that repeater fields do not nest more than 3 levels deep

### Requirement 22: Content Item 数据序列化

**User Story:** 作为系统，我需要序列化和反序列化 Content_Item 数据，以便在数据库和应用层之间转换数据格式。

#### Acceptance Criteria

1. WHEN a Content_Item is saved, THE Database_Layer SHALL serialize the data array into a JSON string for storage in data_json column

2. WHEN serialization fails, THE Database_Layer SHALL log the error and return false

3. WHEN a Content_Item is retrieved, THE Database_Layer SHALL deserialize the data_json string into a PHP array

4. WHEN deserialization fails, THE Database_Layer SHALL log the error and return an empty array

5. THE Database_Layer SHALL provide a Data_Printer that formats Content_Item data arrays back into valid JSON strings

6. FOR ALL valid Content_Item data arrays, deserializing the JSON then serializing then deserializing SHALL produce equivalent data (round-trip property)

7. THE Database_Layer SHALL handle special characters and Unicode content correctly during serialization

8. WHEN data contains WordPress shortcodes, THE Database_Layer SHALL preserve shortcode syntax during serialization and deserialization

---

## 总结

本需求文档定义了 22 个核心需求，涵盖了数据库架构、内容管理、布局系统、Elementor 集成、性能优化、安全性、扩展性和解析器/序列化器等方面。所有需求均遵循 EARS 模式和 INCOSE 质量规则，确保可测试性和完整性。

系统采用三层架构设计，通过数据层、布局层和渲染层的分离，实现了高度的可扩展性和灵活性。插件支持无限内容组类型和布局方式的扩展，为企业级应用和商业化扩展提供了坚实的基础。
 