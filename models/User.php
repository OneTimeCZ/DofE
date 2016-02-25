<?php

namespace Models;

use Models\Base\User as BaseUser;

/**
 * Skeleton subclass for representing a row from the 'users' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class User extends BaseUser
{
    public function signInUpdate() {
        $this->setSigninCount($this->getSigninCount()+1);
        $this->setLastSigninAt(date("U"));
        $this->save();
    }
}
