<?php

namespace App\models;

class Task extends Table
{
    public function __construct($id = null)
    {
        static::$tableName = 'tasks';

        parent::__construct();
        $this->fields = [
            'user_id' => null,
            'description' => null,
            'is_done' => 0,
            'is_edits_by_admin' => 0
        ];

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

    public function getDescription(): ?string
    {
        return $this->fields['description'];
    }

    public function setDescription(string $description): Task
    {
        $this->fields['description'] = $description;
        return $this;
    }

    public function getIsDone(): ?string
    {
        return (bool)$this->fields['is_done'];
    }

    public function setIsDone(string $isDone): Task
    {
        $this->fields['is_done'] = (int)$isDone;
        return $this;
    }

    public function getIsEditsByAdmin(): ?string
    {
        return $this->fields['is_edits_by_admin'];
    }

    public function setIsEditsByAdmin(string $isEditsByAdmin): Task
    {
        $this->fields['is_edits_by_admin'] = password_hash($isEditsByAdmin, PASSWORD_DEFAULT);
        return $this;
    }

    public function save(): Task
    {
        if ($this->fields['description'] === null) {
            throw new \Exception('cannot save task without description');
        }
        if ($this->fields['user_id'] === null) {
            throw new \Exception('cannot save task without user');
        }
        if ($this->isNew) {
            $this->fields['id'] = $this->insert();
            return $this;
        }
        $this->update();
        return $this;
    }

    public static function all(): array
    {
        $resData = self::selectAll();
        return array_map(function ($resItem) {
            return new static($resItem['id']);
        }, $resData);

    }

    public static function find($id): Task
    {
        $task = new self($id);
        $task->select();
        return $task;
    }
    
    public function getUser(): User
    {
        $user = new User($this->fields['user_id']);
        return $user;
    }
    
    public function setUser(User $user): Task
    {
        $this->fields['user_id'] = $user->getId();
        return $this;
    }
}