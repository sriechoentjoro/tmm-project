#!/bin/bash
# Fix DashboardController to use TableRegistry instead of loadModel with array

cd /var/www/tmm/src/Controller

# Backup original
cp DashboardController.php DashboardController.php.bak

# Add TableRegistry use statement after AppController
sed -i '4a use Cake\\ORM\\TableRegistry;' DashboardController.php

# Fix getTotalCount method - use TableRegistry
sed -i '/protected function getTotalCount/,/^    }$/{
    s/\$table = \$this->loadModel(\$modelName);/\$table = TableRegistry::getTableLocator()->get(\$modelName);/
}' DashboardController.php

# Fix getCountByCondition method
sed -i '/protected function getCountByCondition/,/^    }$/{
    s/\$table = \$this->loadModel(\$modelName, compact(.connection.));/\$table = TableRegistry::getTableLocator()->get(\$modelName, [\x27connection\x27 => \$connection]);/
}' DashboardController.php

# Fix getRecentRecords method
sed -i '/protected function getRecentRecords/,/^    }$/{
    s/\$table = \$this->loadModel(\$modelName, compact(.connection.));/\$table = TableRegistry::getTableLocator()->get(\$modelName, [\x27connection\x27 => \$connection]);/
}' DashboardController.php

# Fix getRecentRecordsByCondition method
sed -i '/protected function getRecentRecordsByCondition/,/^    }$/{
    s/\$table = \$this->loadModel(\$modelName, compact(.connection.));/\$table = TableRegistry::getTableLocator()->get(\$modelName, [\x27connection\x27 => \$connection]);/
}' DashboardController.php

echo "DashboardController fixed - now using TableRegistry"
echo "Backup saved as DashboardController.php.bak"
