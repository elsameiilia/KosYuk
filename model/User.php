<?php

namespace model;

class User
{
    public string $email;
    public string $password;
    public string $username;
    public string $name;
    public ?string $photo = null;
    public string $role;

}