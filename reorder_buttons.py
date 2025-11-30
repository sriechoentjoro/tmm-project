import os
import re
from pathlib import Path

print("Reordering buttons: View -> Edit -> Delete...")

# Find all index.ctp files
index_files = []
for root, dirs, files in os.walk("src/Template"):
    for file in files:
        if file == "index.ctp":
            full_path = os.path.join(root, file)
            if "Bake" not in full_path and "Dashboard" not in full_path and "Inventories" not in full_path:
                index_files.append(full_path)

updated = 0

for filepath in index_files:
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()
    
    original = content
    
    # Pattern to match the three buttons in current order: Edit, Delete, View
    pattern = r'(<div class="action-buttons-hover">)\s+' \
              r'(<?= \$this->Html->link\(\s+' \
              r'\'<i class="fas fa-edit"></i>\',.*?' \
              r'\) \?>)\s+' \
              r'(<?= \$this->Form->postLink\(\s+' \
              r'\'<i class="fas fa-trash"></i>\',.*?' \
              r'\) \?>)\s+' \
              r'(<?= \$this->Html->link\(\s+' \
              r'\'<i class="fas fa-expand"></i>\',.*?' \
              r'\) \?>)'
    
    # Replacement: View, Edit, Delete
    replacement = r'\1\n                        \4\n                        \2\n                        \3'
    
    content_new = re.sub(pattern, replacement, content, flags=re.DOTALL)
    
    if content_new != original:
        with open(filepath, 'w', encoding='utf-8', newline='') as f:
            f.write(content_new)
        print(f"[OK] {os.path.basename(os.path.dirname(filepath))}/index.ctp")
        updated += 1

print(f"\nReordered {updated} files")
