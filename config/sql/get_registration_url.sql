-- Quick query to get the registration URL for testing

SELECT 
    '==============================================',
    'REGISTRATION WIZARD TEST URL',
    '==============================================';

SELECT 
    id,
    name,
    email,
    username,
    registration_token,
    DATE_FORMAT(token_expires_at, '%Y-%m-%d %H:%i:%s') as expires_at,
    is_registered,
    CONCAT('http://localhost/tmm/institution-registration/complete/', registration_token) as REGISTRATION_URL
FROM vocational_training_institutions
WHERE username = 'testwizard';

SELECT 
    '==============================================',
    'COPY THE URL ABOVE AND OPEN IN BROWSER',
    '==============================================';

SELECT 
    'Expected URL:',
    'http://localhost/tmm/institution-registration/complete/WIZARD_TEST_TOKEN_123' as example_url;
