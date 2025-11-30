-- Setup Test Credentials for TMM System
-- Run this script to set standard passwords for test users
-- Passwords generated using CakePHP DefaultPasswordHasher (bcrypt)

USE cms_authentication_authorization;

-- Admin (password: admin123)
UPDATE users SET password = '$2y$10$cka06dLU8frl88JDTPaHZug1RT9KiB9N54hxRnPvvUssJL1mSXJoG' 
WHERE username = 'admin';

-- Director (password: director123)
UPDATE users SET password = '$2y$10$buQsp/nT5dvSN3Ny.oRpyedb9IQqMyVeXw/noqdKyQ19ImhnJmcQG' 
WHERE username = 'director';

-- Manager (password: manager123)
UPDATE users SET password = '$2y$10$qP6/ppj7dQn0q2W5CE9Tzet3MAjkK6M2vQ6fwwh7YW7r9904q7yAa' 
WHERE username = 'manager';

-- Recruitment Officers (password: recruit123)
UPDATE users SET password = '$2y$10$OnPpaO0PoLVGEhd0OU3yWOzIZRbza.7ViWiV9E.iFwpzZIIZj0.pO' 
WHERE username IN ('recruitment1', 'recruitment2');

-- Training Coordinators (password: training123)
UPDATE users SET password = '$2y$10$XMYIWrA23o9LLMzcmokHlO8Xwi1rKf9RSXeU1/mLN/C4yY/HotiwK' 
WHERE username IN ('training1', 'training2');

-- Documentation Officers (password: doc123)
UPDATE users SET password = '$2y$10$B37Hov2M1CrVlwWk1/meEumjIQDJVBh7QnjP8FEBHCWyBSsDtr/Iy' 
WHERE username IN ('documentation1', 'documentation2');

-- LPK Users (password: lpk123)
UPDATE users SET password = '$2y$10$buLzegmczTjrYKY7j.4bM.RNBZEwDyCZF8UhUEjrsewiBKafr9nJK' 
WHERE username IN ('lpk_semarang', 'lpk_makasar', 'lpk_medan', 'lpk_padang', 'lpk_bekasi');

-- Verify users are active
UPDATE users SET is_active = 1 
WHERE username IN (
    'admin', 'director', 'manager',
    'recruitment1', 'recruitment2',
    'training1', 'training2',
    'documentation1', 'documentation2',
    'lpk_semarang', 'lpk_makasar', 'lpk_medan', 'lpk_padang', 'lpk_bekasi'
);

-- Show updated users
SELECT 
    u.id,
    u.username,
    u.full_name,
    u.email,
    u.is_active,
    r.name as role_name,
    'Check login page for password' as password_info
FROM users u
LEFT JOIN user_roles ur ON u.id = ur.user_id
LEFT JOIN roles r ON ur.role_id = r.id
WHERE u.username IN (
    'admin', 'director', 'manager',
    'recruitment1', 'training1', 'documentation1',
    'lpk_semarang', 'lpk_bekasi'
)
ORDER BY u.id;

-- Password Reference (DO NOT store in production):
-- admin: admin123
-- director: director123
-- manager: manager123
-- recruitment1/2: recruit123
-- training1/2: training123
-- documentation1/2: doc123
-- lpk_*: lpk123
