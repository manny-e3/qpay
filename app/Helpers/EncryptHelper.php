<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Crypt;

class EncryptHelper
{
    public static function encrypt($data)
    {
        return Crypt::encryptString($data);
    }

    public static function decrypt($data)
    {
        return Crypt::decryptString($data);
    }
}
