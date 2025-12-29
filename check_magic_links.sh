#!/bin/bash
echo "=== Checking for Magic Links in Logs ==="
echo ""
tail -n 500 storage/logs/laravel.log | grep -A 30 "Magic Login Link" | grep -E "(Magic Login Link|Log In:|http)" | head -n 20
