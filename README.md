**SMC NSTP Management System**

smcnstp.social

Add this line of code if system in connect.php is hosted
session_set_cookie_params([
     'lifetime' => 0,
     'path' => '/',
     'domain' => 'smcnstp.social',
     'secure' => true,
     'httponly' => true,
     'samesite' => 'Lax',
]);
