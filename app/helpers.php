<?php

declare(strict_types=1);

use App\Exceptions\UserNotFoundException;
use App\Models\User;


function getUser(): User
{
    $user = auth()->user();

    if (! $user) {
        throw new UserNotFoundException();
    }

    return $user;
}

function productImagePath($filename)
{
    $defaultImagePath = asset('assets/images/auth-bg.jpg');
    if (!empty($filename)) {
        return asset('storage/product/' . $filename);
    }
    return $defaultImagePath;
}

function categoryImagePath($filename)
{
    $defaultImagePath = asset('assets/images/auth-bg.jpg');
    if (!empty($filename)) {
        return asset('storage/category/' . $filename);
    }
    return $defaultImagePath;
}

function chatImagePath($filename)
{
    $defaultImagePath = asset('assets/images/auth-bg.jpg');
    if (!empty($filename)) {
        return asset('storage/message/' . $filename);
    }
    return $defaultImagePath;
}

function adminChatImagePath($file)
{
    $defaultImagePath = null;
    if (!empty($file)) {
        return asset('storage/admin_message/' . $file);
    }
    return $defaultImagePath;
}

function notificationImagePath($filename)
{
    $defaultImagePath = asset('assets/images/auth-bg.jpg');
    if (!empty($filename)) {
        return asset('storage/notification/' . $filename);
    }
    return $defaultImagePath;
}

function vendorImagePath($filename)
{
    $defaultImagePath = null;
    if (!empty($filename)) {
        return asset('storage/vendor/' . $filename);
    }
    return $defaultImagePath;
}
