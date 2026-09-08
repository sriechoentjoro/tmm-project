<?php
namespace App\Mailer\Transport;

use Cake\Http\Client;
use Cake\Mailer\AbstractTransport;
use Cake\Mailer\Email;
use Cake\Network\Exception\SocketException;

/**
 * Sends mail through a provider's HTTPS API instead of SMTP.
 *
 * This exists because the host blocks outbound SMTP: ports 25, 465 and 587 all
 * time out, so no Gmail setting can help. Port 443 is open — the server pulls
 * from GitHub over it — so the mail leaves through an API on that port.
 *
 * Configure it in config/app_local.php, which is git-ignored, so the API key
 * never reaches this public repository:
 *
 *     'EmailTransport' => [
 *         'default' => [
 *             'className' => 'App\Mailer\Transport\HttpApiTransport',
 *             'service'   => 'brevo',      // brevo | resend | sendgrid | mailgun
 *             'apiKey'    => 'xkeysib-...',
 *             //'domain'  => 'example.com',   // mailgun only
 *             //'region'  => 'eu',            // mailgun only, for the EU endpoint
 *         ],
 *     ],
 *
 * Nothing else changes: EmailServiceComponent keeps calling Email::send(), and
 * CakePHP renders the same templates and layout before handing the result here.
 */
class HttpApiTransport extends AbstractTransport
{
    /**
     * @var array
     */
    protected $_defaultConfig = [
        'service' => 'brevo',
        'apiKey' => null,
        'domain' => null,
        'region' => 'us',
        'timeout' => 30,
    ];

    /**
     * @var \Cake\Http\Client|null Injected in tests; built on demand otherwise.
     */
    protected $client;

    /**
     * @param \Cake\Http\Client|null $client Replaces the HTTP client, for tests.
     * @return void
     */
    public function setClient(Client $client = null)
    {
        $this->client = $client;
    }

    /**
     * @param \Cake\Mailer\Email $email Email instance, already rendered.
     * @return array The request that was sent, for logging and tests.
     * @throws \Cake\Network\Exception\SocketException When the API rejects it.
     */
    public function send(Email $email)
    {
        $service = strtolower((string)$this->getConfig('service'));
        $apiKey = $this->getConfig('apiKey');

        if (empty($apiKey)) {
            throw new SocketException('No apiKey configured for the ' . $service . ' mail transport.');
        }

        $request = $this->buildRequest($email, $service, $apiKey);

        $client = $this->client ?: new Client(['timeout' => (int)$this->getConfig('timeout')]);
        if ($request['type'] === 'json') {
            $response = $client->post($request['url'], json_encode($request['data']), [
                'headers' => $request['headers'] + ['Content-Type' => 'application/json'],
            ]);
        } else {
            $response = $client->post($request['url'], $request['data'], [
                'headers' => $request['headers'],
            ]);
        }

        if (!$response->isOk()) {
            // The body carries the provider's reason — a rejected sender, an
            // unverified domain, a spent quota — which is what makes a failure
            // diagnosable from email_logs.
            throw new SocketException(sprintf(
                '%s API returned HTTP %d: %s',
                $service,
                $response->getStatusCode(),
                trim(substr((string)$response->getStringBody(), 0, 500))
            ));
        }

        return [
            'headers' => $this->_headersToString($email->getHeaders(['from', 'to', 'subject'])),
            'message' => (string)$email->message(Email::MESSAGE_HTML),
        ];
    }

