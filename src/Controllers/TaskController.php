<?php

namespace App\Controllers;

use App\Models\Task;
use App\App;
use App\Models\User;

class TaskController
{
    public function showTasks()
    {
        $tasks = Task::all();
        return App::view('showTasks', 'Список задач', ['tasks' => $tasks]);
    }
    
    public function createTask()
    {
        return App::view('editTask', 'Новая задача');
    }
    
    public function editTask($taskId)
    {
        $task = Task::find($taskId);
        if ($task->getId() === null) {
            return App::view('editTask', 'Новая задача');
        }
        $taskData = [
            'id' => $taskId,
            'user_id' => $task->getUser()->getId(),
            'description' => $task->getDescription(),
            'is_done' => $task->getIsDone()
        ];
        return App::view('editTask', 'Редактировать задачу', ['taskData' => $taskData]);
    }
    
    public function saveTask($taskData)
    {
        if (!$this->validateTaskData($taskData)) {
            return App::view('editTask', 'Редактировать задачу', ['taskData' => $taskData]);
        }
        $task = new Task($taskData['id'] ?? null);
        if ($taskData['id'] !== null) {
            $oldDescription = $task->getDescription();
            $isEditsByAdmin = $oldDescription !== $taskData['description'];
        }
        $task->setDescription($taskData['description']);
        $task->setUser(new User($taskData['user_id']));
        $task->setIsDone($taskData['is_done']);
        $task->setIsEditsByAdmin($isEditsByAdmin);
        $task->save();
        return $this->showTasks();
    }
    
    public function validateTaskData($taskData)
    {
        if (!is_string($taskData['description']) || strlen($taskData['description']) < 3) {
            return false;
        }
        if (!is_bool($taskData['is_done'])) {
            return false;
        }
        if (!is_int($taskData['user_id'])) {
            return false;
        }
        if ($taskData['id'] !== null && !is_int($taskData['id'])) {
            return false;
        }
    }
}
