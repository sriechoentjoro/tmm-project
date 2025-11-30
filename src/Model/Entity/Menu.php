<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Menu Entity
 *
 * @property int $id
 * @property int|null $parent_id
 * @property string $title
 * @property string|null $url
 * @property string|null $icon
 * @property string|null $target
 * @property int|null $sort_order
 * @property bool|null $is_active
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\ParentMenu $parent_menu
 * @property \App\Model\Entity\ChildMenu[] $child_menus
 */
class Menu extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        'parent_id' => true,
        'title' => true,
        'url' => true,
        'icon' => true,
        'target' => true,
        'sort_order' => true,
        'is_active' => true,
        'created' => true,
        'modified' => true,
        'parent_menu' => true,
        'child_menus' => true,
    ];
}
