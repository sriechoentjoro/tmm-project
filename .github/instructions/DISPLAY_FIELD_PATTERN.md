# Display Field Pattern for CakePHP Tables

## Overview
All database tables should have a meaningful `displayField` that provides human-readable identification of records. This pattern explains how to implement displayField for different scenarios.

## Standard DisplayField Setup

### Basic Pattern (Static Field)
For tables with a single identifying field (title, name, fullname):

```php
// In Table class (e.g., AcceptanceOrganizationsTable.php)
public function initialize(array $config)
{
    parent::initialize($config);
    $this->setTable('acceptance_organizations');
    $this->setDisplayField('title'); // or 'name', 'fullname'
    $this->setPrimaryKey('id');
}
```

### Virtual Field Pattern (Dynamic/Computed)
For tables requiring computed display values (multiple fields, formatting):

**Example: ApprenticeOrders (Organization + Year + Month)**

#### 1. Database Field (Optional but Recommended)
```sql
ALTER TABLE apprentice_orders 
ADD COLUMN display_field VARCHAR(512) NULL,
ADD INDEX idx_display_field (display_field);
```

#### 2. Table Class Setup
```php
// src/Model/Table/ApprenticeOrdersTable.php
class ApprenticeOrdersTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);
        $this->setTable('apprentice_orders');
        $this->setDisplayField('display_field'); // Use virtual field
        $this->setPrimaryKey('id');
        
        // Define associations needed for display_field
        $this->belongsTo('AcceptanceOrganizations', [
            'foreignKey' => 'acceptance_organization_id',
            'strategy' => 'select'
        ]);
    }
    
    /**
     * Auto-generate display_field on save
     */
    public function beforeSave($event, $entity, $options)
    {
        if ($entity->isDirty('acceptance_organization_id') || 
            $entity->isDirty('departure_year') || 
            $entity->isDirty('departure_month')) {
            
            // Load association if needed
            if (!$entity->has('acceptance_organization') && $entity->acceptance_organization_id) {
                $org = $this->AcceptanceOrganizations->get($entity->acceptance_organization_id);
                $entity->acceptance_organization = $org;
            }
            
            $entity->display_field = $this->generateDisplayField($entity);
        }
    }
    
    /**
     * Generate display field format: "Organization Name (Year Month)"
     */
    public function generateDisplayField($entity)
    {
        $parts = [];
        
        // Organization name/title
        if ($entity->has('acceptance_organization') && $entity->acceptance_organization) {
            $parts[] = $entity->acceptance_organization->title ?: 
                       $entity->acceptance_organization->name ?: 
                       'Unknown Organization';
        } else {
            $parts[] = 'No Organization';
        }
        
        // Year and Month
        $yearMonth = [];
        if ($entity->departure_year) {
            $yearMonth[] = $entity->departure_year;
        }
        if ($entity->departure_month) {
            $monthNames = [
                1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
                5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
                9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
            ];
            $monthName = $monthNames[$entity->departure_month] ?? $entity->departure_month;
            $yearMonth[] = $monthName;
        }
        
        if (!empty($yearMonth)) {
            $parts[] = '(' . implode(' ', $yearMonth) . ')';
        }
        
        return implode(' ', $parts);
    }
}
```

#### 3. Entity Class (Virtual Getter)
```php
// src/Model/Entity/ApprenticeOrder.php
class ApprenticeOrder extends Entity
{
    protected $_accessible = [
        'display_field' => true,
        'acceptance_organization_id' => true,
        'departure_year' => true,
        'departure_month' => true,
        // ... other fields
    ];
    
    /**
     * Virtual field getter for display_field
     * Fallback if database field is empty
     */
    protected function _getDisplayField()
    {
        // Use database field if set
        if (isset($this->_properties['display_field']) && !empty($this->_properties['display_field'])) {
            return $this->_properties['display_field'];
        }
        
        // Otherwise generate it
        $parts = [];
        
        if ($this->has('acceptance_organization') && $this->acceptance_organization) {
            $parts[] = $this->acceptance_organization->title ?: 
                       $this->acceptance_organization->name ?: 
                       'Unknown Organization';
        } else {
            $parts[] = 'No Organization';
        }
        
        $yearMonth = [];
        if (isset($this->departure_year) && $this->departure_year) {
            $yearMonth[] = $this->departure_year;
        }
        if (isset($this->departure_month) && $this->departure_month) {
            $monthNames = [
                1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
                5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
                9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
            ];
            $monthName = $monthNames[$this->departure_month] ?? $this->departure_month;
            $yearMonth[] = $monthName;
        }
        
        if (!empty($yearMonth)) {
            $parts[] = '(' . implode(' ', $yearMonth) . ')';
        }
        
        return implode(' ', $parts);
    }
}
```

