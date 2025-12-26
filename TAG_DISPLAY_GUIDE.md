# Tag Display and Management Guide

## Overview

This guide explains how player tags are displayed and managed throughout the game interface.

## Features

### 1. **GameSetup Component - Player Creation**

#### Visual Tag Display
- **Inline Tag List**: Tags are now displayed directly under each player's name in the player list
- **Badge Format**: Each tag appears as a removable badge with an 'X' button
- **Auto-Tag Preview**: Players with a gender but no custom tags show a preview of what will be auto-assigned

#### Tag Management
- **Easy Removal**: Click the 'X' on any tag badge to remove it from the player
- **Add More Tags**: Expand the "➕ Add more tags" section to add additional tags
- **Visual Feedback**: Selected tags are highlighted in primary color with a checkmark

#### Auto-Tagging
- **Gender Selection**: Selecting a gender shows what tags will be auto-assigned
- **Male Players**: Automatically get `Male` + `Boxers` tags
- **Female Players**: Automatically get `Female` + `Bra` + `Skirt` + `Dress` + `Panties` tags
- **Preview Text**: Shows "🏷️ Will auto-add: [tags]" below the gender selector
- **On-Demand Assignment**: Gender tags are assigned when the game is created

### 2. **GamePlay Component - Scoreboard**

#### Enhanced Player Cards
- **Gender Icon**: Shows 👨 for male, 👩 for female next to player name
- **Compact Tag Display**: Shows up to 3 tags as small badges
- **Overflow Indicator**: If more than 3 tags, shows "+N" badge with remaining count
- **Tooltip Support**: Hover over tags to see full names (via title attribute)
- **Visual Hierarchy**: Current player's tags are highlighted differently

## UI Components

### Player Card in Setup
```
┌─────────────────────────────────────────────┐
│ 😀  John 👨                              ✕ │
│     Male · 3 tags                           │
│     [Male ✕] [Boxers ✕] [Adults Only ✕]   │
│                                             │
│     ➕ Add more tags for John              │
│     └─ [Grid of available tags]            │
└─────────────────────────────────────────────┘
```

### Player Card in Scoreboard (Active)
```
┌──────────────┐
│      😎      │
│   Sarah 👩   │
│   12 pts     │
│ [Female]     │
│ [Bra] [+3]   │
└──────────────┘
```

### Player Card in Scoreboard (Inactive)
```
┌──────────────┐
│      😀      │
│   John 👨    │
│    8 pts     │
│  [Male]      │
│  [Boxers]    │
└──────────────┘
```

## Implementation Details

### GameSetup.vue Changes

1. **Enhanced Player Display Section** (Lines ~92-190)
   - Added flex-1 to player name container for better spacing
   - Added inline tag display with removable badges
   - Added auto-tag preview for players with gender but no custom tags
   - Changed collapse title to "Add more tags" for clarity

2. **New Method: `getTagName()`** (Line ~486)
   - Helper method to look up tag name by ID
   - Used to display tag names in the badge list

3. **Visual Improvements**
   - Tags show as primary badges with remove button
   - Auto-tags show as outlined badges with lower opacity
   - Better spacing and typography

### GamePlay.vue Changes

1. **Enhanced Scoreboard** (Lines ~38-95)
   - Added gender icon next to player name
   - Display up to 3 tag badges inline
   - Show "+N" badge for additional tags
   - Different badge styling for active vs inactive players
   - Tooltip support via title attribute
   - Responsive max-width to prevent overflow

## User Experience Flow

### Adding a Player with Tags

1. **Enter Name**: Type player name
2. **Select Gender** (Optional): Choose Male or Female
   - See preview of auto-assigned tags
3. **Add Player**: Click "Add Player" button
4. **View Tags**: See assigned tags inline under player name
5. **Modify Tags**: 
   - Click 'X' on any badge to remove
   - Expand "Add more tags" to add more
6. **Start Game**: Tags are saved when game is created

### During Gameplay

1. **Scoreboard Shows**: Each player's card displays their tags
2. **Current Player**: Highlighted with different badge styling
3. **Tag Visibility**: See what tags each player has at a glance
4. **Hover for Details**: Hover over "+N" to see remaining tags

## Benefits

✅ **Immediate Visibility**: See all tags without expanding menus
✅ **Quick Management**: Remove tags with single click
✅ **Clear Feedback**: Visual preview of auto-assigned tags
✅ **Space Efficient**: Compact display doesn't clutter the UI
✅ **Better UX**: Easier to manage tags during setup
✅ **Gameplay Context**: See player tags during gameplay for better understanding

## Technical Notes

### Tag Storage
- Tags are stored as array of IDs in player object
- Gender tags are auto-assigned on server via model events
- Custom tags are merged with auto-assigned tags

### API Integration
- Tags sent to API via `tag_ids` array parameter
- Server assigns gender tags automatically via `Player::boot()` method
- Response includes full tag objects with names

### Performance
- Tags loaded once on component mount
- Local tag lookup using `getTagName()` helper
- No additional API calls needed for tag display

## Future Enhancements

Potential improvements:
- Drag and drop tag reordering
- Tag filtering/search in the add tags section
- Tag categories/grouping
- Tag usage statistics
- Bulk tag operations