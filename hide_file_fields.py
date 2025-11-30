#!/usr/bin/env python3
"""
Hide File/Image Fields in All View Templates
This script removes file/image field rows from detail sections in all view.ctp files
"""

import os
import re
from pathlib import Path

# File/image field patterns
FILE_PATTERNS = r'(image|file|photo|document|foto|gambar|dokumen|attachment|pdf|scan|upload|media|mcu_files|file_path|file_name|image_path|mou_file)'

def process_view_file(filepath):
    """Process a single view.ctp file"""
    with open(filepath, 'r', encoding='utf-8') as f:
        lines = f.readlines()
    
    new_lines = []
    i = 0
    modified = False
    
    while i < len(lines):
        line = lines[i]
        
        # Check if this line contains a file field label
        if 'github-detail-label' in line and re.search(FILE_PATTERNS, line, re.IGNORECASE):
            # Extract field name
            match = re.search(r"__\('([^']+)'\)", line)
            if match:
                field_name = match.group(1)
                print(f"  → Removing: {field_name}")
                modified = True
                
                # Find start of <tr> (go backwards)
                j = i
                while j >= 0 and '<tr>' not in lines[j]:
                    j -= 1
                
                if j >= 0:
                    # Remove lines from <tr> to current
                    new_lines = new_lines[:len(new_lines) - (i - j)]
                    
                    # Add comment
                    indent = ' ' * 24
                    new_lines.append(f'{indent}<!-- {field_name} removed - already shown in preview section above -->\n')
                
                # Skip until </tr>
                while i < len(lines) and '</tr>' not in lines[i]:
                    i += 1
                i += 1  # Skip </tr>
                continue
        
        new_lines.append(line)
        i += 1
    
    if modified:
        with open(filepath, 'w', encoding='utf-8') as f:
            f.writelines(new_lines)
        return True
    return False

def main():
    print("=" * 50)
    print("Hide File Fields in View Templates")
    print("=" * 50)
    print()
    
    # Find all view.ctp files
    view_files = list(Path('src/Template').rglob('view.ctp'))
    # Exclude backup and bake templates
    view_files = [f for f in view_files if 'Backup_' not in str(f) and 'Bake/Template' not in str(f)]
    
    total = len(view_files)
    updated = 0
    
    print(f"Found {total} view.ctp files...\n")
    
    for idx, filepath in enumerate(view_files, 1):
        relative_path = filepath.relative_to(Path.cwd())
        print(f"[{idx}/{total}] {relative_path}")
        
        if process_view_file(filepath):
            updated += 1
            print("  ✓ Updated!")
        else:
            print("  - No file fields")
        print()
    
    print("=" * 50)
    print(f"Files updated: {updated} / {total}")
    print("=" * 50)

if __name__ == '__main__':
    main()
