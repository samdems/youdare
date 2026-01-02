# Tag Groups Feature

## Overview

Tag groups allow you to organize tags into logical categories, making it easier for users to find and select relevant tags during game setup. Each tag group has a name, description, and can contain multiple tags.

## Database Schema

### Tag Groups Table

```sql
CREATE TABLE tag_groups (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    description TEXT NULL,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

### Tags Table (Updated)

The `tags` table now includes a `tag_group_id` foreign key:

```sql
ALTER TABLE tags ADD COLUMN tag_group_id BIGINT UNSIGNED NULL;
ALTER TABLE tags ADD FOREIGN KEY (tag_group_id) REFERENCES tag_groups(id) ON DELETE SET NULL;
```

## Predefined Tag Groups

The system comes with 4 predefined tag groups:

1. **Content Type** (sort_order: 1)
   - Description: "Categories that define the overall nature and audience of the content"
   - Tags: Adults Only, Family Friendly, Party Mode, Romantic, Extreme

2. **Activity Style** (sort_order: 2)
   - Description: "The type of challenge or activity involved"
   - Tags: Funny, Physical, Mental, Creative, Social

3. **Gender** (sort_order: 3)
   - Description: "Gender-specific tags for participants"
   - Tags: Male, Female, Any Gender

4. **Clothing & Accessories** (sort_order: 4)
   - Description: "Tags related to specific clothing items or accessories"
   - Tags: Boxers, Bra, Skirt, Dress, Panties

## Models

### TagGroup Model

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TagGroup extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'sort_order'];
    
    // Relationship: A tag group has many tags
    public function tags()
    {
        return $this->hasMany(Tag::class)->orderBy('name');
    }
}
```

### Tag Model (Updated)

```php
// Added to Tag model
public function tagGroup()
{
    return $this->belongsTo(TagGroup::class);
}
```

## API Endpoints

### Get Grouped Tags

**Endpoint:** `GET /api/tags/grouped`

**Query Parameters:**
- `min_spice_level` (optional): Filter tags by minimum spice level

**Response:**

```json
{
    "status": "success",
    "data": [
        {
            "id": 1,
            "name": "Content Type",
            "slug": "content-type",
            "description": "Categories that define the overall nature and audience of the content",
            "sort_order": 1,
            "tags": [
                {
                    "id": 1,
                    "name": "Adults Only",
                    "slug": "adults-only",
                    "description": "Content suitable for adults only (18+)",
                    "min_spice_level": 1,
                    "is_default": true,
                    "default_for_gender": "both"
                },
                // ... more tags
            ]
        },
        // ... more groups
    ]
}
```

If there are tags without a group, they are returned in an "Other" group with `id: null`.

## Frontend Integration

### API Client

Added to `resources/js/api/tags.js`:

```javascript
export const getGroupedTags = async (minSpiceLevel = null) => {
    const params = {};
    if (minSpiceLevel !== null) {
        params.min_spice_level = minSpiceLevel;
    }
    return await apiClient.get("/tags/grouped", { params });
};
```

### Game Store

Added to `resources/js/stores/gameStore.js`:

```javascript
const groupedTags = ref([]);

const fetchGroupedTags = async () => {
    loadingTags.value = true;
    try {
        const data = await tags.getGroupedTags(maxSpiceRating.value);
        if (data.status === "success" || data.success === true) {
            groupedTags.value = data.data;
            // Also flatten for availableTags for backward compatibility
            availableTags.value = data.data.flatMap((group) => group.tags);
        }
    } catch (err) {
        console.error("Failed to load grouped tags:", err);
        groupedTags.value = [];
    } finally {
        loadingTags.value = false;
    }
};
```

### GameSetup Component

The tag selection screen (Step 2) now displays tags organized by their groups:

```vue
<div v-for="group in groupedTags" :key="group.id || group.slug">
    <!-- Group Header -->
    <h5>{{ group.name }}</h5>
    <p>{{ group.description }}</p>
    
    <!-- Tags in this group -->
    <div v-for="tag in group.tags" :key="tag.id">
        <input type="checkbox" @change="toggleTagInPlay(tag.id)" />
        <span>{{ tag.name }}</span>
        <p>{{ tag.description }}</p>
    </div>
</div>
```

## Migrations

### Run Migrations

```bash
# Create tag groups table
php artisan migrate --path=database/migrations/2025_12_27_000001_create_tag_groups_table.php

# Add tag_group_id to tags table
php artisan migrate --path=database/migrations/2025_12_27_000002_add_tag_group_id_to_tags_table.php
```

### Seed Data

```bash
# Seed tag groups
php artisan db:seed --class=TagGroupSeeder

# Re-seed tags with group associations
php artisan db:seed --class=TagSeeder
```

Or run all seeders:

```bash
php artisan db:seed
```

## Adding New Tag Groups

To add a new tag group:

1. **Via Seeder** (recommended for initial setup):

```php
// In TagGroupSeeder.php
$tagGroups = [
    [
        'name' => 'New Group',
        'slug' => 'new-group',
        'description' => 'Description of the new group',
        'sort_order' => 5,
    ],
];
```

2. **Via Code**:

```php
use App\Models\TagGroup;

TagGroup::create([
    'name' => 'New Group',
    'slug' => 'new-group',
    'description' => 'Description of the new group',
    'sort_order' => 5,
]);
```

## Assigning Tags to Groups

When creating or updating tags, specify the `tag_group_id`:

```php
use App\Models\Tag;
use App\Models\TagGroup;

$group = TagGroup::where('slug', 'content-type')->first();

Tag::create([
    'name' => 'New Tag',
    'slug' => 'new-tag',
    'description' => 'Tag description',
    'tag_group_id' => $group->id,
    'min_spice_level' => 1,
]);
```

## Benefits

1. **Better Organization**: Tags are logically grouped making them easier to browse
2. **Improved UX**: Users can quickly find relevant tags within categories
3. **Scalability**: As more tags are added, groups keep the interface manageable
4. **Flexibility**: Tags can be ungrouped (tag_group_id = null) if needed
5. **Customizable**: Groups have descriptions to help users understand their purpose

## Backward Compatibility

- The `availableTags` array in the store still exists and is flattened from `groupedTags`
- Existing code that uses `availableTags` continues to work
- The old `/api/tags` endpoint is still available
- Tags without a group are displayed in an "Other" section