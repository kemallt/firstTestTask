<?php

namespace App\Controllers;

use App\Models\Task;
use App\App;
use App\Models\User;

class TaskController
{
    public function showTasks()
    {
        $tasks = Task::allWithUsers();
        return App::view('showTasks', 'Список задач', ['tasks' => $tasks]);
    }
    
    public function createTask()
    {
        return App::view('editTask', 'Новая задача', ['formAction' => 'store']);
    }
    
    public function editTask($taskId)
    {
        $task = Task::find($taskId);
        if ($task->getId() === null) {
            return App::view('editTask', 'Новая задача', ['formAction' => 'update']);
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
        $validationResult = $this->validateTaskData($taskData);
        if (!$validationResult['valid']) {
            $_SESSION['errors'] = $validationResult['errors'];
            return App::view('editTask', 'Создать задачу', ['taskData' => $taskData]);
        }
        $currentUser = AuthController::getCurrentUser();
        $task = new Task();
        $task->setDescription($taskData['description']);
        $task->setUser($currentUser);
        $task->save();
        $_SESSION['messages'] = ['Задача успешно добавлена'];
        return $this->showTasks();
    }
    
    public function updateTask($taskData)
    {
        $validationResult = $this->validateTaskData($taskData);
        if (!$validationResult['valid']) {
            $_SESSION['errors'] = $validationResult['errors'];
            return App::view('editTask', 'Редактировать задачу', ['taskData' => $taskData]);
        }
        $task = new Task($taskData['id']);
        if ($taskData['id'] !== null) {
            $oldDescription = $task->getDescription();
            $isEditsByAdmin = $oldDescription !== $taskData['description'];
        }
        $task->setDescription($taskData['description']);
        $task->setIsDone($taskData['is_done'] ?? false);
        $task->setIsEditsByAdmin($isEditsByAdmin);
        $task->save();
        $_SESSION['messages'] = ['Задача успешно обновлена'];
        return $this->showTasks();
    }
    
    public function validateTaskData($taskData): array
    {
        $valid = true;
        $errors = [];
        if (!is_string($taskData['description']) || strlen($taskData['description']) < 3) {
            $valid = false;
            $errors[] = 'Описание должно быть более 3 символов в длину';
        }
        return ['valid' => $valid, 'errors' => $errors]; 
    }
}
