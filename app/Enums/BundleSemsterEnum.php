<?php

namespace App\Enums;

enum BundleSemsterEnum: string
{
    case FIRST = 'First Semester';
    case SECOND = 'second Semester';
    case THIRD = 'third Semester';
    case FOURTH = 'fourth Semester';


    public function get()
    {
        return self::cases();
    }
}
