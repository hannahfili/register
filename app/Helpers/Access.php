<?php

namespace App\Helpers;

class Access
{
    public $teacherAccess;
    public $studentAccess;
    public $adminAccess;
    public $allUsersAccess;

    function __construct()
    {
        $this->teacherAccess = false;
        $this->studentAccess = false;
        $this->adminAccess = false;
        $this->allUsersAccess = false;
    }
    public function set_teacherAccess()
    {
        $this->teacherAccess = true;
    }
    public function set_adminAccess()
    {
        $this->adminAccess = true;
    }
    function set_studentAccess()
    {
        $this->studentAccess = true;
    }
    function set_allUsersAccess()
    {
        $this->allUsersAccess = true;
    }
    public function is_teacherAccess()
    {
        return $this->teacherAccess;
    }
    public function is_adminAccess()
    {
        return $this->adminAccess;
    }
    function is_studentAccess()
    {
        return $this->studentAccess;
    }
    function is_allUsersAccess()
    {
        return $this->allUsersAccess;
    }
}
