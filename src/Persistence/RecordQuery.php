<?php

declare(strict_types=1);

namespace App\Persistence;

use InvalidArgumentException;
use LogicException;
use PDO;
use PDOStatement;
use function get_class;
use function in_array;
use function mb_strtoupper;
use function uniqid;

class RecordQuery
{
    /** @var bool */
    private $isInstantiated = false;
    /** @var Record */
    private $recordClass;
    /** @var PDO */
    private $pdo;

    public function __construct(Record $recordClass, PDO $pdo)
    {
        if ($this->isInstantiated) {
            throw new LogicException(
                'RecordQuery can only be instantiated once'
            );
        }

        $this->isInstantiated = true;
        $this->recordClass    = $recordClass;
        $this->pdo            = $pdo;
    }

    /** @var mixed[] */
    private $where = [];

    /**
     * @param mixed $val
     */
    public function withWhere(
        string $col,
        $val,
        string $operator = '='
    ) : RecordQuery {
        $clone                             = clone $this;
        $clone->where[$this->whereGroup][] = [
            'col' => $col,
            'val' => $val,
            'operator' => $operator,
        ];

        return $clone;
    }

    /** @var int */
    private $whereGroup = 0;

    /**
     * @param mixed $val
     */
    public function withNewWhereGroup(
        string $col,
        $val,
        string $operator = '='
    ) : RecordQuery {
        $clone = clone $this;
        $clone->whereGroup++;
        $clone->where[$clone->whereGroup][] = [
            'col' => $col,
            'val' => $val,
            'operator' => $operator,
        ];

        return $clone;
    }

    /** @var mixed[] */
    private $order = [];

    public function withOrder(
        string $column,
        string $direction = 'desc'
    ) : RecordQuery {
        $direction = mb_strtoupper($direction);

        if (! in_array($direction, ['ASC', 'DESC'])) {
            throw new InvalidArgumentException(
                'Direction must be asc or desc',
            );
        }

        $clone          = clone $this;
        $clone->order[] = [
            'col' => $column,
            'dir' => $direction,
        ];

        return $clone;
    }

    public function one() : ?Record
    {
        $statement = $this->executeStatement();

        /** @var Record|bool $record */
        $record = $statement->fetchObject(
            get_class($this->recordClass)
        );

        if ($record instanceof Record) {
            return $record;
        }

        return null;
    }

    /**
     * @return Record[]
     */
    public function all() : array
    {
        $statement = $this->executeStatement();

        return $statement->fetchAll(
            PDO::FETCH_CLASS,
            get_class($this->recordClass)
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function getSqlAndBind() : array
    {
        $sql = 'SELECT * FROM ' . $this->recordClass->getTableName();

        $bind = [];

        foreach ($this->where as $key => $whereGroup) {
            if ($key === 0) {
                $sql .= ' WHERE (';
            } else {
                $sql .= ' OR (';
            }

            foreach ($whereGroup as $groupKey => $groupVal) {
                $id = uniqid('', false);

                $bindKey = ':' . $groupVal['col'] . '_' . $id;

                $bind[$bindKey] = $groupVal['val'];

                if ($groupKey === 0) {
                    $sql .= $groupVal['col'] .
                        ' ' .
                        $groupVal['operator'] .
                        ' '
                        . $bindKey;
                } else {
                    $sql .= ' AND ' .
                        $groupVal['col'] .
                        $groupVal['operator'] .
                        $bindKey;
                }
            }

            $sql .= ')';
        }

        foreach ($this->order as $key => $order) {
            if ($key === 0) {
                $sql .= ' ORDER BY';
            } else {
                $sql .= ',';
            }

            $sql .= ' ' . $order['col'] . ' ' . $order['dir'];
        }

        $sql .= ';';

        return [
            'sql' => $sql,
            'bind' => $bind,
        ];
    }

    private function executeStatement() : PDOStatement
    {
        $sqlAndBind = $this->getSqlAndBind();

        $statement = $this->pdo->prepare($sqlAndBind['sql']);

        $statement->execute($sqlAndBind['bind']);

        return $statement;
    }
}
