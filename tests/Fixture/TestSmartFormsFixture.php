<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * TestSmartFormsFixture
 */
class TestSmartFormsFixture extends TestFixture
{
    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'title' => ['type' => 'string', 'length' => 255, 'null' => false, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'description' => ['type' => 'text', 'length' => null, 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => '', 'precision' => null],
        'is_active' => ['type' => 'boolean', 'length' => null, 'null' => true, 'default' => '1', 'comment' => '', 'precision' => null],
        'image_url' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Will be detected as image upload', 'precision' => null, 'fixed' => null],
        'photo_path' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Will be detected as image upload', 'precision' => null, 'fixed' => null],
        'file_path' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Will be detected as file upload', 'precision' => null, 'fixed' => null],
        'attachment' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Will be detected as file upload', 'precision' => null, 'fixed' => null],
        'purchase_date' => ['type' => 'date', 'length' => null, 'null' => true, 'default' => null, 'comment' => 'Will use datepicker', 'precision' => null],
        'delivery_date' => ['type' => 'date', 'length' => null, 'null' => true, 'default' => null, 'comment' => 'Will use datepicker', 'precision' => null],
        'tanggal_lahir' => ['type' => 'date', 'length' => null, 'null' => true, 'default' => null, 'comment' => 'Will use datepicker (Indonesian)', 'precision' => null],
        'email' => ['type' => 'string', 'length' => 100, 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Will have email validation', 'precision' => null, 'fixed' => null],
        'contact_email' => ['type' => 'string', 'length' => 100, 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Will have email validation', 'precision' => null, 'fixed' => null],
        'name_katakana' => ['type' => 'string', 'length' => 100, 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Will use kana.js for katakana', 'precision' => null, 'fixed' => null],
        'name_hiragana' => ['type' => 'string', 'length' => 100, 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Will use kana.js for hiragana', 'precision' => null, 'fixed' => null],
        'supplier_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => 'Will be dropdown select', 'precision' => null, 'autoIncrement' => null],
        'storage_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => 'Will be dropdown select', 'precision' => null, 'autoIncrement' => null],
        'quantity' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => '0', 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'price' => ['type' => 'decimal', 'length' => 10, 'precision' => 2, 'unsigned' => false, 'null' => true, 'default' => '0.00', 'comment' => ''],
        'created' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'modified' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
        ],
        '_options' => [
            'engine' => 'InnoDB',
            'collation' => 'utf8mb4_general_ci'
        ],
    ];
    // @codingStandardsIgnoreEnd
    /**
     * Init method
     *
     * @return void
     */
    public function init()
    {
        $this->records = [
            [
                'id' => 1,
                'title' => 'Lorem ipsum dolor sit amet',
                'description' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
                'is_active' => 1,
                'image_url' => 'Lorem ipsum dolor sit amet',
                'photo_path' => 'Lorem ipsum dolor sit amet',
                'file_path' => 'Lorem ipsum dolor sit amet',
                'attachment' => 'Lorem ipsum dolor sit amet',
                'purchase_date' => '2025-11-07',
                'delivery_date' => '2025-11-07',
                'tanggal_lahir' => '2025-11-07',
                'email' => 'Lorem ipsum dolor sit amet',
                'contact_email' => 'Lorem ipsum dolor sit amet',
                'name_katakana' => 'Lorem ipsum dolor sit amet',
                'name_hiragana' => 'Lorem ipsum dolor sit amet',
                'supplier_id' => 1,
                'storage_id' => 1,
                'quantity' => 1,
                'price' => 1.5,
                'created' => '2025-11-07 19:33:01',
                'modified' => '2025-11-07 19:33:01',
            ],
        ];
        parent::init();
    }
}