#### 4. Database Trigger (Optional - Auto-update on SQL level)
```sql
-- Auto-update display_field on INSERT
CREATE TRIGGER apprentice_orders_before_insert
BEFORE INSERT ON apprentice_orders
FOR EACH ROW
BEGIN
    DECLARE org_name VARCHAR(255);
    DECLARE month_name VARCHAR(20);
    
    SELECT IFNULL(title, IFNULL(name, 'No Organization'))
    INTO org_name
    FROM cms_masters.acceptance_organizations
    WHERE id = NEW.acceptance_organization_id;
    
    SET month_name = CASE NEW.departure_month
        WHEN 1 THEN 'January'
        WHEN 2 THEN 'February'
        WHEN 3 THEN 'March'
        WHEN 4 THEN 'April'
        WHEN 5 THEN 'May'
        WHEN 6 THEN 'June'
        WHEN 7 THEN 'July'
        WHEN 8 THEN 'August'
        WHEN 9 THEN 'September'
        WHEN 10 THEN 'October'
        WHEN 11 THEN 'November'
        WHEN 12 THEN 'December'
        ELSE IFNULL(NEW.departure_month, '')
    END;
    
    SET NEW.display_field = CONCAT(
        IFNULL(org_name, 'No Organization'),
        ' (',
        IFNULL(NEW.departure_year, ''),
        ' ',
        month_name,
        ')'
    );
END;

-- Auto-update display_field on UPDATE
CREATE TRIGGER apprentice_orders_before_update
BEFORE UPDATE ON apprentice_orders
FOR EACH ROW
BEGIN
    DECLARE org_name VARCHAR(255);
    DECLARE month_name VARCHAR(20);
    
    IF NEW.acceptance_organization_id <> OLD.acceptance_organization_id OR
       NEW.departure_year <> OLD.departure_year OR
       NEW.departure_month <> OLD.departure_month THEN
        
        SELECT IFNULL(title, IFNULL(name, 'No Organization'))
        INTO org_name
        FROM cms_masters.acceptance_organizations
        WHERE id = NEW.acceptance_organization_id;
        
        SET month_name = CASE NEW.departure_month
            WHEN 1 THEN 'January'
            WHEN 2 THEN 'February'
            WHEN 3 THEN 'March'
            WHEN 4 THEN 'April'
            WHEN 5 THEN 'May'
            WHEN 6 THEN 'June'
            WHEN 7 THEN 'July'
            WHEN 8 THEN 'August'
            WHEN 9 THEN 'September'
            WHEN 10 THEN 'October'
            WHEN 11 THEN 'November'
            WHEN 12 THEN 'December'
            ELSE IFNULL(NEW.departure_month, '')
        END;
        
        SET NEW.display_field = CONCAT(
            IFNULL(org_name, 'No Organization'),
            ' (',
            IFNULL(NEW.departure_year, ''),
            ' ',
            month_name,
            ')'
        );
    END IF;
END;
```

## Usage in Templates

### View Template Header
```php
<h1>
    <?= __('Apprentice Order') ?>: <?= h($apprenticeOrder->display_field) ?>
</h1>
```
**Output**: "Apprentice Order: PT ASAHI FAMILY (2026 February)"

### Index Template
```php
<td><?= h($apprenticeOrder->display_field) ?></td>
```

### Dropdown/Select
```php
<?= $this->Form->control('apprentice_order_id', [
    'options' => $apprenticeOrders, // Automatically uses display_field
    'empty' => 'Select Order'
]) ?>
```

## DisplayField Decision Matrix

| Scenario | Display Field | Implementation |
|----------|---------------|----------------|
| Single text field | `title`, `name`, `fullname` | Static: `setDisplayField('title')` |
| Multiple fields | `display_field` | Virtual: Entity getter + beforeSave |
| Formatted output | `display_field` | Virtual: Entity getter + beforeSave |
| Cross-database lookup | `display_field` | Virtual + Database trigger |
| High-frequency reads | `display_field` (DB column) | Database trigger for performance |

## Common DisplayField Patterns

### Pattern 1: Title/Name Field
```php
$this->setDisplayField('title');
// or
$this->setDisplayField('name');
```

### Pattern 2: Fullname (User/Person)
```php
$this->setDisplayField('fullname');
```

### Pattern 3: Composite (First + Last Name)
```php
// Entity getter
protected function _getDisplayField()
{
    return trim($this->first_name . ' ' . $this->last_name);
}
```

### Pattern 4: With Association (Foreign Key)
```php
// Entity getter
protected function _getDisplayField()
{
    if ($this->has('organization') && $this->organization) {
        return $this->organization->name . ' - ' . $this->code;
    }
    return 'Unknown';
}
```

### Pattern 5: Date-based
```php
// Entity getter
protected function _getDisplayField()
{
    return $this->title . ' (' . $this->created->format('Y-m-d') . ')';
}
```

## Best Practices

1. **Always set displayField**: Every table should have a meaningful display field
2. **Use database column for performance**: If display_field is computed frequently
3. **Implement Entity getter**: As fallback if database field is empty
4. **Update on save**: Use beforeSave callback to keep display_field current
5. **Use triggers for cross-DB**: When associations span multiple databases
6. **Index display_field**: Add index for better query performance
7. **Handle NULL values**: Always provide fallback for missing associations
8. **Keep format consistent**: Use consistent formatting across similar entities

## Troubleshooting

**Problem**: Display field shows ID instead of name
**Solution**: Check that displayField is set and associations are loaded with `contain`

**Problem**: Display field not updating
**Solution**: Verify beforeSave callback is firing and display_field is accessible

**Problem**: NULL display field
**Solution**: Implement Entity getter as fallback, check association loading

**Problem**: Slow queries
**Solution**: Add database column + trigger instead of pure virtual field

## Migration Checklist

When adding display_field to existing table:

- [ ] Add `display_field` column to database
- [ ] Add index on `display_field` column
- [ ] Update Table class: `setDisplayField('display_field')`
- [ ] Add `beforeSave` callback to Table class
- [ ] Add `generateDisplayField()` method to Table class
- [ ] Add Entity `_getDisplayField()` getter method
- [ ] Update Entity `$_accessible` to include `display_field`
- [ ] Create database triggers (INSERT and UPDATE)
- [ ] Backfill existing records with display_field values
- [ ] Update view templates to use `display_field`
- [ ] Clear cache: `bin/cake cache clear_all`
- [ ] Test CRUD operations

## See Also
- `DATABASE_MAPPING_REFERENCE.md` - Association strategy patterns
- `FILE_VIEWER_USAGE.md` - File preview with showPreview pattern
- `copilot-instructions.md` - Main coding guidelines
