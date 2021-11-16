<?php

namespace App\models;

class User extends Table
{
    public function __construct($id = null, $config = null)
    {
        parent::__construct($config);
        $this->fields = [
            'name' => null,
            'email' => null,
            'is_admin' => 0,
            'password' => null
        ];

        $this->tableName = 'users';
        if ($id === null) {
            $this->isNew = true;
        } else {
            $this->fields['id'] = $id;
            $this->select();
            $this->isNew = false;
        }
    }

    public function getId(): ?string
    {
        return $this->fields['id'];
    }

    public function getEmail(): ?string
    {
        return $this->fields['email'];
    }

    public function setEmail(string $email): User
    {
        $this->fields['email'] = $email;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->fields['name'];
    }

    public function setName(string $name): User
    {
        $this->fields['name'] = $name;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->fields['password'];
    }

    public function setPassword(string $password): User
    {
        $this->fields['password'] = password_hash($password, PASSWORD_DEFAULT);
        return $this;
    }

    public function getIsAdmin(): ?string
    {
        return (bool)$this->fields['is_admin'];
    }

    public function setIsAdmin(bool $isAdmin): User
    {
        $this->fields['is_admin'] = (int)$isAdmin;
        return $this;
    }

    public function save(): User
    {
        if ($this->isNew) {
            $this->fields['id'] = $this->insert();
            return $this;
        }
        $this->update();
        return $this;
    }

    public function all(): array
    {
        $resData = $this->selectAll();
        return array_map(function ($resItem) {
            return new static($resItem['id'], $this->config);
        }, $resData);

    }

    public function find($id): User
    {
        $this->fields['id'] = $id;
        $this->select();
        return $this;
    }
}