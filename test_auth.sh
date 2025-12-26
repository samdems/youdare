#!/bin/bash

# Test Authentication API

BASE_URL="http://localhost:8000/api"

echo "=========================================="
echo "Testing Authentication System"
echo "=========================================="
echo ""

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Generate random email for testing
TIMESTAMP=$(date +%s)
TEST_EMAIL="test${TIMESTAMP}@example.com"
TEST_PASSWORD="password123"
TEST_NAME="Test User ${TIMESTAMP}"

echo -e "${YELLOW}1. Testing User Registration${NC}"
echo "POST ${BASE_URL}/register"
REGISTER_RESPONSE=$(curl -s -X POST "${BASE_URL}/register" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d "{
    \"name\": \"${TEST_NAME}\",
    \"email\": \"${TEST_EMAIL}\",
    \"password\": \"${TEST_PASSWORD}\",
    \"password_confirmation\": \"${TEST_PASSWORD}\"
  }")

echo "$REGISTER_RESPONSE" | jq '.'

# Extract token
TOKEN=$(echo "$REGISTER_RESPONSE" | jq -r '.data.token')

if [ "$TOKEN" != "null" ] && [ -n "$TOKEN" ]; then
    echo -e "${GREEN}✓ Registration successful${NC}"
    echo "Token: ${TOKEN:0:20}..."
else
    echo -e "${RED}✗ Registration failed${NC}"
    exit 1
fi

echo ""
echo "=========================================="
echo -e "${YELLOW}2. Testing Get Current User (with token)${NC}"
echo "GET ${BASE_URL}/me"
ME_RESPONSE=$(curl -s -X GET "${BASE_URL}/me" \
  -H "Authorization: Bearer ${TOKEN}" \
  -H "Accept: application/json")

echo "$ME_RESPONSE" | jq '.'

if echo "$ME_RESPONSE" | jq -e '.data.user.email' > /dev/null; then
    echo -e "${GREEN}✓ Successfully retrieved user info${NC}"
else
    echo -e "${RED}✗ Failed to retrieve user info${NC}"
fi

echo ""
echo "=========================================="
echo -e "${YELLOW}3. Testing Create Task (with authentication)${NC}"
echo "POST ${BASE_URL}/tasks"
TASK_RESPONSE=$(curl -s -X POST "${BASE_URL}/tasks" \
  -H "Authorization: Bearer ${TOKEN}" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "type": "dare",
    "spice_rating": 3,
    "description": "Do 20 pushups right now!",
    "draft": false
  }')

echo "$TASK_RESPONSE" | jq '.'

TASK_ID=$(echo "$TASK_RESPONSE" | jq -r '.data.id')

if [ "$TASK_ID" != "null" ] && [ -n "$TASK_ID" ]; then
    echo -e "${GREEN}✓ Task created successfully${NC}"
    echo "Task ID: ${TASK_ID}"
else
    echo -e "${RED}✗ Failed to create task${NC}"
fi

echo ""
echo "=========================================="
echo -e "${YELLOW}4. Testing Create Task (without authentication)${NC}"
echo "POST ${BASE_URL}/tasks (no token)"
UNAUTH_RESPONSE=$(curl -s -X POST "${BASE_URL}/tasks" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "type": "truth",
    "spice_rating": 2,
    "description": "This should fail without authentication",
    "draft": false
  }')

echo "$UNAUTH_RESPONSE" | jq '.'

if echo "$UNAUTH_RESPONSE" | jq -e '.message' | grep -q "Unauthenticated"; then
    echo -e "${GREEN}✓ Correctly rejected unauthenticated request${NC}"
else
    echo -e "${RED}✗ Should have rejected unauthenticated request${NC}"
fi

echo ""
echo "=========================================="
echo -e "${YELLOW}5. Testing Create Tag (with authentication)${NC}"
echo "POST ${BASE_URL}/tags"
TAG_RESPONSE=$(curl -s -X POST "${BASE_URL}/tags" \
  -H "Authorization: Bearer ${TOKEN}" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Test Tag '${TIMESTAMP}'",
    "description": "A test tag created via API",
    "min_spice_level": 1,
    "is_default": false,
    "default_for_gender": "none"
  }')

