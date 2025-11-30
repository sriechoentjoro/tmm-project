<!DOCTYPE html>
<html>
<head>
    <title>Session Debug</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .debug-box { background: #f5f5f5; padding: 15px; margin: 10px 0; border-radius: 5px; }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
        pre { background: white; padding: 10px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1><i class="fa fa-bug"></i> TMM Session Debug</h1>
    
    <?php
    session_start();
    
    // Check if user is logged in
    $isLoggedIn = isset($_SESSION['Auth']['User']['id']);
    ?>
    
    <div class="debug-box <?= $isLoggedIn ? 'success' : 'error' ?>">
        <h2>Login Status: <?= $isLoggedIn ? '✓ LOGGED IN' : '✗ NOT LOGGED IN' ?></h2>
    </div>
    
    <?php if ($isLoggedIn): ?>
        <div class="debug-box">
            <h3>Session Data:</h3>
            <pre><?php print_r($_SESSION); ?></pre>
        </div>
        
        <div class="debug-box">
            <h3>User Info:</h3>
            <ul>
                <li><strong>User ID:</strong> <?= $_SESSION['Auth']['User']['id'] ?? 'N/A' ?></li>
                <li><strong>Username:</strong> <?= $_SESSION['Auth']['User']['username'] ?? 'N/A' ?></li>
                <li><strong>Full Name:</strong> <?= $_SESSION['Auth']['User']['full_name'] ?? 'N/A' ?></li>
                <li><strong>Roles:</strong> <?= isset($_SESSION['Auth']['User']['role_names']) ? implode(', ', $_SESSION['Auth']['User']['role_names']) : 'N/A' ?></li>
            </ul>
        </div>
        
        <div class="debug-box">
            <h3>Test Navbar Icons:</h3>
            <p>
                <i class="fa fa-language"></i> Language Icon |
                <i class="fa fa-user-circle"></i> User Icon |
                <i class="fa fa-sign-out"></i> Logout Icon
            </p>
            <p>If you can see these icons above, FontAwesome is working!</p>
        </div>
        
        <p><a href="/tmm/dashboard">← Back to Dashboard</a></p>
    <?php else: ?>
        <div class="debug-box error">
            <p>You are not logged in. Please <a href="/tmm/users/login">login here</a>.</p>
        </div>
        
        <div class="debug-box">
            <h3>Session Contents:</h3>
            <pre><?php print_r($_SESSION); ?></pre>
        </div>
    <?php endif; ?>
</body>
</html>
