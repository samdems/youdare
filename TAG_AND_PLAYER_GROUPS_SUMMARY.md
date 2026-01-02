# Tag and Player Groups - Complete Feature Summary

## Overview

This document summarizes the complete implementation of Tag Groups and Player Groups features, which organize tags and players into logical categories for better user experience during game setup.

---

## Tag Groups Feature

### Purpose
Organize tags into logical categories (e.g., "Content Type", "Activity Style", "Gender") to make tag selection easier during game setup.

### Database Schema

#### `tag_groups` Table
- `id` - Primary key
- `name` - Group name (e.g., "Content Type")
- `slug` - URL-friendly identifier
- `description` - Explains the purpose of the group
- `sort_order` - Controls display order
- `created_at`, `updated_at` - Timestamps

#### `tags` Table (Updated)
- Added `tag_group_id` - Foreign key to `tag_groups` (nullable, ON DELETE SET NULL)

### Predefined Tag Groups

1. **Content Type** (sort_order: 1)
   - Tags: Adults Only, Family Friendly, Party Mode, Romantic, Extreme
   - Purpose: Categories that define the overall nature and audience of the content

2. **Activity Style** (sort_order: 2)
   - Tags: Funny, Physical, Mental, Creative, Social
   - Purpose: The type of challenge or activity involved

3. **Gender** (sort_order: 3)
   - Tags: Male, Female, Any Gender
   - Purpose: Gender-specific tags for participants

4. **Clothing & Accessories** (sort_order: 4)
   - Tags: Boxers, Bra, Skirt, Dress, Panties
   - Purpose: Tags related to specific clothing items or accessories

### Backend Implementation

#### Models
- `app/Models/TagGroup.php` - TagGroup model with tags relationship
- `app/Models/Tag.php` - Updated with tagGroup relationship

#### Controllers
- `app/Http/Controllers/TagGroupController.php` - Web CRUD operations
- `app/Http/Controllers/Api/TagGroupController.php` - JSON API endpoints
- `app/Http/Controllers/TagController.php` - Updated to support tag_group_id
- `app/Http/Controllers/Api/TagController.php` - Added grouped() method and tag_group_id support

#### Seeders
- `database/seeders/TagGroupSeeder.php` - Seeds predefined tag groups
- `database/seeders/TagSeeder.php` - Updated to assign tags to groups

#### Migrations
- `2025_12_27_000001_create_tag_groups_table.php`
- `2025_12_27_000002_add_tag_group_id_to_tags_table.php`

### API Endpoints

#### Tag Groups
- `GET /api/tag-groups` - List all tag groups (paginated)
- `POST /api/tag-groups` - Create tag group (admin only)
- `GET /api/tag-groups/{id}` - Get specific tag group
- `PUT/PATCH /api/tag-groups/{id}` - Update tag group (admin only)
- `DELETE /api/tag-groups/{id}` - Delete tag group (admin only)

#### Tags (Updated)
- `GET /api/tags/grouped` - Get tags organized by tag groups (with min_spice_level filter)

### Web Routes

All routes require admin authentication:
- `GET /tag-groups` - List tag groups
- `GET /tag-groups/create` - Create form
- `POST /tag-groups` - Store new group
- `GET /tag-groups/{id}` - Show tag group details
- `GET /tag-groups/{id}/edit` - Edit form
- `PUT /tag-groups/{id}` - Update group
- `DELETE /tag-groups/{id}` - Delete group

### Frontend Implementation

#### API Client
- `resources/js/api/tags.js` - Added `getGroupedTags()` method

#### Store
- `resources/js/stores/gameStore.js`
  - New state: `groupedTags`
  - New action: `fetchGroupedTags()`
  - Preserves tag_group data when flattening tags for backward compatibility

#### Components

**GameSetup.vue (Step 2 - Set Tags)**
- Displays tags grouped by tag groups
- Each group shows:
  - Group name (uppercase, primary color)
  - Group description
  - Tags within that group
- Select All button selects all tags across all groups

**PlayerListItem.vue (Tag Modal)**
- Updated to display tags grouped by categories
- Players can select tags organized by groups
- Visual consistency with game tag selection

#### Views (Admin)
- `resources/views/tag-groups/index.blade.php` - List all tag groups
- `resources/views/tag-groups/create.blade.php` - Create new tag group
- `resources/views/tag-groups/edit.blade.php` - Edit tag group
- `resources/views/tag-groups/show.blade.php` - View tag group and its tags
- `resources/views/tags/create.blade.php` - Updated with tag group dropdown
- `resources/views/tags/edit.blade.php` - Updated with tag group dropdown

#### Navigation
Added "📁 Tag Groups" to admin navigation:
- Desktop menu
- Mobile dropdown menu
- User profile dropdown

---

## Player Groups Feature

### Purpose
Organize players into categories (e.g., teams, couples, friends) for better organization and visual distinction during gameplay.

### Database Schema

