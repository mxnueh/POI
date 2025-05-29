<?php
// Configure secure session settings
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_samesite', 'Strict');

session_start();

// Session timeout (30 minutes)
$timeout_duration = 1800;

// Check if user is logged in
function check_auth() {
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        header('Location: login.php');
        exit();
    }
    
    // Check for session timeout
    global $timeout_duration;
    if (isset($_SESSION['last_activity']) && 
        (time() - $_SESSION['last_activity']) > $timeout_duration) {
        
        // Session expired
        session_unset();
        session_destroy();
        header('Location: login.php?timeout=1');
        exit();
    }
    
    // Update last activity time
    $_SESSION['last_activity'] = time();
    
    // Regenerate session ID periodically (every 15 minutes)
    if (!isset($_SESSION['last_regeneration'])) {
        $_SESSION['last_regeneration'] = time();
    } elseif (time() - $_SESSION['last_regeneration'] > 900) {
        session_regenerate_id(true);
        $_SESSION['last_regeneration'] = time();
    }
}

// Navigation menu
function get_navigation_menu($current_page = '') {
    $menu_items = [
        'dashboard.php' => 'Dashboard',
        'profile.php' => 'Mi Perfil',
        'products.php' => 'Productos',
        'reports.php' => 'Reportes',
        'settings.php' => 'Configuración'
    ];
    
    $html = '<nav class="navigation">';
    foreach ($menu_items as $page => $title) {
        $active = ($current_page === $page) ? ' class="active"' : '';
        $html .= "<a href=\"$page\"$active>$title</a>";
    }
    $html .= '<a href="logout.php" class="logout">Cerrar Sesión</a>';
    $html .= '</nav>';
    
    return $html;
}

// Common HTML header
function get_html_header($title = 'Dashboard', $current_page = '') {
    $navigation = get_navigation_menu($current_page);
    return "
<!DOCTYPE html>
<html>
<head>
    <title>$title</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; }
        .header { background: #333; color: white; padding: 10px 0; }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }
        .user-info { float: right; }
        .navigation { background: #f4f4f4; padding: 10px 0; border-bottom: 1px solid #ddd; }
        .navigation a { 
            display: inline-block; 
            padding: 10px 15px; 
            text-decoration: none; 
            color: #333; 
            margin-right: 10px;
            border-radius: 4px;
        }
        .navigation a:hover { background: #ddd; }
        .navigation a.active { background: #007cba; color: white; }
        .navigation a.logout { background: #dc3545; color: white; float: right; }
        .navigation a.logout:hover { background: #c82333; }
        .content { padding: 20px 0; }
        .card { 
            background: white; 
            border: 1px solid #ddd; 
            border-radius: 4px; 
            padding: 20px; 
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .info { background: #e7f3ff; }
        .success { background: #d4edda; border-color: #c3e6cb; color: #155724; }
        .warning { background: #fff3cd; border-color: #ffeaa7; color: #856404; }
        h1, h2, h3 { margin-top: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; }
    </style>
</head>
<body>
    <div class='header'>
        <div class='container'>
            <h1 style='display: inline-block; margin: 0;'>Sistema Seguro</h1>
            <div class='user-info'>
                Bienvenido, " . htmlspecialchars($_SESSION['username']) . "
            </div>
        </div>
    </div>
    $navigation
    <div class='container'>
        <div class='content'>
    ";
}

// Common HTML footer
function get_html_footer() {
    return "
        </div>
    </div>
</body>
</html>";
}
?>