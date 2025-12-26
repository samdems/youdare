#!/bin/bash

echo "=== Testing Gender Tag Auto-Assignment via API ==="
echo ""

# Get CSRF token
echo "1. Getting CSRF token..."
CSRF=$(curl -s http://localhost:8000 | grep -oP 'csrf-token" content="\K[^"]+')
echo "CSRF Token: $CSRF"
echo ""

# Create a game
echo "2. Creating a game..."
GAME_RESPONSE=$(curl -s -X POST http://localhost:8000/api/games \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: $CSRF" \
  -H "Cookie: XSRF-TOKEN=$CSRF" \
  -d '{
    "name": "Test Game",
    "max_spice_rating": 5,
    "tag_ids": []
  }')

echo "Game Response: $GAME_RESPONSE"
GAME_ID=$(echo $GAME_RESPONSE | grep -oP '"id":\K[0-9]+' | head -1)
echo "Game ID: $GAME_ID"
echo ""

# Create a male player
echo "3. Creating a MALE player..."
MALE_RESPONSE=$(curl -s -X POST http://localhost:8000/api/games/$GAME_ID/players \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: $CSRF" \
  -H "Cookie: XSRF-TOKEN=$CSRF" \
  -d '{
    "name": "John",
    "gender": "male",
    "tag_ids": []
  }')

echo "Male Player Response:"
echo $MALE_RESPONSE | jq '.'
echo ""

# Create a female player
echo "4. Creating a FEMALE player..."
FEMALE_RESPONSE=$(curl -s -X POST http://localhost:8000/api/games/$GAME_ID/players \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: $CSRF" \
  -H "Cookie: XSRF-TOKEN=$CSRF" \
  -d '{
    "name": "Jane",
    "gender": "female",
    "tag_ids": []
  }')

echo "Female Player Response:"
echo $FEMALE_RESPONSE | jq '.'
echo ""

echo "=== Test Complete ==="
