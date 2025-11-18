<?php

namespace app\database\builder;

use app\database\Connection;

class SelectQuery
{
    private ?string $table = null;
    private ?string $fields = null;
    private string $order;
    private int $limit = 10;
    private int $offset = 0;
    private array $where = [];
    private array $binds = [];
    private string $limits;
    public static function select(string $fields = '*'): ?self
    {
        $self = new self;
        $self->fields = $fields;
        return $self;
    }
    public function from(string $table): ?self
    {
        $this->table = '';
        $this->table = $table;
        return $this;
    }
    public function where(string $field, string $operator, string|int $value, ?string $logic = null): ?self
    {
        # Define um placeholder baseado no nome do campo
        $placeHolder = '';
        $placeHolder = $field;
        # Caso o campo venha com um alias (ex: "u.id"), extrai apenas o nome da coluna (ex: "id")
        if (str_contains($placeHolder, '.')) {
            $placeHolder = substr($field, strpos($field, '.') + 1);
        }
        # Monta a expressão da cláusula WHERE com o placeholder e operador lógico
        $this->where[] = "{$field} {$operator} :{$placeHolder} {$logic}";
        # Associa o valor ao placeholder no array de binds
        $this->binds[$placeHolder] = $value;
        # Retorna a própria instância para encadeamento
        return $this;
    }
    public function order(string $field, string $value): ?self
    {
        $this->order = " order by {$field} {$value}";
        return $this;
    }
    public function createQuery()
    {
        if (!$this->fields) {
            throw new \Exception("Por favor informe os campos a serem selecionados na consulta");
        }
        if (!$this->table) {
            throw new \Exception("Por favor informe o nome da tabela");
        }
        $query = '';
        $query = 'select ';
        $query .= $this->fields . ' from ';
        $query .= $this->table;
        $query .= (isset($this->where) and (count($this->where) > 0)) ? ' where ' . implode(' ', $this->where) : '';
        $query .= $this->order ?? '';
        $query .= $this->limits ?? '';
        return $query;
    }
    public function limit(int $limit, int $offset): ?self
    {
        $this->limit = $limit;
        $this->offset = $offset;
        $this->limits = " limit {$this->limit} offset {$this->offset}";
        return $this;
    }
    public function fetch($IsArray = true)
    {
        $query = '';
        $query = $this->createQuery();
        try {
            $connection = Connection::connection();
            $prepare = $connection->prepare($query);
            $prepare->execute($this->binds ?? []);
            return $IsArray ? $prepare->fetch(\PDO::FETCH_ASSOC) : $prepare->fetch(\PDO::FETCH_OBJ);
        } catch (\PDOException $e) {
            throw new \Exception("Restrição: {$e->getMessage()}");
        }
    }
    public function fetchAll($IsArray = true)
    {
        $query = '';
        $query = $this->createQuery();
        try {
            $connection = Connection::connection();
            $prepare = $connection->prepare($query);
            $prepare->execute($this->binds ?? []);
            return $IsArray ? $prepare->fetchAll(\PDO::FETCH_ASSOC) : $prepare->fetchAll(\PDO::FETCH_OBJ);
        } catch (\PDOException $e) {
            throw new \Exception("Restrição: {$e->getMessage()}");
        }
    }
}