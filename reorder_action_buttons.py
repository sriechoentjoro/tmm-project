import os
import re
import shutil
from datetime import datetime

# Configuration
template_dir = "src/Template"
backup_dir = f"template_backups/action_buttons_py_{datetime.now().strftime('%Y%m%d_%H%M%S')}"

# Create backup directory
os.makedirs(backup_dir, exist_ok=True)
print(f"✓ Backup directory created: {backup_dir}")

# Find all index.ctp files
index_files = []
for root, dirs, files in os.walk(template_dir):
    # Skip Bake directory
    if '\\Bake\\' in root or '/Bake/' in root:
        continue
    for file in files:
        if file == 'index.ctp':
            index_files.append(os.path.join(root, file))

print(f"\nFound {len(index_files)} index.ctp files to process\n")

success_count = 0
skip_count = 0
error_count = 0

for file_path in index_files:
    rel_path = os.path.relpath(file_path)
    print(f"Processing: {rel_path}")
    
    try:
        # Backup original file
        dir_name = os.path.basename(os.path.dirname(file_path))
        backup_path = os.path.join(backup_dir, f"{dir_name}_index.ctp")
        shutil.copy2(file_path, backup_path)
        
        # Read file content
        with open(file_path, 'r', encoding='utf-8') as f:
            content = f.read()
        
        # Check if file has action buttons
        if '<i class="fas fa-eye"></i>' not in content or '<i class="fas fa-edit"></i>' not in content:
            print(f"  [SKIP] No action buttons found\n")
            skip_count += 1
            continue
        
        # Check if already in correct order
        view_index = content.find("'<i class=\"fas fa-eye\"></i>'")
        edit_index = content.find("'<i class=\"fas fa-edit\"></i>'")
        
        if edit_index < view_index and edit_index > 0:
            print(f"  [SKIP] Already in correct order\n")
            skip_count += 1
            continue
        
        # Pattern to match the action buttons section
        # This will match: View button, Edit button, and optional Delete button
        pattern = re.compile(
            r"(<td[^>]*class=['\"]actions['\"][^>]*>.*?<div[^>]*>)\s*"
            r"(<\?=\s*\$this->Html->link\(\s*'<i class=\"fas fa-eye\"></i>'[^?]+\?>\s*)"
            r"(<\?=\s*\$this->Html->link\(\s*'<i class=\"fas fa-edit\"></i>'[^?]+\?>\s*)"
            r"(?:<\?=\s*\$this->Form->postLink\([^?]+\?>\s*)?"
            r"(</div>\s*</td>)",
            re.DOTALL
        )
        
        # Replacement: swap Edit and View, remove Delete
        def replace_buttons(match):
            return (
                match.group(1) + "\n" +
                "                            " + match.group(3).strip() + "\n" +
                "                            " + match.group(2).strip() + "\n" +
                "                        " + match.group(4)
            )
        
        modified = pattern.sub(replace_buttons, content)
        
        # Also update the comment
        modified = re.sub(
            r'<!-- Action Buttons with Icons -->',
            '<!-- Action Buttons with Icons: Edit (Left) | View (Right) -->',
            modified
        )
        
        # Check if any changes were made
        if modified == content:
            print(f"  [SKIP] No changes applied\n")
            skip_count += 1
            continue
        
        # Write modified content back
        with open(file_path, 'w', encoding='utf-8', newline='\n') as f:
            f.write(modified)
        
        print(f"  [SUCCESS] Reordered: Edit → View (Delete removed)\n")
        success_count += 1
        
    except Exception as e:
        print(f"  [ERROR] {str(e)}\n")
        error_count += 1

print("=" * 50)
print(f"Successfully updated: {success_count} files")
print(f"Skipped: {skip_count} files")
print(f"Errors: {error_count} files")
print(f"Backups saved to: {backup_dir}")

if success_count > 0:
    print("\n✓ Action button reorder complete!")
    print("All updated pages now show: [Edit Icon] [View Icon]")
    print("Delete buttons have been removed.")
