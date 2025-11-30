#!/bin/bash
# Add lpk() method to DashboardController

cd /var/www/tmm/src/Controller

# Find line number where to insert (after the closing brace of index method)
LINE=$(grep -n "Administrator Dashboard - Full system overview" DashboardController.php | cut -d: -f1)
INSERT_LINE=$((LINE - 2))

# Create the new method code
cat > /tmp/lpk_method.txt << 'EOF'

    /**
     * LPK action - Public method to access LPK dashboard
     * Routes to lpkDashboard()
     */
    public function lpk()
    {
        return $this->lpkDashboard();
    }

EOF

# Insert the new method
head -n $INSERT_LINE DashboardController.php > /tmp/dashboard_new.php
cat /tmp/lpk_method.txt >> /tmp/dashboard_new.php
tail -n +$((INSERT_LINE + 1)) DashboardController.php >> /tmp/dashboard_new.php

# Replace the file
mv /tmp/dashboard_new.php DashboardController.php
chown www-data:www-data DashboardController.php

echo "Method lpk() added to DashboardController"
rm /tmp/lpk_method.txt
