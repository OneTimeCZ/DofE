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
    public static function sideBarInit(){
        $user = UserQuery::create()
            ->joinWith('Image')
            ->select(array('Username', 'Url', 'Permissions', 'Image.Path'))
            ->findPk($_SESSION["user"]);
        
        return $user;
    }
}