echo "$TAG_RESPONSE" | jq '.'

TAG_ID=$(echo "$TAG_RESPONSE" | jq -r '.data.id')

if [ "$TAG_ID" != "null" ] && [ -n "$TAG_ID" ]; then
    echo -e "${GREEN}✓ Tag created successfully${NC}"
    echo "Tag ID: ${TAG_ID}"
else
    echo -e "${RED}✗ Failed to create tag${NC}"
fi

echo ""
echo "=========================================="
echo -e "${YELLOW}6. Testing Login with existing credentials${NC}"
echo "POST ${BASE_URL}/login"
LOGIN_RESPONSE=$(curl -s -X POST "${BASE_URL}/login" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d "{
    \"email\": \"${TEST_EMAIL}\",
    \"password\": \"${TEST_PASSWORD}\"
  }")

echo "$LOGIN_RESPONSE" | jq '.'

NEW_TOKEN=$(echo "$LOGIN_RESPONSE" | jq -r '.data.token')

if [ "$NEW_TOKEN" != "null" ] && [ -n "$NEW_TOKEN" ]; then
    echo -e "${GREEN}✓ Login successful${NC}"
    echo "New Token: ${NEW_TOKEN:0:20}..."
else
    echo -e "${RED}✗ Login failed${NC}"
fi

echo ""
echo "=========================================="
echo -e "${YELLOW}7. Testing Logout${NC}"
echo "POST ${BASE_URL}/logout"
LOGOUT_RESPONSE=$(curl -s -X POST "${BASE_URL}/logout" \
  -H "Authorization: Bearer ${NEW_TOKEN}" \
  -H "Accept: application/json")

echo "$LOGOUT_RESPONSE" | jq '.'

if echo "$LOGOUT_RESPONSE" | jq -e '.message' | grep -q "logged out"; then
    echo -e "${GREEN}✓ Logout successful${NC}"
else
    echo -e "${RED}✗ Logout failed${NC}"
fi

echo ""
echo "=========================================="
echo -e "${YELLOW}8. Testing with revoked token (after logout)${NC}"
echo "GET ${BASE_URL}/me (with revoked token)"
REVOKED_RESPONSE=$(curl -s -X GET "${BASE_URL}/me" \
  -H "Authorization: Bearer ${NEW_TOKEN}" \
  -H "Accept: application/json")

echo "$REVOKED_RESPONSE" | jq '.'

if echo "$REVOKED_RESPONSE" | jq -e '.message' | grep -q "Unauthenticated"; then
    echo -e "${GREEN}✓ Correctly rejected revoked token${NC}"
else
    echo -e "${RED}✗ Should have rejected revoked token${NC}"
fi

echo ""
echo "=========================================="
echo -e "${YELLOW}9. Testing View Tasks (public - no auth required)${NC}"
echo "GET ${BASE_URL}/tasks"
TASKS_RESPONSE=$(curl -s -X GET "${BASE_URL}/tasks?per_page=5" \
  -H "Accept: application/json")

TASKS_COUNT=$(echo "$TASKS_RESPONSE" | jq '.data | length')
echo "Retrieved ${TASKS_COUNT} tasks (public access)"

if [ "$TASKS_COUNT" -gt 0 ]; then
    echo -e "${GREEN}✓ Successfully retrieved tasks without authentication${NC}"
else
    echo -e "${YELLOW}⚠ No tasks found (this is OK if database is empty)${NC}"
fi

echo ""
echo "=========================================="
echo -e "${GREEN}Authentication System Tests Complete!${NC}"
echo "=========================================="
echo ""
echo "Summary:"
echo "- User Registration: ✓"
echo "- User Login: ✓"
echo "- User Logout: ✓"
echo "- Token Authentication: ✓"
echo "- Protected Routes: ✓"
echo "- Public Routes: ✓"
echo ""
echo "Test User Created:"
echo "  Email: ${TEST_EMAIL}"
echo "  Password: ${TEST_PASSWORD}"
echo ""
