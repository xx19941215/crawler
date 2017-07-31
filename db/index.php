<?php
// If you installed via composer, just use this code to requrie autoloader on the top of your projects.
require 'vendor/autoload.php';

// Using Medoo namespace
use Medoo\Medoo;

// Initialize
$database = new Medoo([
'database_type' => 'mysql',
'database_name' => 'zhihu',
'server' => '127.0.0.1',
'username' => 'root',
'password' => '123456789'
]);

// Enjoy

$data = $database->select('zhihu_user', [
'name'
], [
'name[~]' => 'n'
]);

echo json_encode($data);

// [
//     {
//         "user_name" : "foo",
//         "email" : "foo@bar.com"
//     }
// ]