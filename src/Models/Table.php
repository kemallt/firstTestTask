<?php

namespace App\Models;

use App\DatabaseConnect;

abstract class Table
{
    protected static string $tableName;
    protected static ?\PDO $connect = null;

    protected array $fields;
    protected bool $isNew;

    public function __construct()
    {
        if (self::$connect === null) {
            self::init();
        }
    }

    protected static function init()
    {
        static::$connect = DatabaseConnect::getConnect();
    }

    protected static function execQuery($query, $params = []): \PDOStatement
    {
        self::init();
        $statement = self::$connect->prepare($query);
        $statement->execute($params);
        return $statement;
    }

    protected static function selectAll(): array
    {
        $tableName = static::$tableName;
        $query = "select id from {$tableName}";
        $queryRes = self::execQuery($query);
        if (!$queryRes) {
            return [];
        }
        return $queryRes->fetchAll(\PDO::FETCH_ASSOC);
    }

    protected function select(): void
    {
        $tableName = static::$tableName;
        $query = "select * from {$tableName} where id = :id";
        $queryRes = self::execQuery($query, ['id' => $this->fields['id']]);
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
        $tableName = static::$tableName;
        $query = "insert into {$tableName} ({$fieldNamesString}) values ({$fieldsSting})";
        $queryRes = self::execQuery($query, $this->fields);
        if (!$queryRes) {
            throw new \Exception("could not insert into {$tableName}");
        }
        return self::$connect->lastInsertId();
    }

    protected function update(): void
    {
        $updateFieldsString = $this->getUpdateFieldString();
        $tableName = static::$tableName;
        $query = "update {$tableName} set {$updateFieldsString} where id = :id";
        $params = array_merge($this->fields, ['id' => $this->fields['id']]);
        $queryRes = self::execQuery($query, $params);
        if (!$queryRes) {
            throw new \Exception("could not update {$tableName}");
        }
    }

    private function getUpdateFieldString()
    {
        $fieldEls = array_map(
            fn ($item) => "{$item}=:{$item}",
            array_filter(array_keys($this->fields), fn ($item) => $item !== "id")
        );
        return implode(', ', $fieldEls);
    }

    private function getInsertFieldsString(): string
    {
        return implode(',', array_map(fn($item) => ":{$item}", array_keys($this->fields)));
    }

    private function getFieldNamesString(): string
    {
        return implode(", ", array_keys($this->fields));
    }
}
