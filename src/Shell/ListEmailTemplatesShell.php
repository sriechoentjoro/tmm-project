<?php
namespace App\Shell;

use App\Model\Table\EmailTemplatesTable;
use Cake\Console\Shell;
use Cake\ORM\TableRegistry;

/**
 * Show what is actually in the email_templates table.
 *
 * Reads only. Nothing here writes, sends, or deletes.
 *
 * Worth having because the table is easy to be wrong about. Until recently
 * EmailComponent::sendTemplate() could not send at all - it ended on a method
 * Cake\Mailer\Email does not have - so every row in this table was decoration
 * and nobody had cause to look at it. Now that the path works, what the table
 * holds decides what arrives.
 *
 * Three things it reports that a SELECT does not:
 *
 *   wanted by the code   which template_keys the application actually asks
 *                        getTemplate() for. A key nobody asks for sends
 *                        nothing; a key the code asks for and the table lacks
 *                        makes sendTemplate() return false with a line in the
 *                        log and no email.
 *   letterhead           whether the body would be wrapped in the branded
 *                        layout or sent as written - the same question
 *                        EmailTemplatesTable::wrapsInLayout() asks at send
 *                        time, and the editor's preview asks beside the form.
 *   placeholders         the {{names}} each body uses, against what the code
 *                        supplies for that key. A name the code never passes
 *                        reaches the reader literally, as {{name}}.
 *
 * Usage:
 *     bin/cake list_email_templates            one line per template
 *     bin/cake list_email_templates --bodies   also print each body
 */
class ListEmailTemplatesShell extends Shell
{
    /**
     * What the application passes to sendTemplate(), by key.
     *
     * Taken from EmailComponent: sendRegistrationEmail() and
     * sendTestRegistrationEmail() both build the same set.
     *
     * @var array<string, array<int, string>>
     */
    protected $supplied = [
        'institution_registration' => [
            'institution_name', 'username', 'email', 'registration_url', 'expiry_date',
        ],
    ];

    /**
     * @return \Cake\Console\ConsoleOptionParser
     */
    public function getOptionParser()
    {
        return parent::getOptionParser()
            ->setDescription('Show the contents of the email_templates table. Reads only.')
            ->addOption('bodies', [
                'help' => 'Print each template body in full as well as the summary.',
                'boolean' => true,
            ]);
    }

    /**
     * @return int|null
     */
    public function main()
    {
        $table = TableRegistry::getTableLocator()->get('EmailTemplates');
        $this->out(sprintf('Connection: <info>%s</info>', $table->getConnection()->configName()));

        try {
            $rows = $table->find()->order(['template_key' => 'ASC'])->all();
        } catch (\Exception $e) {
            $this->abort('Could not read email_templates: ' . $e->getMessage());
        }

        if ($rows->isEmpty()) {
            $this->out('');
            $this->out('<warning>The table is empty.</warning>');
            $this->out('Nothing that goes through EmailComponent::sendTemplate() can send:');
            $this->out('getTemplate() returns null, the send is abandoned, and the only trace');
            $this->out('is a line in logs/error.log. The keys the code asks for are:');
            foreach (array_keys($this->supplied) as $key) {
                $this->out('  - ' . $key);
            }
            $this->out('');
            $this->out('Add one at /email-templates/add - the editor shows the message as it will arrive.');

            return null;
        }

        $this->out('');
        $this->out(sprintf(
            '  %-28s %-7s %-11s %6s %6s  %s',
            'template_key', 'active', 'letterhead', 'html', 'text', 'subject'
        ));
        $this->hr();

        $present = [];
        foreach ($rows as $row) {
            $present[$row->template_key] = true;

            // Padded before it is wrapped: %-7s counts the markup, so a cell
            // tagged <warning> ends up seven characters short of its column.
            $active = str_pad($row->is_active ? 'yes' : 'no', 7);
            if (!$row->is_active) {
                $active = '<warning>' . $active . '</warning>';
            }

            $this->out(sprintf(
                '  %-28s %s %-11s %6d %6d  %s',
                $this->shorten($row->template_key, 28),
                $active,
                EmailTemplatesTable::wrapsInLayout($row->body_html) ? 'added' : 'as written',
                strlen((string)$row->body_html),
                strlen((string)$row->body_text),
                $this->shorten((string)$row->subject, 40)
            ));

            $this->reportPlaceholders($row);

            if ($this->param('bodies')) {
                $this->out('');
                $this->out('    --- body_html ---');
                $this->out('    ' . str_replace("\n", "\n    ", (string)$row->body_html));
                if (trim((string)$row->body_text) !== '') {
                    $this->out('    --- body_text ---');
                    $this->out('    ' . str_replace("\n", "\n    ", (string)$row->body_text));
                }
                $this->out('');
            }
        }
        $this->hr();

        foreach (array_keys($this->supplied) as $key) {
            if (!isset($present[$key])) {
                $this->out(sprintf(
                    '  <warning>%s is asked for by the code but is not in the table.</warning>',
                    $key
                ));
            }
        }

        return null;
    }

    /**
     * Name the placeholders a template uses that its sender never supplies.
     *
     * @param \App\Model\Entity\EmailTemplate $row The template.
     * @return void
     */
    protected function reportPlaceholders($row)
    {
        $text = (string)$row->subject . "\n" . (string)$row->body_html . "\n" . (string)$row->body_text;
        if (!preg_match_all('/\{\{\s*([A-Za-z0-9_]+)\s*\}\}/', $text, $m)) {
            return;
        }

        $used = array_values(array_unique($m[1]));
        if (!isset($this->supplied[$row->template_key])) {
            // Nothing asks for this key, so nothing supplies anything to it.
            $this->out(sprintf(
                '      <warning>no sender for this key</warning>; uses %s',
                implode(', ', $used)
            ));

            return;
        }

        $missing = array_diff($used, $this->supplied[$row->template_key]);
        if ($missing) {
            $this->out(sprintf(
                '      <warning>arrives literally: %s</warning>',
                implode(', ', array_map(function ($n) {
                    return '{{' . $n . '}}';
                }, $missing))
            ));
        }
    }

    /**
     * @param string $text Text to shorten.
     * @param int $length Maximum length.
     * @return string
     */
    protected function shorten($text, $length)
    {
        $text = (string)$text;

        return mb_strlen($text) > $length ? mb_substr($text, 0, $length - 1) . '…' : $text;
    }
}
