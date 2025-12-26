# Simplified Tag System

## Overview

The tag system has been simplified to use **frontend pre-selection** instead of backend auto-assignment logic. This makes the system more transparent, predictable, and gives users full control.

## What Changed

### ❌ Old System (Complex)
- Backend had special `boot()` method in Player model
- Automatic tag assignment when player created
- Automatic tag reassignment when gender changed
- Special `assignGenderTags()` and `removeGenderTags()` methods
- Hidden logic that users couldn't see or control
- Tags "magically" appeared after game creation

### ✅ New System (Simple)
- Frontend pre-selects default tags based on gender choice
- Backend simply saves whatever tags are sent
- No special logic in Player model
- No hidden auto-assignment
- Users see exactly what tags will be assigned
- Users can modify tags before creating the game

## How It Works

### 1. User Selects Gender

When adding a player in `GameSetup.vue`:

```javascript
// User selects "Male" from dropdown
newPlayerGender = "male"
```

UI shows preview:
```
🏷️ Will auto-select: Male, Boxers
```

### 2. Frontend Pre-Selects Tags

When clicking "Add Player", the `addPlayer()` method:

```javascript
addPlayer() {
    // Auto-select default gender tags
    const defaultTags = [];
    if (this.newPlayerGender === "male") {
        const maleTag = this.availableTags.find(t => t.slug === "male");
        const boxersTag = this.availableTags.find(t => t.slug === "boxers");
        if (maleTag) defaultTags.push(maleTag.id);
        if (boxersTag) defaultTags.push(boxersTag.id);
    } else if (this.newPlayerGender === "female") {
        const femaleTags = ["female", "bra", "skirt", "dress", "panties"];
        femaleTags.forEach(slug => {
            const tag = this.availableTags.find(t => t.slug === slug);
            if (tag) defaultTags.push(tag.id);
        });
    }
    
    // Create player with pre-selected tags
    const player = {
        name: name,
        gender: this.newPlayerGender,
        tags: defaultTags  // ← Pre-selected tags
    };
    
    this.players.push(player);
}
```

### 3. User Can Modify Tags

After player is added, tags are **immediately visible** as badges:

```
┌──────────────────────────────────────┐
│ 😎  John 👨                       ✕ │
│     Male · 2 tags                    │
│                                      │
│     [Male ✕] [Boxers ✕]            │
│                                      │
│     ▶ ➕ Add more tags for John     │
└──────────────────────────────────────┘
```

Users can:
- **Click ✕** on any badge to remove it
- **Expand** "Add more tags" to add additional tags
- **See exactly** what tags the player has

### 4. Backend Saves Tags

When creating the game, frontend sends:

```javascript
fetch(`/api/games/${game.id}/players`, {
    method: "POST",
    body: JSON.stringify({
        name: player.name,
        gender: player.gender,
        tag_ids: player.tags  // ← Just the tag IDs
    })
});
```

Backend in `PlayerController`:

```php
// Create player
$player = $game->players()->create([
    "name" => $request->name,
    "gender" => $request->gender,
    "order" => $nextOrder,
]);

// Simply sync the provided tags
if ($request->has("tag_ids")) {
    $player->tags()->sync($request->tag_ids);
}
```

No special logic. No magic. Just saves what was sent.

## Default Tag Mappings

### Male Gender
- `Male` tag (slug: `male`)
- `Boxers` tag (slug: `boxers`)

### Female Gender
- `Female` tag (slug: `female`)
- `Bra` tag (slug: `bra`)
- `Skirt` tag (slug: `skirt`)
- `Dress` tag (slug: `dress`)
- `Panties` tag (slug: `panties`)

### No Gender
- No default tags selected
- User can manually add any tags

## Benefits

### ✅ Transparency
Users see exactly what tags will be assigned **before** creating the game.

### ✅ Control
Users can add or remove any default tags before creating the game.

### ✅ Simplicity
No hidden backend logic. Frontend handles UI, backend handles storage.

### ✅ Predictability
What you see is what you get. No surprises.

### ✅ Maintainability
All tag selection logic is in one place (frontend). Easy to modify.

### ✅ Flexibility
Easy to change default tags or add new gender options.

## User Experience Flow

### Adding a Male Player

1. **Enter Name**: "John"
2. **Select Gender**: Male 👨
3. **See Preview**: "🏷️ Will auto-select: Male, Boxers"
4. **Click Add**: Player added with tags visible
5. **View Tags**: `[Male ✕] [Boxers ✕]`
6. **Optional**: Remove Boxers by clicking ✕
7. **Optional**: Add "Adults Only" by expanding tag selector
8. **Create Game**: Tags are saved as-is

### Adding a Female Player

1. **Enter Name**: "Sarah"
2. **Select Gender**: Female 👩
3. **See Preview**: "🏷️ Will auto-select: Female, Bra, Skirt, Dress, Panties"
4. **Click Add**: Player added with 5 tags visible
5. **View Tags**: `[Female ✕] [Bra ✕] [Skirt ✕] [Dress ✕] [Panties ✕]`
6. **Optional**: Modify tags as needed
7. **Create Game**: Tags are saved

## Code Locations

### Frontend (GameSetup.vue)
- **Lines 75-84**: Preview text showing what tags will be selected
- **Lines 416-443**: `addPlayer()` method with tag pre-selection logic
- **Lines 139-161**: Tag display with remove buttons
- **Lines 194-215**: Expandable tag selector for adding more tags

### Backend (PlayerController.php)
- **Lines 61-75**: Simple tag sync without special logic
- **Removed**: No more `assignGenderTags()` calls
- **Removed**: No more gender change detection

### Model (Player.php)
- **Removed**: No more `boot()` method
- **Removed**: No more `assignGenderTags()` method
- **Removed**: No more `removeGenderTags()` method
- **Simplified**: Model just handles relationships

## Testing

Run the simplified tag test:

```bash
php test_simple_tags.php
```

Expected output:
```
✓ Tags are pre-selected in frontend based on gender
✓ Backend simply saves whatever tags are sent
✓ No special auto-assignment logic needed
✓ Users have full control to add/remove any tags
✓ Transparent and predictable behavior
```

## Migration Notes

### For Existing Games
- No database changes needed
- Existing player-tag relationships unchanged
- Works with existing data

### For New Features
- Add new gender? Just update `addPlayer()` method in frontend
- Change default tags? Just update the tag slug arrays
- No backend changes needed

## API Contract

### Create Player
```http
POST /api/games/{game}/players
Content-Type: application/json

{
  "name": "John",
  "gender": "male",
  "tag_ids": [11, 14]  // ← Frontend sends the tags
}
```

### Response
```json
{
  "success": true,
  "message": "Player added successfully",
  "data": {
    "id": 1,
    "name": "John",
    "gender": "male",
    "tags": [
      {"id": 11, "name": "Male", "slug": "male"},
      {"id": 14, "name": "Boxers", "slug": "boxers"}
    ]
  }
}
```

## Summary

The simplified system moves tag selection logic from the backend to the frontend where it belongs. This results in:

- **Better UX**: Users see and control tags before game creation
- **Simpler Code**: No hidden auto-assignment logic
- **Easier Maintenance**: All logic in one place
- **More Flexible**: Easy to customize and extend

The backend becomes a simple data store, and the frontend handles the intelligent UI decisions. This is the proper separation of concerns.