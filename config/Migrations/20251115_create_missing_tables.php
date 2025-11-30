<?php
use Migrations\AbstractMigration;

class CreateMissingTables extends AbstractMigration
{
    public function change()
    {
        // apprenticeship_orders table
        $table = $this->table('apprenticeship_orders');
        $table->addColumn('order_number', 'string', ['limit' => 50, 'null' => false])
              ->addColumn('candidate_id', 'integer', ['null' => false])
              ->addColumn('created', 'datetime', ['null' => false])
              ->addColumn('modified', 'datetime', ['null' => true, 'default' => null])
              ->create();

        // vocational_training_institutions table
        $table = $this->table('vocational_training_institutions');
        $table->addColumn('name', 'string', ['limit' => 255, 'null' => false])
              ->addColumn('address', 'text', ['null' => true])
              ->addColumn('created', 'datetime', ['null' => false])
              ->addColumn('modified', 'datetime', ['null' => true, 'default' => null])
              ->create();
    }
}