    /**
     * Build the provider-specific request.
     *
     * Kept separate from send() so the payload can be asserted without a
     * network call.
     *
     * @param \Cake\Mailer\Email $email Rendered email.
     * @param string $service Provider name.
     * @param string $apiKey API key.
     * @return array url, headers, data and type (json or form).
     * @throws \Cake\Network\Exception\SocketException On an unknown service.
     */
    public function buildRequest(Email $email, $service, $apiKey)
    {
        $from = $email->getFrom();
        $fromEmail = (string)key($from);
        $fromName = (string)current($from);
        if ($fromName === '' || $fromName === $fromEmail) {
            $fromName = $fromEmail;
        }

        $subject = (string)$email->getSubject();
        $html = (string)$email->message(Email::MESSAGE_HTML);
        $text = (string)$email->message(Email::MESSAGE_TEXT);

        switch ($service) {
            case 'brevo':
                $to = [];
                foreach ($email->getTo() as $address => $name) {
                    $to[] = ['email' => $address, 'name' => $name ?: $address];
                }
                $data = [
                    'sender' => ['email' => $fromEmail, 'name' => $fromName],
                    'to' => $to,
                    'subject' => $subject,
                ];
                if ($html !== '') {
                    $data['htmlContent'] = $html;
                }
                if ($text !== '') {
                    $data['textContent'] = $text;
                }

                return [
                    'url' => 'https://api.brevo.com/v3/smtp/email',
                    'headers' => ['api-key' => $apiKey, 'Accept' => 'application/json'],
                    'data' => $data,
                    'type' => 'json',
                ];

            case 'resend':
                $to = [];
                foreach ($email->getTo() as $address => $name) {
                    $to[] = ($name && $name !== $address) ? sprintf('%s <%s>', $name, $address) : $address;
                }
                $data = [
                    'from' => $fromName === $fromEmail ? $fromEmail : sprintf('%s <%s>', $fromName, $fromEmail),
                    'to' => $to,
                    'subject' => $subject,
                ];
                if ($html !== '') {
                    $data['html'] = $html;
                }
                if ($text !== '') {
                    $data['text'] = $text;
                }

                return [
                    'url' => 'https://api.resend.com/emails',
                    'headers' => ['Authorization' => 'Bearer ' . $apiKey],
                    'data' => $data,
                    'type' => 'json',
                ];

            case 'sendgrid':
                $to = [];
                foreach ($email->getTo() as $address => $name) {
                    $to[] = ['email' => $address, 'name' => $name ?: $address];
                }
                $content = [];
                if ($text !== '') {
                    $content[] = ['type' => 'text/plain', 'value' => $text];
                }
                if ($html !== '') {
                    // SendGrid requires text/plain before text/html.
                    $content[] = ['type' => 'text/html', 'value' => $html];
                }

                return [
                    'url' => 'https://api.sendgrid.com/v3/mail/send',
                    'headers' => ['Authorization' => 'Bearer ' . $apiKey],
                    'data' => [
                        'personalizations' => [['to' => $to]],
                        'from' => ['email' => $fromEmail, 'name' => $fromName],
                        'subject' => $subject,
                        'content' => $content,
                    ],
                    'type' => 'json',
                ];

            case 'mailgun':
                $domain = $this->getConfig('domain');
                if (empty($domain)) {
                    throw new SocketException('The mailgun transport needs a "domain" in its configuration.');
                }
                $to = [];
                foreach ($email->getTo() as $address => $name) {
                    $to[] = ($name && $name !== $address) ? sprintf('%s <%s>', $name, $address) : $address;
                }
                $data = [
                    'from' => sprintf('%s <%s>', $fromName, $fromEmail),
                    'to' => implode(', ', $to),
                    'subject' => $subject,
                ];
                if ($html !== '') {
                    $data['html'] = $html;
                }
                if ($text !== '') {
                    $data['text'] = $text;
                }
                $host = $this->getConfig('region') === 'eu' ? 'api.eu.mailgun.net' : 'api.mailgun.net';

                return [
                    'url' => sprintf('https://%s/v3/%s/messages', $host, $domain),
                    'headers' => ['Authorization' => 'Basic ' . base64_encode('api:' . $apiKey)],
                    'data' => $data,
                    'type' => 'form',
                ];
        }

        throw new SocketException(sprintf(
            'Unknown mail service "%s". Use brevo, resend, sendgrid or mailgun.',
            $service
        ));
    }
}
