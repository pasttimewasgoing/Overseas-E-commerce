# Database Layer

This directory contains database-related classes for the Elementor Dynamic Content Framework plugin.

## Implemented Classes

### DCF_Database

Database management class that handles table creation, version management, and table existence checks.

**Methods:**
- `create_tables()`: Creates all three core tables (group_types, groups, group_items) with proper indexes and foreign key constraints
- `get_table_name($table)`: Returns the full table name with WordPress prefix
- `table_exists($table)`: Checks if a specific table exists in the database
- `get_db_version()`: Returns the current database version from WordPress options
- `needs_upgrade()`: Checks if the database needs to be upgraded
- `verify_tables()`: Verifies that all required tables exist

**Database Tables:**

1. **wp_dcf_group_types**: Stores content group type definitions and field structures
   - `id`: Primary key (BIGINT UNSIGNED AUTO_INCREMENT)
   - `name`: Type name (VARCHAR 255)
   - `slug`: Unique identifier (VARCHAR 100, UNIQUE)
   - `schema_json`: Field structure JSON (LONGTEXT)
   - `created_at`: Creation timestamp (DATETIME)
   - `updated_at`: Last update timestamp (DATETIME)
   - Indexes: PRIMARY KEY (id), UNIQUE KEY (slug), KEY (created_at)

2. **wp_dcf_groups**: Stores content group instances
   - `id`: Primary key (BIGINT UNSIGNED AUTO_INCREMENT)
   - `type_id`: Foreign key to group_types (BIGINT UNSIGNED)
   - `title`: Content group title (VARCHAR 255)
   - `status`: Status enum ('active', 'inactive', 'draft')
   - `created_at`: Creation timestamp (DATETIME)
   - `updated_at`: Last update timestamp (DATETIME)
   - Indexes: PRIMARY KEY (id), KEY (type_id), KEY (status), KEY (created_at)
   - Foreign Key: CONSTRAINT fk_group_type FOREIGN KEY (type_id) REFERENCES wp_dcf_group_types(id) ON DELETE RESTRICT

3. **wp_dcf_group_items**: Stores content item data
   - `id`: Primary key (BIGINT UNSIGNED AUTO_INCREMENT)
   - `group_id`: Foreign key to groups (BIGINT UNSIGNED)
   - `data_json`: Field data JSON (LONGTEXT)
   - `sort_order`: Sort order value (INT, DEFAULT 0)
   - `created_at`: Creation timestamp (DATETIME)
   - `updated_at`: Last update timestamp (DATETIME)
   - Indexes: PRIMARY KEY (id), KEY (group_id), KEY (sort_order), KEY (created_at)
   - Foreign Key: CONSTRAINT fk_group_item FOREIGN KEY (group_id) REFERENCES wp_dcf_groups(id) ON DELETE CASCADE

**Requirements Satisfied:**
- Requirement 1.1: Creates wp_dcf_group_types table with all required fields
- Requirement 1.2: Creates wp_dcf_groups table with all required fields
- Requirement 1.3: Creates wp_dcf_group_items table with all required fields
- Requirement 1.4: Creates indexes on type_id, group_id, status, and sort_order columns
- Requirement 1.5: Preserves tables and data on plugin deactivation

## Usage

The DCF_Database class is automatically used during plugin activation:

```php
// In DCF_Activator::activate()
require_once DCF_PLUGIN_DIR . 'includes/database/class-dcf-database.php';
DCF_Database::create_tables();
```

To check if tables exist:

```php
if ( DCF_Database::verify_tables() ) {
    // All tables exist
}
```

To get a table name:

```php
$table_name = DCF_Database::get_table_name( 'group_types' );
// Returns: wp_dcf_group_types (with your WordPress prefix)
```

## Future Classes

The following classes will be implemented in future tasks:
- Schema parser and validator
- Data serializer
- Query builder
- Model classes
