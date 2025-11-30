#!/usr/bin/env python3

# Read the fixed JavaScript
with open('/tmp/js_replacement.txt', 'r', encoding='utf-8') as f:
    new_js = f.read()

# Fix Candidates/add.ctp
filepath = '/var/www/tmm/src/Template/Candidates/add.ctp'
with open(filepath, 'r', encoding='utf-8') as f:
    lines = f.readlines()

# Replace lines 700-836 (0-indexed: 699-835)
new_js_lines = new_js.split('\n')
lines[699:836] = [line + '\n' for line in new_js_lines]

with open(filepath, 'w', encoding='utf-8') as f:
    f.writelines(lines)

print(f'Fixed: {filepath}')
