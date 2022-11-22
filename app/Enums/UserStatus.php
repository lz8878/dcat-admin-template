<?php

namespace App\Enums;

enum UserStatus: int
{
    case Inactivated = 0;
    case Active = 1;
}
