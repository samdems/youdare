# Gender Filter Feature for {{someone}} Template Variable

## Overview

Added a gender filter to the "someone" template variable system, allowing tasks to specify whether `{{someone}}` should select a player of the same gender, different gender, or any gender relative to the current player.

## Database Changes

### Migration: `add_someone_gender_to_tasks_table`

Added new column to `tasks` table:
- `someone_gender` (enum: 'any', 'same', 'other') - Default: 'any'
  - `any`: No gender restriction (default behavior)
  - `same`: Select a player with the same gender as the current player
  - `other`: Select a player with a different gender than the current player

## Model Changes

### Task Model (`app/Models/Task.php`)

Added `someone_gender` to:
- `$fillable` array
- `$casts` array (as string)

## Controller Changes

### TaskController (`app/Http/Controllers/TaskController.php`)

Added validation for `someone_gender` in both `store()` and `update()` methods:
```php
"someone_gender" => "nullable|in:any,same,other",
```

### API TaskController (`app/Http/Controllers/Api/TaskController.php`)

Added validation and support for `someone_gender` in both `store()` and `update()` methods.
Also added missing validation for `someone_tags` and `someone_cant_have_tags` that were previously missing from the API endpoints.

## Frontend Changes

### playerStore.js (`resources/js/stores/playerStore.js`)

Updated `processTaskDescription()` function to handle gender filtering:
- When `someone_gender` is set to `"same"`, filters eligible players to only those with the same gender as the current player
- When `someone_gender` is set to `"other"`, filters eligible players to only those with a different gender than the current player
- When `someone_gender` is `"any"` or not set, no gender filtering is applied

### Task Creation/Edit Views

Added gender filter UI in the "Someone Filters" tab of both:
- `resources/views/tasks/create.blade.php`
- `resources/views/tasks/edit.blade.php`

The UI includes three radio button options styled as cards:
1. **Any Gender** (🌐) - Default, no restriction
2. **Same Gender** (👥) - Must be same gender as current player
3. **Other Gender** (🔄) - Must be different gender from current player

## Usage Example

When creating or editing a task, you can now:

1. Use the `{{someone}}` template variable in your task description
2. Navigate to the "Someone Filters" tab
3. Select the gender filter option:
   - Choose "Same Gender" for tasks like "Give {{someone}} a high five" where gender matching matters
   - Choose "Other Gender" for tasks like "Kiss {{someone}} on the cheek" where opposite gender is preferred
   - Leave as "Any Gender" for tasks where gender doesn't matter

## Filter Priority

The gender filter works in combination with other someone filters:
1. Excludes the current player
2. Applies tag-based filtering (someone_tags and someone_cant_have_tags)
3. Applies gender filtering (someone_gender)
4. Randomly selects from remaining eligible players

If no eligible players remain after all filters are applied, `{{someone}}` will be replaced with the literal text "someone".

## Technical Notes

- Gender values in the system are: `"male"` and `"female"` (stored in players table)
- Players without a gender value will only be eligible when `someone_gender` is set to `"any"`
- The filter is relative to the current player, so the same task may select different gendered players depending on who's turn it is