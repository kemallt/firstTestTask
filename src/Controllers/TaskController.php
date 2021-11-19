<?php

namespace App\Controllers;

use App\Models\Task;
use App\App;
use App\Models\User;

class TaskController
{
    public function showTasks($messages = null)
    {
        $tasks = Task::allWithUsers();
        return App::view('showTasks', 'Список задач', ['tasks' => $tasks], $messages = $messages);
    }
    
    public function createTask()
    {
        return App::view('editTask', 'Новая задача', ['formAction' => 'store']);
    }
    
    public function editTask($taskId)
    {
        $task = Task::find($taskId);
        if ($task->getId() === null) {
            return App::view('editTask', 'Новая задача', ['formAction' => 'store']);
        }
        $taskData = [
            'id' => $taskId,
            'user_id' => $task->getUser()->getId(),
            'description' => $task->getDescription(),
            'is_done' => $task->getIsDone()
        ];
        return App::view('editTask', 'Редактировать задачу', ['formAction' => 'update', 'taskData' => $taskData]);
    }
    
    public function storeTask($taskData)
    {
        $currentUser = LoginController::getCurrentUser();
        if ($currentUser === null) {
            return App::view('editTask', 'Создать задачу', ['taskData' => $taskData, 'errors' => ['user' => 'tasks may be created only by registerd users']]);
        }
        $validationResult = $this->validateTaskData($taskData, $currentUser);
        if (!$validationResult['valid']) {
            return App::view('editTask', 'Создать задачу', ['taskData' => $taskData, 'errors' => $validationResult['errors']]);
        }
        $task = new Task();
        $task->setDescription($taskData['description']);
        $task->save();
        $messages = ['task successfully saved'];
        return $this->showTasks($messages);
    }
    
    public function updateTask($taskData)
    {
        $currentUser = LoginController::getCurrentUser();
        if ($currentUser === null || !$currentUser->getIsAdmin()) {
            return App::view('editTask', 'Редактировать задачу', ['taskData' => $taskData, 'errors' => ['user' => 'tasks may be edited only by admin']]);
        }
        $validationResult = $this->validateTaskData($taskData);
        if (!$validationResult['valid']) {
            return App::view('editTask', 'Редактировать задачу', ['taskData' => $taskData, 'errors' => $validationResult['errors']]);
        }
        $validationEditResult = $this->validateEditTaskData($taskData);
        if (!$validationEditResult['valid']) {
            return App::view('editTask', 'Редактировать задачу', ['taskData' => $taskData, 'errors' => $validationEditResult['errors']]);
        }
        $task = new Task($taskData['id']);
        if ($taskData['id'] !== null) {
            $oldDescription = $task->getDescription();
            $isEditsByAdmin = $oldDescription !== $taskData['description'];
        }
        $task->setDescription($taskData['description']);
        $task->setUser($currentUser);
        $task->setIsDone($taskData['is_done']);
        $task->setIsEditsByAdmin($isEditsByAdmin);
        $task->save();
        $messages = ['task successfully saved'];
        return $this->showTasks($messages);
    }
    
    public function validateEditTaskData($taskData)
    {
        $valid = true;
        $errors = [];
        if (!is_bool($taskData['is_done'])) {
            $valid = false;
            $errors[] = 'must be boolean';
        }
        if ($taskData['id'] !== null && !is_int($taskData['id'])) {
            $valid = false;
            $errors[] = 'id must be null or interger';
        }
        return ['valid' => $valid, 'errors' => $errors];
    }
    
    public function validateTaskData($taskData, $currentUser): array
    {
        $valid = true;
        $errors = [];
        if (!is_string($taskData['description']) || strlen($taskData['description']) < 3) {
            $valid = false;
            $errors[] = 'must be string more then 3 chars';
        }
        return ['valid' => $valid, 'errors' => $errors]; 
    }
}
