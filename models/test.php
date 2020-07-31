<?php
// Import the necessary classes
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Illuminate\Database\Capsule\Manager as Capsule;

// Setup a new Eloquent Capsule instance
$capsule = new Capsule;

$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => 'localhost',
    'database'  => 'activezero_co_jp',
    'username'  => 'activezero',
    'password'  => 'Activezero1101',
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
]);

$capsule->bootEloquent();

$credentials = [
    'email' => 'kakinuma@activezero.co.jp',
    'password' => '3r99j2cx'
];

// 登録済みかを確認
$user = Sentinel::getUserRepository()->findByCredentials($credentials);
if (is_null($user)) {
    // 存在しない場合は、新規登録
    $user = Sentinel::register($credentials);
    print_r($user);
} else {
    // 存在する場合は削除
    $user->delete();
    echo "user deleted".PHP_EOL;
}

// 一覧
$userObj = Sentinel::getUserRepository();
$roleObj = Sentinel::getRoleRepository();
$persistenceObj = Sentinel::getPersistenceRepository();
$activationObj = Sentinel::getActivationRepository();
$reminderObj = Sentinel::getReminderRepository();
$throttleObj = Sentinel::getThrottleRepository();