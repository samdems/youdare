#!/bin/bash
echo "=== Last Magic Link Email ==="
echo ""
tail -n 1000 storage/logs/laravel.log | tac | grep -B 5 -A 25 "Magic Login Link" | tac | head -n 35
