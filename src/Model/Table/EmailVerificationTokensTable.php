<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\I18n\Time;
use Cake\Datasource\ConnectionManager;
use Cake\Log\Log;

/**
 * EmailVerificationTokens Model
 *
 * Table for managing email verification and password reset tokens
 * Used in Phase 3-4: LPK Registration Wizard
 *
 * @property \Cake\ORM\Association\BelongsTo $Users
 */
class EmailVerificationTokensTable extends Table
{
    /**
     * Why the last token could not be written, or null.
     *
     * generateToken() returns false for a refused save and for a database that
     * throws, and the two need different answers from whoever called it: one is
     * worth trying again, the other never will be. The resend button told
     * everybody to "try again" while MySQL was refusing a token_type its ENUM
     * had never been given, which is a loop with no way out of it.
     *
     * @var string|null
     */
    protected $lastError = null;

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('email_verification_tokens');
        $this->setDisplayField('token');
        $this->setPrimaryKey('id');

        // Use authentication database connection
        $this->setConnection(ConnectionManager::get('cms_authentication_authorization'));
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->email('user_email')
            ->requirePresence('user_email', 'create')
            ->notEmptyString('user_email')
            ->maxLength('user_email', 100);

        $validator
            ->scalar('token')
            ->requirePresence('token', 'create')
            ->notEmptyString('token')
            ->maxLength('token', 64)
            ->add('token', 'exactLength', [
                'rule' => function ($value, $context) {
                    return strlen($value) === 64;
                },
                'message' => 'Token must be exactly 64 characters'
            ]);

        $validator
            ->scalar('token_type')
            ->requirePresence('token_type', 'create')
            ->notEmptyString('token_type')
            // user_verification is a login account confirming its own address,
            // which is not the same thing as email_verification - that one
            // belongs to an institution's registration. Both are keyed on an
            // email address and nothing else, and for an LPK the two are the
            // same address, so they are kept apart by type: resending one must
            // not spend the other's live token.
            ->inList('token_type',
                ['email_verification', 'user_verification', 'password_reset'],
                'Invalid token type');

        $validator
            ->boolean('is_used')
            ->notEmptyString('is_used');

        $validator
            ->dateTime('used_at')
            ->allowEmptyDateTime('used_at');

        $validator
            ->dateTime('expires_at')
            ->requirePresence('expires_at', 'create')
            ->notEmptyDateTime('expires_at');

        $validator
            ->dateTime('created')
            ->requirePresence('created', 'create')
            ->notEmptyDateTime('created');

