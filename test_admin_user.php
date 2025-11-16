<?php
require_once 'app/bootstrap.php';

$user = \App\Models\User::findByUsername('admin_demo');
if ($user) {
    echo 'User found: ' . $user->username . ' (ID: ' . $user->id . ', Role: ' . ($user->role ?? 'none') . ', is_admin: ' . (isset($user->role) && $user->role === 'admin' ? 'true' : 'false') . ')' . PHP_EOL;
} else {
    echo 'User not found' . PHP_EOL;
}
?>
