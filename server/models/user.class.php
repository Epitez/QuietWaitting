<?php
/**
 * User
 */
class User extends Model
{
    protected static function attributes() {
        return ['fullName', 'password', 'email', 'admin', 'rememberToken'];
    }

    protected static function primary() {
        return 'username';
    }

    protected static function defaults() {
        return ['admin' => false, 'rememberToken' => ''];
    }

    function __construct($arguments = [])
    {
        // TODO $arguments
        parent::__construct();
    }
}

?>