        return $validator;
    }

    /**
     * Generate a new verification token
     *
     * Creates a cryptographically secure 64-character token with 24-hour expiry
     *
     * @param string $email Email address for verification
     * @param string $tokenType Type of token (email_verification or password_reset)
     * @return string|false Token string on success, false on failure
     */
    public function generateToken($email, $tokenType = 'email_verification')
    {
        try {
            // Generate 64-character cryptographically secure token
            $token = bin2hex(random_bytes(32));
            
            // Set expiry (24 hours from now)
            $expiresAt = new Time('+24 hours');
            $created = new Time();
            
            // Create token record
            $tokenRecord = $this->newEntity([
                'user_email' => $email,
                'token' => $token,
                'token_type' => $tokenType,
                'is_used' => 0,
                'used_at' => null,
                'expires_at' => $expiresAt,
                'created' => $created
            ]);
            
            if ($this->save($tokenRecord)) {
                $this->lastError = null;
                Log::info("Token generated for email: $email, type: $tokenType", ['scope' => 'email_verification']);
                return $token;
            }

            $this->lastError = $this->describeErrors($tokenRecord->getErrors())
                ?: 'the record was refused without saying why';
            Log::error("Failed to save token for email: $email ({$this->lastError})", ['scope' => 'email_verification']);
            return false;

        } catch (\Exception $e) {
            $this->lastError = $e->getMessage();
            Log::error("Error generating token: " . $e->getMessage(), ['scope' => 'email_verification']);
            return false;
        }
    }

    /**
     * Why the last token could not be written, or null if the last one was.
     *
     * @return string|null
     */
    public function lastError()
    {
        return $this->lastError;
    }

    /**
     * Flatten a set of validation errors into one readable line.
     *
     * @param array $errors As getErrors() returns them.
     * @return string Empty when there were none.
     */
    protected function describeErrors(array $errors)
    {
        $said = [];
        foreach ($errors as $field => $rules) {
            foreach ((array)$rules as $message) {
                $said[] = $field . ': ' . (is_array($message)
                    ? implode(', ', $message) : $message);
            }
        }

        return implode('; ', $said);
    }

    /**
     * Validate a token
     *
     * Checks if token exists, is not used, and has not expired
     *
     * @param string $token Token to validate
     * @param string $tokenType Type of token (optional, for additional validation)
     * @return \Cake\Datasource\EntityInterface|false Token record on success, false if invalid
     */
    public function validateToken($token, $tokenType = null)
    {
        if (!$token || strlen($token) !== 64) {
            Log::warning("Invalid token format", ['scope' => 'email_verification']);
            return false;
        }
        
        // Find token
        $query = $this->find()
            ->where([
                'token' => $token,
                'is_used' => 0
            ]);
        
        // Add token type filter if specified
        if ($tokenType) {
            $query->where(['token_type' => $tokenType]);
        }
        
        $tokenRecord = $query->first();
        
        if (!$tokenRecord) {
            Log::warning("Token not found or already used: $token", ['scope' => 'email_verification']);
            return false;
        }
        
        // Check expiry.
        //
        // expires_at is read rather than trusted. The ORM hands back a time
        // object where the column is a real DATETIME and a plain string where
        // it is not - these tables were built by hand-written SQL - and
        // new Time() on text that is not a date throws, which here would be a
        // fatal in the middle of somebody verifying their address. A token
        // whose expiry cannot be read is treated as expired: that refuses a
        // link that might have been good, which is recoverable by resending,
        // where the other way round accepts a link that may have been anyone's.
        $now = new Time();
        try {
            $expiresAt = $tokenRecord->expires_at instanceof \DateTimeInterface
                ? new Time($tokenRecord->expires_at)
                : new Time(trim((string)$tokenRecord->expires_at));
        } catch (\Exception $e) {
            Log::warning(sprintf('Token %s has an expiry that cannot be read (%s), '
                . 'so it is treated as expired', $token,
                json_encode($tokenRecord->expires_at)), ['scope' => 'email_verification']);

            return false;
        }

        if ($now->greaterThan($expiresAt)) {
            Log::warning("Token expired: $token", ['scope' => 'email_verification']);
            return false;
        }
        
        Log::info("Token validated successfully: $token", ['scope' => 'email_verification']);
        return $tokenRecord;
    }

    /**
     * Mark token as used
     *
     * Updates token record to prevent reuse
     *
     * @param string $token Token to mark as used
     * @return bool Success status
     */
    public function markAsUsed($token)
    {
        try {
            $tokenRecord = $this->find()
                ->where(['token' => $token])
                ->first();
            
            if (!$tokenRecord) {
                Log::warning("Cannot mark non-existent token as used: $token", ['scope' => 'email_verification']);
                return false;
            }
            
            $tokenRecord->is_used = 1;
            $tokenRecord->used_at = new Time();
            
            if ($this->save($tokenRecord)) {
                Log::info("Token marked as used: $token", ['scope' => 'email_verification']);
                return true;
            }
            
            Log::error("Failed to mark token as used: $token", ['scope' => 'email_verification']);
            return false;
            
        } catch (\Exception $e) {
            Log::error("Error marking token as used: " . $e->getMessage(), ['scope' => 'email_verification']);
            return false;
        }
    }

    /**
     * Clean up expired tokens
     *
     * Deletes tokens that expired more than 48 hours ago
     * Should be run as a cron job or on user login
     *
     * @return int Number of tokens deleted
     */
    public function cleanupExpired()
    {
        try {
            $cutoffDate = new Time('-48 hours');
            
            $deletedCount = $this->deleteAll([
                'expires_at <' => $cutoffDate
            ]);
            
            if ($deletedCount > 0) {
                Log::info("Cleaned up $deletedCount expired tokens", ['scope' => 'email_verification']);
            }
            
            return $deletedCount;
            
        } catch (\Exception $e) {
            Log::error("Error cleaning up expired tokens: " . $e->getMessage(), ['scope' => 'email_verification']);
            return 0;
        }
    }

    /**
     * Get token statistics
     *
     * Returns counts of tokens by type and status
     *
     * @return array Statistics array
     */
    public function getTokenStats()
    {
        $total = $this->find()->count();
        $used = $this->find()->where(['is_used' => 1])->count();
        $expired = $this->find()->where(['expires_at <' => new Time()])->count();
        $active = $this->find()
            ->where([
                'is_used' => 0,
                'expires_at >=' => new Time()
            ])
            ->count();
        
        $byType = $this->find()
            ->select(['token_type', 'count' => $this->find()->func()->count('*')])
            ->group('token_type')
            ->toArray();
        
        return [
            'total' => $total,
            'used' => $used,
            'expired' => $expired,
            'active' => $active,
            'by_type' => $byType
        ];
    }

    /**
     * Resend verification email
     *
     * Invalidates old tokens and generates new one
     *
     * @param string $email Email address
     * @return string|false New token on success, false on failure
     */
    public function resendVerification($email, $tokenType = 'email_verification')
    {
        try {
            // Invalidate all existing tokens for this email, of this kind only.
            //
            // The kind matters: an institution's verification and a login
            // account's are both keyed on an email address and nothing else, so
            // where the two share one - which is the ordinary case for an LPK -
            // resending either would otherwise spend the other's live token and
            // break a link somebody is about to click.
            $this->updateAll(
                ['is_used' => 1, 'used_at' => new Time()],
                [
                    'user_email' => $email,
                    'token_type' => $tokenType,
                    'is_used' => 0
                ]
            );
            
            // Generate new token
            $newToken = $this->generateToken($email, $tokenType);
            
            if ($newToken) {
                Log::info("Verification email resent for: $email", ['scope' => 'email_verification']);
                return $newToken;
            }

            // lastError is generateToken's and is left as it set it.
            return false;

        } catch (\Exception $e) {
            $this->lastError = $e->getMessage();
            Log::error("Error resending verification: " . $e->getMessage(), ['scope' => 'email_verification']);
            return false;
        }
    }
}
