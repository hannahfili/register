<?php

namespace App\Helpers;

use App\Models\RegisterUser;
use App\Models\Teacher;
use App\Models\Student;
use App\Helpers\Access;
use App\Helpers\Access\AccessType;

class Helper
{
    public static function userIsEligibleForResource($userToken, Access $access)
    {
        if (!Helper::checkIfTokenExists($userToken)) {
            return TokenAuthResult::TokenNotFound;
        }
        if ($access->is_allUsersAccess()) return true;
        $user = RegisterUser::where('api_token', $userToken)->first();
        if (Helper::userIsAdmin($user) && $access->is_adminAccess()) return true;
        if (Helper::userIsTeacher($user) && $access->is_teacherAccess()) return true;
        if (Helper::userIsStudent($user) && $access->is_studentAccess()) return true;
        return false;
    }
    private function checkIfTokenExists($token)
    {
        if (RegisterUser::where('api_token', $token)->exists()) {
            return true;
        }
        return false;
    }
    private function userIsAdmin($user)
    {
        if ($user->isAdmin) return true;
        return false;
    }
    private function userIsTeacher($user)
    {
        if (Teacher::where('user_id', $user->id)->exists()) {
            return true;
        }
        return false;
    }
    private function userIsStudent($user)
    {
        if (Student::where('user_id', $user->id)->exists()) {
            return true;
        }
        return false;
    }
}
abstract class TokenAuthResult
{
    const TokenNotFound = 0;
    const UserNotAllowed = 1;
    const UserIsAllowed = 2;
}