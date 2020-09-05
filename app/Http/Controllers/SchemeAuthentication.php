<?php

namespace App\Http\Controllers;

use App\User as User;
use App\SchemeAdmin as SchemeAdmin;
use App\SchemeUser as SchemeUser;

trait SchemeAuthentication
{
    /**
     * @param int $schemeID
     * @param User $user
     * @return bool True if the specified user is a system administrator or an admin of the specified scheme.
     */
    protected final function checkAccessToScheme(int $schemeID, User $user): bool
    {
        return $this->isSystemAdministrator($user) || $this->isSchemeAdministrator($schemeID, $user->id);
    }

    /**
     * @param int $schemeID
     * @param User $user
     * @return int -1 if the specified user is a system administrator
     * <br>1 if the user is an admin of the specified scheme
     * <br>Else 0.
     */
    protected final function getSchemeAccess(int $schemeID, User $user): int
    {
        if ($this->isSystemAdministrator($user)) {
            return -1;
        } else if ($this->isSchemeAdministrator($schemeID, $user->id)) {
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * @param int $schemeID
     * @param int $userID
     * @return bool True if the specified user is an administrator of the specified scheme.
     */
    protected final function isSchemeAdministrator(int $schemeID, int $userID): bool
    {
        return SchemeAdmin::whereUserId($userID)->whereSchemeId($schemeID)->first() !== null;
    }

    /**
     * @param int $schemeID
     * @param int $userID
     * @return bool True if the specified user has joined the specified scheme.
     */
    protected final function isSchemeUser(int $schemeID, int $userID): bool
    {
        return SchemeUser::whereUserId($userID)->whereSchemeId($schemeID)->whereApproved(true)->first() !== null;
    }

    /**
     * @param User $user
     * @return bool True if the specified user is a system administrator.
     */
    protected final function isSystemAdministrator(User $user): bool
    {
        return $user->hasRole('sysadmin');
    }
}