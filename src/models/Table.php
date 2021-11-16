<?php

namespace App\models;

use App\DatabaseConnect;

abstract class Table
{
    protected \PDO $connect;
    protected ?array $config;
    protected string $tableName;
    protected array $fields;
    protected bool $isNew;

    public function __construct($config = null)
    {
        $this->config = $config;
        $this->connect = DatabaseConnect::getConnect($config);
    }

    protected function execQuery($query, $params = []): \PDOStatement
    {
        $statement = $this->connect->prepare($query);
        $statement->execute($params);
        return $statement;
    }

    protected function selectAll(): array
    {
        $query = "select id from {$this->tableName}";
        $queryRes = $this->execQuery($query);
        if (!$queryRes) {
            return [];
        }
        return $queryRes->fetchAll(\PDO::FETCH_ASSOC);
    }

    protected function select(): void
    {
        $query = "select * from {$this->tableName} where id = :id";
        $queryRes = $this->execQuery($query, ['id' => $this->fields['id']]);
        if (!$queryRes || $queryRes->rowCount() === 0) {
            throw new \Exception('could not get by supplied id');
        }
        $values = $queryRes->fetch(\PDO::FETCH_ASSOC);
        $this->fields = array_merge($this->fields, $values);
    }

    protected function insert(): int
    {
        $fieldNamesString = $this->getFieldNamesString();
        $fieldsSting = $this->getInsertFieldsString();
        $query = "insert into {$this->tableName} ({$fieldNamesString}) values ({$fieldsSting})";
        $queryRes = $this->execQuery($query, $this->fields);
        if (!$queryRes) {
            throw new \Exception("could not insert into {$this->tableName}");
        }
        return $this->connect->lastInsertId();
    }

    protected function update(): void
    {
        $updateFieldsString = $this->getUpdateFieldString();
        $query = "update {$this->tableName} set {$updateFieldsString} where id = :id";
        $params = array_merge($this->fields, ['id' => $this->fields['id']]);
        $queryRes = $this->execQuery($query, $params);
        if (!$queryRes) {
            throw new \Exception("could not update {$this->tableName}");
        }
    }

    private function getUpdateFieldString()
    {
        $fieldEls = array_map(fn ($item) => "{$item}=:{$item}", array_filter(array_keys($this->fields), fn ($item) => $item !== "id"));
        return implode(', ', $fieldEls);
    }

    private function getInsertFieldsString(): string
    {
        return implode(',', array_map(fn($item) => ":{$item}", array_keys($this->fields)));
    }

    private function  getFieldNamesString(): string
    {
        return implode(", ", array_keys($this->fields));
    }
}