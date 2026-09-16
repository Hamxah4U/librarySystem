<?php
  
  session_start();

  $uri = parse_url($_SERVER['REQUEST_URI'])['path'];

  // Remove '/index.php' prefix if present in the path
  $uri = str_replace('/index.php', '', $uri);
  if ($uri === '') {
      $uri = '/';
  }

  // Routes allowed without login
  $publicRoutes = ['/', '/login', '/resetpassword'];

  if (!isset($_SESSION['user_id']) && !in_array($uri, $publicRoutes)) {
      header('Location: /');
      exit();
  }

  $routes = [
      '/' => 'controllers/index.php',
      '/student-dashboard' => 'controllers/student/dashboard.php',
      '/student-catalog'   => 'controllers/student/catalog.php',
      '/student-loans'     => 'controllers/student/loan.php',
      '/student-holds'     => 'controllers/student/hold.php',
      '/student-fines'     => 'controllers/student/fine.php',

      // Staff routes
      '/staff-dashboard'   => 'controllers/staff/dashboar.php',
      '/staff-issue-book' => 'controllers/staff/issue-book.php',
      '/staff-add-book'   => 'controllers/staff/add-book.php',
      '/staff-circulation' => 'controllers/staff/circulation.php',
      '/staff-return-book' => 'controllers/staff/return-book.php',
      '/staff-verify-payment' => 'controllers/staff/verify-payment.php',
      '/staff-inventory' => 'controllers/staff/inventory.php',
      '/staff-update-book' => 'controllers/staff/update-book.php',
  ];

  if (array_key_exists($uri, $routes)) {
      require $routes[$uri];
  } else {
      require 'controllers/404.php';
  }









/* session_start();
$uri = parse_url($_SERVER['REQUEST_URI'])['path'];

if(!isset($_SESSION['userID']) && 
  $uri != '/' && 
  $uri != '/login' && 
  $uri != '/resetpassword' && 
  $uri != '/currentcapital' && 
  $uri != '/view-store' &&
  $uri != '/chart' && 
  $uri != '/diary' &&
  $uri != '/wallet' &&
  $uri != '/sales2' &&
  $uri != '/inventory-reconciliation' &&
  $uri != '/wholesale'
  
  ) {
  header('Location: /');
  exit();
}

$routes = [
  '/' => 'controllers/index.php',

  '/student-dashboard' => 'controllers/student/dashboard.php',
  '/student-catalog' => 'controllers/student/catalog.php',
  '/student-loans' => 'controllers/student/loan.php',
  '/student-holds' => 'controllers/student/hold.php',
  '/student-fines' => 'controllers/student/fine.php',
];

if(array_key_exists($uri, $routes)) {
  require $routes[$uri];
}else{
  require 'controllers/404.php';
} */
