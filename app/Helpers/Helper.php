<?php

namespace App\Helpers;

use App\Models\RegisterUser;
use App\Models\Teacher;
use App\Models\Student;
use App\Helpers\Access;
use App\Helpers\Access\AccessType;
use App\Helpers\Abilities;
use Laravel\Sanctum\PersonalAccessToken;

class Helper
{
    // public static function userIsEligibleForResource($userToken, Access $access)
    // {
    //     if (!Helper::checkIfTokenExists($userToken)) {
    //         return TokenAuthResult::TokenNotFound;
    //     }
    //     if ($access->is_allUsersAccess()) return true;
    //     $user = RegisterUser::where('api_token', $userToken)->first();
    //     if (Helper::userIsAdmin($user) && $access->is_adminAccess()) return true;
    //     if (Helper::userIsTeacher($user) && $access->is_teacherAccess()) return true;
    //     if (Helper::userIsStudent($user) && $access->is_studentAccess()) return true;
    //     return false;
    // }
    // private function checkIfTokenExists($token)
    // {
    //     if (RegisterUser::where('api_token', $token)->exists()) {
    //         return true;
    //     }
    //     return false;
    // }
    // public static function getAdminAbilities
    // $adminAbilities=[
    //             Abilities::USER_CRUD,
    //             Abilities::SCHOOL_CLASS_CRUD,
    //             Abilities::SUBJECT_CRUD,
    //             Abilities::MARK_CRUD,
    //             Abilities::MARK_READ,
    //             Abilities::MARK_MODIFICATION_CRUD,
    //             Abilities::ACTIVITY_CRUD
    //         ];
    public static function checkIfUserIsAuthorized($request, $requiredAbility)
    {
        if (config('global.authorization_activated')) {
            $userIsEligible = Helper::isUserEligbleForResource($request->bearerToken(), $requiredAbility);
            if ($userIsEligible) {
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }
    public static function isUserEligbleForResource($token, $requiredAbility)
    {

        // $tokeny = PersonalAccessToken::all();
        $tokenHashed = hash('sha256', $token);
        if (!PersonalAccessToken::where('token', $tokenHashed)->exists()) {
            return false;
        }

        // echo $tokenHashed;
        $tokenFromDB = PersonalAccessToken::where('token', $tokenHashed)->first();
        // $userAssignedToTokenId = $tokenFromDB->tokenable_id;
        $userAssignedToToken = RegisterUser::where('id', $tokenFromDB->tokenable_id)->first();
        $tokenAbilities = $tokenFromDB->abilities;
        if (in_array($requiredAbility, $tokenAbilities)) {
            // echo gettype($requiredAbility);
            // echo gettype($tokenAbilities[0]);
            // echo implode(",", gettype($tokenAbilities));
            return true;
        }
        return false;
    }
    public static function createAbilitiesList($user)
    {
        if (Helper::userIsAdmin($user)) {
            return [
                Abilities::USER_CRUD,
                Abilities::SCHOOL_CLASS_CRUD,
                Abilities::SUBJECT_CRUD,
                Abilities::MARK_CRUD,
                Abilities::MARK_READ,
                Abilities::MARK_MODIFICATION_CRUD,
                Abilities::ACTIVITY_CRUD
            ];
        } else if (Helper::userIsTeacher($user)) {
            return [
                Abilities::MARK_CRUD,
                Abilities::MARK_READ,
                Abilities::MARK_MODIFICATION_CRUD
            ];
        } else if (Helper::userIsStudent($user)) {
            return [
                Abilities::MARK_READ
            ];
        }
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
// abstract class TokenAuthResult
// {
//     const TokenNotFound = 0;
//     const UserNotAllowed = 1;
//     const UserIsAllowed = 2;
// }
