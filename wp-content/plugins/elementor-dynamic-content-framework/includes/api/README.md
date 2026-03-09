# REST API

This directory contains REST API implementation for the Dynamic Content Framework.

## Classes

### DCF_REST_API

Main REST API controller that registers all endpoints under the `dcf/v1` namespace.

**Namespace:** `dcf/v1`

**Endpoints:**

- `GET /dcf/v1/group-types` - Get all content group types
- `GET /dcf/v1/groups` - Get all active content groups (supports filtering and pagination)
- `GET /dcf/v1/groups/{id}` - Get a specific group with all its items
- `GET /dcf/v1/groups/{id}/items` - Get all items for a specific group
- `GET /dcf/v1/layouts` - Get all registered layouts

**Authentication:**

All endpoints require WordPress authentication. The `permissions_check()` method verifies:
- REST API is enabled in settings
- For GET requests: User is authenticated or public access is allowed
- For write operations: User has `edit_posts` capability

**Features:**

- Namespace registration under `dcf/v1`
- Permission checking for all endpoints
- Support for query parameters (filtering, pagination)
- Proper HTTP status codes and error handling
- JSON response format
- CORS support ready

## Usage

The REST API is automatically initialized when the plugin loads. All endpoints are registered on the `rest_api_init` hook.

### Example Requests

```bash
# Get all group types
curl https://example.com/wp-json/dcf/v1/group-types

# Get active groups with pagination
curl https://example.com/wp-json/dcf/v1/groups?per_page=20&page=1

# Get a specific group with items
curl https://example.com/wp-json/dcf/v1/groups/1

# Get items for a group
curl https://example.com/wp-json/dcf/v1/groups/1/items

# Get all layouts
curl https://example.com/wp-json/dcf/v1/layouts
```

## Future Endpoints

Additional endpoints will be implemented in task 13.2:
- `DCF_REST_Group_Types` - Detailed group type endpoints
- `DCF_REST_Groups` - Detailed group endpoints
- `DCF_REST_Layouts` - Detailed layout endpoints