#### `player_groups` Table
- `id` - Primary key
- `name` - Group name (e.g., "Team A", "Couples")
- `slug` - URL-friendly identifier
- `description` - Purpose of the group
- `color` - Hex color code for visual distinction (default: #3b82f6)
- `icon` - Emoji or icon for the group
- `sort_order` - Controls display order
- `created_at`, `updated_at` - Timestamps

#### `players` Table (Updated)
- Added `player_group_id` - Foreign key to `player_groups` (nullable, ON DELETE SET NULL)

### Predefined Player Groups

1. **Team A** 🔵
   - Color: #3b82f6 (Blue)
   - Description: Players on Team A

2. **Team B** 🔴
   - Color: #ef4444 (Red)
   - Description: Players on Team B

3. **Couples** 💕
   - Color: #ec4899 (Pink)
   - Description: Players who are in a relationship

4. **Friends** 👥
   - Color: #10b981 (Green)
   - Description: Friends playing together

5. **VIP** ⭐
   - Color: #f59e0b (Amber/Gold)
   - Description: Special players or guests

### Backend Implementation

#### Models
- `app/Models/PlayerGroup.php` - PlayerGroup model with players relationship
- `app/Models/Player.php` - Updated with playerGroup relationship

#### Controllers
- `app/Http/Controllers/Api/PlayerGroupController.php` - JSON API endpoints
- `app/Http/Controllers/Api/PlayerController.php` - Updated to support player_group_id

#### Seeders
- `database/seeders/PlayerGroupSeeder.php` - Seeds predefined player groups

#### Migrations
- `2025_12_27_000003_create_player_groups_table.php`
- `2025_12_27_000004_add_player_group_id_to_players_table.php`

### API Endpoints

#### Player Groups
- `GET /api/player-groups` - List all player groups (paginated)
- `POST /api/player-groups` - Create player group (admin only)
- `GET /api/player-groups/{id}` - Get specific player group
- `PUT/PATCH /api/player-groups/{id}` - Update player group (admin only)
- `DELETE /api/player-groups/{id}` - Delete player group (admin only)

#### Players (Updated)
- Player creation and update now accept `player_group_id` parameter

### Frontend Implementation

#### API Client
- `resources/js/api/playerGroups.js` - Complete CRUD operations for player groups
- `resources/js/api/index.js` - Exports playerGroups module

#### Store
- `resources/js/stores/playerStore.js`
  - New state: `availablePlayerGroups`, `loadingPlayerGroups`
  - New action: `fetchPlayerGroups()`
  - Players now support `player_group_id` field

---

## Usage Instructions

### For Administrators

#### Managing Tag Groups
1. Navigate to "📁 Tag Groups" in the admin menu
2. Click "New Tag Group" to create a new category
3. Fill in:
   - Name (e.g., "Game Mode")
   - Description (e.g., "Different game modes and styles")
   - Sort Order (lower numbers appear first)
4. After creating a group, assign tags to it by editing individual tags

#### Managing Tags
1. Navigate to "🏷️ Tags" in the admin menu
2. When creating or editing a tag, select a tag group from the dropdown
3. Tags without a group will appear in an "Other" section

#### Managing Player Groups
1. Use the API endpoints to create player groups
2. Assign colors and icons for visual distinction
3. Players can be assigned to groups during game setup

### For Players (Game Setup)

#### Step 2: Set Tags
- Tags are now organized by categories
- Each category displays its name and description
- Select tags from relevant categories for your game
- "Select All" button selects all available tags

#### Step 3: Add Players
- When editing player tags, tags are organized by categories
- Easier to find and select relevant tags for each player
- Visual consistency with game tag selection

---

## Benefits

### Tag Groups
1. **Better Organization** - Tags are logically grouped making them easier to browse
2. **Improved UX** - Users can quickly find relevant tags within categories
3. **Scalability** - As more tags are added, groups keep the interface manageable
4. **Flexibility** - Tags can be ungrouped if needed
5. **Guidance** - Group descriptions help users understand tag purposes

### Player Groups
1. **Team Organization** - Organize players into teams for team-based games
2. **Visual Distinction** - Color-coded groups for easy identification
3. **Flexible Categories** - Support for couples, friends, VIPs, and custom groups
4. **Enhanced Gameplay** - Better organization leads to smoother game flow

---

## Technical Notes

### Backward Compatibility
- The `availableTags` array still exists and works as before
- Old code using flat tag arrays continues to work
- The original `/api/tags` endpoint is unchanged
- Tags without a group are displayed in an "Other" section

### Data Relationships
- Tags can exist without a tag group (tag_group_id = null)
- Players can exist without a player group (player_group_id = null)
- Deleting a tag group sets tags' tag_group_id to null (tags are not deleted)
- Deleting a player group sets players' player_group_id to null (players are not deleted)

### Performance Considerations
- Tag groups use eager loading to minimize database queries
- Grouped tags endpoint filters by spice level server-side
- Frontend caching reduces API calls during game setup

---

## Future Enhancements (Optional)

1. **Admin UI for Player Groups** - Create web views similar to tag groups
2. **Drag & Drop Reordering** - Allow admins to reorder groups visually
3. **Group Templates** - Predefined group sets for different game types
4. **Color Themes** - Apply group colors throughout the UI
5. **Group Statistics** - Show usage statistics for each group
6. **Bulk Operations** - Move multiple tags/players between groups at once

---

## Migration Commands

Run these commands to set up the feature:

```bash
# Migrate tag groups
php artisan migrate --path=database/migrations/2025_12_27_000001_create_tag_groups_table.php
php artisan migrate --path=database/migrations/2025_12_27_000002_add_tag_group_id_to_tags_table.php

# Migrate player groups
php artisan migrate --path=database/migrations/2025_12_27_000003_create_player_groups_table.php
php artisan migrate --path=database/migrations/2025_12_27_000004_add_player_group_id_to_players_table.php

# Seed data
php artisan db:seed --class=TagGroupSeeder
php artisan db:seed --class=TagSeeder
php artisan db:seed --class=PlayerGroupSeeder
```

Or run all migrations and seeds:

```bash
php artisan migrate
php artisan db:seed
```

---

## Documentation Files

- `TAG_GROUPS.md` - Technical documentation for tag groups
- `TAG_AND_PLAYER_GROUPS_SUMMARY.md` - This comprehensive summary