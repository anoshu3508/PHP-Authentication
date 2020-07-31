<?php
// Include the composer autoload file
require '../vendor/autoload.php';

// Import the necessary classes
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Illuminate\Database\Capsule\Manager as Capsule;

// Setup a new Eloquent Capsule instance
$capsule = new Capsule();

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

$user = Sentinel::findById(2);

// ユーザをアクティベーション
$Activation = Sentinel::getActivationRepository();
$activationCode = $Activation->create($user)->code;
$Activation->complete($user, $activationCode);

// 一覧
/*
$userObj = Sentinel::getUserRepository();
$roleObj = Sentinel::getRoleRepository();
$persistenceObj = Sentinel::getPersistenceRepository();
$activationObj = Sentinel::getActivationRepository();
$reminderObj = Sentinel::getReminderRepository();
$throttleObj = Sentinel::getThrottleRepository();
*/