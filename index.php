<?php 
$request = $_SERVER['REQUEST_URI'];

switch ($request) {
     case '/kwasu_demo/home':

        require __DIR__ . '/views/home.view.php';
        break;

     case '/kwasu_demo/login':
        require __DIR__ . '/views/login.view.php';
        break;
    
      case '/kwasu_demo/logout':
        require __DIR__ . '/model/logout.php';
        break;

     case '/kwasu_demo/register':
        require __DIR__ . '/views/register.view.php';
        break;

     case '/kwasu_demo/dashboard':
        require __DIR__ . '/views/dashboard.view.php';
        break;
     
     default:
        http_response_code(404);
        require __DIR__ . '/views/404.view.php';
    // case '/views/department':
      //  require __DIR__ . '/views/dep.php';
}
?>