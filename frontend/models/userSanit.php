<?php
function sanit($unSanitData)
{
    $username = trim($unSanitData['username'] ?? '');
    $name = trim($unSanitData['name'] ?? '');
    $email = trim($unSanitData['email'] ?? '');
    $dob = $unSanitData['dob'] ?? '';
    $password = $unSanitData['password'] ?? '';
    $password_confirm = $unSanitData['password_confirm'] ?? '';
    $seller = $unSanitData['seller-toggle'] ?? 0;

    return [
        'username' => $username,
        'name' => $name,
        'email' => $email,
        'dob' => $dob,
        'password' => $password,
        'password_confirm' => $password_confirm,
        'seller-toggle' => $seller
    ];
}
?>