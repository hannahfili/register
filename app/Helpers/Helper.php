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
        $tokenHashed = hash('sha256', $token);
        if (!PersonalAccessToken::where('token', $tokenHashed)->exists()) {
            return false;
        }
        $tokenFromDB = PersonalAccessToken::where('token', $tokenHashed)->first();
        $userAssignedToToken = RegisterUser::where('id', $tokenFromDB->tokenable_id)->first();
        $tokenAbilities = $tokenFromDB->abilities;
        if (in_array($requiredAbility, $tokenAbilities)) {
            return true;
        }
        return false;
    }
    public static function createAbilitiesList($user)
    {
        if (Helper::userIsAdmin($user)) {
            return [
                Abilities::USER_CRUD,
                Abilities::USER_READ,
                Abilities::SCHOOL_CLASS_CRUD,
                Abilities::SUBJECT_CRUD,
                Abilities::MARK_CRUD,
                Abilities::MARK_READ,
                Abilities::MARK_MODIFICATION_CRUD,
                Abilities::ACTIVITY_CRUD
            ];
        } else if (Helper::userIsTeacher($user)) {
            return [
                Abilities::USER_READ,
                Abilities::SCHOOL_CLASS_CRUD,
                Abilities::SUBJECT_CRUD,
                Abilities::MARK_CRUD,
                Abilities::MARK_READ,
                Abilities::MARK_MODIFICATION_CRUD,
                Abilities::ACTIVITY_CRUD
            ];
        } else if (Helper::userIsStudent($user)) {
            return [
                Abilities::MARK_READ,
                Abilities::SUBJECT_READ,
                Abilities::SUBJECT_CRUD,
                Abilities::USER_READ,
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
