<?php
/**
 * What may be written into the web root, and by whom.
 *
 * uploadFile() and uploadImage() took the extension straight off the name the
 * browser sent and moved the file into webroot/files/uploads/ or
 * webroot/img/uploads/. Nothing checked it, at any of their three hundred and
 * thirty call sites, and the server hands an existing file straight to PHP - so
 * an uploaded .php was not served, it was run. Two more doors led into the same
 * room: both base64 croppers took the word after data:image/ as the extension.
 */
require __DIR__ . '/lib/harness.php';

/**
 * A controller with just enough around it to answer the question.
 *
 * Flash and Auth are stood in for because the guard reports a refusal through
 * them, and building the real ones needs a request, a session and a database.
 */
class UploadGuardProbe extends App\Controller\AppController
{
    public $logged = [];

    public function __construct()
    {
        $this->request = new Cake\Http\ServerRequest(['url' => '/candidates/add',
            'params' => ['plugin' => null, 'controller' => 'Candidates',
                'action' => 'add', 'pass' => [], '_ext' => null]]);
        $this->Flash = new UploadGuardFlash();
        $this->Auth = new UploadGuardAuth();
    }

    public function log($message, $level = 'error', $scope = [])
    {
        $this->logged[] = $level . ': ' . $message;

        return true;
    }

    /**
     * @param string $extension The extension to test.
     * @param bool $imagesOnly Whether this is an image field.
     * @return bool
     */
    public function allows($extension, $imagesOnly = false)
    {
        $method = new ReflectionMethod('App\Controller\AppController', 'uploadExtensionAllowed');
        $method->setAccessible(true);

        return $method->invoke($this, $extension, $imagesOnly);
    }
}

class UploadGuardFlash
{
    public $messages = [];

    public function error($message)
    {
        $this->messages[] = $message;
    }
}

class UploadGuardAuth
{
    public function user($key = null)
    {
        return null;
    }
}

$probe = new UploadGuardProbe();

echo "  what must never get in\n";
foreach (['php', 'PHP', 'phtml', 'php5', 'php7', 'phps', 'phar', 'pht',
        'htaccess', 'cgi', 'pl', 'py', 'sh', 'exe', 'so'] as $bad) {
    check(sprintf('.%s is refused', $bad), $probe->allows($bad), false);
}
check('a double extension is judged by its last part', $probe->allows('jpg.php'), false);
check('a file with no extension is refused', $probe->allows(''), false);
checkTrue('a refusal is written down', count($probe->logged) > 0);
checkTrue('and says what was refused', strpos(implode(' ', $probe->logged), 'php') !== false);
checkTrue('and the person is told', count($probe->Flash->messages) > 0);

echo "  what belongs in\n";
foreach (['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'] as $image) {
    check(sprintf('.%s passes either kind of field', $image),
        [$probe->allows($image), $probe->allows($image, true)], [true, true]);
}
foreach (['pdf', 'doc', 'docx', 'xls', 'xlsx', 'csv', 'zip'] as $document) {
    check(sprintf('.%s is a document, so a file field takes it', $document),
        $probe->allows($document), true);
    check(sprintf('.%s is not a picture, so an image field does not', $document),
        $probe->allows($document, true), false);
}
check('an extension with its dot still reads the same', $probe->allows('.pdf'), true);
check('and case does not matter', $probe->allows('JPG'), true);

echo "  every place a file is written\n";
$source = file_get_contents(TMM_ROOT . '/src/Controller/AppController.php');
$writes = preg_match_all('/^\s*(?:if \()?(?:\$file->moveTo\(|move_uploaded_file\('
    . '|file_put_contents\(\$absolutePath|file_put_contents\(\$uploadPath)/m', $source);
$guards = preg_match_all('/\$this->uploadExtensionAllowed\(/', $source);
// handleFileUploads() writes twice in one branch behind a single guard, so
// five guards cover six writes.
checkTrue(sprintf('has a guard before it (%d writes, %d guards)', $writes, $guards),
    $guards >= $writes - 1);
check('and the two base64 croppers are among them',
    substr_count($source, 'uploadExtensionAllowed($extension, true)')
    + substr_count($source, 'uploadExtensionAllowed($imageType, true)'), 2);

echo "  the second lock on the disk\n";
foreach (['webroot/files/.htaccess', 'webroot/img/uploads/.htaccess'] as $rule) {
    $path = TMM_ROOT . '/' . $rule;
    checkTrue($rule . ' is there', is_file($path));
    $text = is_file($path) ? file_get_contents($path) : '';
    checkTrue('  turns the PHP engine off', strpos($text, 'php_flag engine off') !== false);
    checkTrue('  and denies the handlers outright',
        strpos($text, 'Require all denied') !== false);
    // The server this runs on is nginx, which never reads these. Saying so in
    // the file stops the next reader taking it for protection in force.
    checkTrue('  and says it does nothing under nginx',
        strpos($text, 'nginx does not read .htaccess') !== false);
}

$conf = TMM_ROOT . '/nginx_configs/tmm_no_exec_uploads.conf';
checkTrue('there is an nginx rule to go with them', is_file($conf));
$text = is_file($conf) ? file_get_contents($conf) : '';
checkTrue('  it denies every script but the front controller',
    strpos($text, 'location ~ ^/(?!index\.php$)') !== false);
checkTrue('  covers the handlers by name, not just php',
    strpos($text, 'phtml|php[3457]|phps|phar|pht') !== false);
checkTrue('  says which file it goes in',
    strpos($text, 'tmm.asahifamily.co.conf') !== false);
checkTrue('  and where in it',
    strpos($text, 'ABOVE its `location ~ \.php$`') !== false);
// The strict rule is only right while index.php really is the only one.
check('the web root still holds exactly one PHP file',
    array_map('basename', array_merge(glob(TMM_ROOT . '/webroot/*.php'),
        glob(TMM_ROOT . '/webroot/*/*.php'))), ['index.php']);

echo "  and nothing is public that should not be\n";
$candidates = file_get_contents(TMM_ROOT . '/src/Controller/CandidatesController.php');
checkTrue('the candidate wizard is not on the allow list',
    preg_match('/Auth->allow\(\[[^\]]*wizard/', $candidates) === 0);
checkTrue('and it is granted to whoever may add a candidate',
    strpos($candidates, "return \$this->hasPermission('Candidates', 'add');") !== false);
checkTrue('the wizard photo name cannot walk out of its directory',
    strpos($candidates, "preg_replace('/[^A-Za-z0-9_-]/', '', (string)\$identityNumber)") !== false);

finish();
