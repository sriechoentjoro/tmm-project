<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * EmailTemplate Entity
 *
 * @property int $id
 * @property string $template_key
 * @property string $subject
 * @property string $body_html
 * @property string|null $body_text
 * @property string|null $variables
 * @property string|null $description
 * @property bool $is_active
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 */
class EmailTemplate extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        'template_key' => true,
        'subject' => true,
        'body_html' => true,
        'body_text' => true,
        'variables' => true,
        'description' => true,
        'is_active' => true,
        'created' => true,
        'modified' => true,
    ];

    /**
     * Render template by replacing variables with actual data
     *
     * @param array $data Associative array of variable => value pairs
     * @param bool $html Whether to render HTML or text version
     * @return string Rendered template
     */
    public function render($data = [], $html = true)
    {
        $template = $html ? $this->body_html : $this->body_text;
        
        if (empty($template)) {
            return '';
        }

        // Replace variables in format {{variable_name}}
        foreach ($data as $key => $value) {
            $template = str_replace('{{' . $key . '}}', $value, $template);
        }

        return $template;
    }

    /**
     * Render subject by replacing variables with actual data
     *
     * @param array $data Associative array of variable => value pairs
     * @return string Rendered subject
     */
    public function renderSubject($data = [])
    {
        $subject = $this->subject;

        foreach ($data as $key => $value) {
            $subject = str_replace('{{' . $key . '}}', $value, $subject);
        }

        return $subject;
    }

    /**
     * Get available variables as array
     *
     * @return array
     */
    public function getVariables()
    {
        if (empty($this->variables)) {
            return [];
        }

        $decoded = json_decode($this->variables, true);
        return is_array($decoded) ? $decoded : [];
    }
}
