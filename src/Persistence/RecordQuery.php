<?php

declare(strict_types=1);

namespace App\Persistence;

use InvalidArgumentException;
use LogicException;
use PDO;
use PDOStatement;
use function count;
use function get_class;
use function implode;
use function in_array;
use function mb_strtoupper;
use function trim;

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
        $clone = clone $this;

        /** @psalm-suppress MixedArrayAssignment */
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

        if (count($clone->where) > 0) {
            $clone->whereGroup++;
        }

        /** @psalm-suppress MixedArrayAssignment */
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

        if (! in_array(
            $direction,
            ['ASC', 'DESC'],
            true
        )) {
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

    /** @var int|null */
    private $limit;

    public function withLimit(?int $limit) : RecordQuery
    {
        $clone        = clone $this;
        $clone->limit = $limit;

        return $clone;
    }

    /** @var int */
    private $offset = 0;

    public function withOffset(int $offset) : RecordQuery
    {
        $clone         = clone $this;
        $clone->offset = $offset;

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

        /** @var Record[] $records */
        $records = $statement->fetchAll(
            PDO::FETCH_CLASS,
            get_class($this->recordClass)
        );

        return $records;
    }

    /**
     * @return array<string, mixed>
     */
    public function getSqlAndBind() : array
    {
        $sql = 'SELECT * FROM ' . $this->recordClass->getTableName();

        $bind = [];

        $idIncrement = 0;

        /**
         * @var int $key
         * @var array<int, array<string, string|null>> $whereGroup
         */
        foreach ($this->where as $key => $whereGroup) {
            if ($key === 0) {
                $sql .= ' WHERE (';
            } else {
                $sql .= ' OR (';
            }

            foreach ($whereGroup as $groupKey => $groupVal) {
                if ($groupVal['operator'] === 'IN') {
                    $in = [];

                    /** @var string[] $groupValVal */
                    $groupValVal = $groupVal['val'];

                    foreach ((array) $groupValVal as $val) {
                        $idIncrement++;

                        $id = $idIncrement;

                        $bindKey = ':' . ((string) $groupVal['col']) . '_' . $id;

                        $in[] = $bindKey;

                        $bind[$bindKey] = $val;
                    }

                    if ($groupKey !== 0) {
                        $sql .= ' AND ';
                    }

                    $sql .= ((string) $groupVal['col']) . ' IN (';

                    $sql .= implode(',', $in);

                    $sql .= ')';

                    continue;
                }

                if ($groupKey !== 0) {
                    $sql .= ' AND ';
                }

                if ($groupVal['val'] === null ||
                    $groupVal['val'] === 'null' ||
                    $groupVal['val'] === 'NULL'
                ) {
                    if ($groupVal['operator'] === '!=') {
                        $sql .= ((string) $groupVal['col']) . ' IS NOT NULL';
                    } else {
                        $sql .= ((string) $groupVal['col']) . ' IS NULL';
                    }
                } else {
                    $idIncrement++;

                    $id = $idIncrement;

                    $bindKey = ':' . ((string) $groupVal['col']) . '_' . $id;

                    $bind[$bindKey] = $groupVal['val'];

                    $sql .= ((string) $groupVal['col']) .
                        ' ' .
                        ((string) $groupVal['operator']) .
                        ' '
                        . $bindKey;
                }
            }

            $sql .= ')';
        }

        /**
         * @var int $key
         * @var array<string, string> $order
         */
        foreach ($this->order as $key => $order) {
            if ($key === 0) {
                $sql .= ' ORDER BY';
            } else {
                $sql .= ',';
            }

            $sql .= ' ' . $order['col'] . ' ' . $order['dir'];
        }

        if ($this->offset > 0) {
            $sql .= ' OFFSET ' . $this->offset;
        }

        if ($this->limit !== null) {
            $sql .= ' LIMIT ' . $this->limit;
        }

        $sql = trim($sql) . ';';

        return [
            'sql' => $sql,
            'bind' => $bind,
        ];
    }

    private function executeStatement() : PDOStatement
    {
        $sqlAndBind = $this->getSqlAndBind();

        /** @var string $sql */
        $sql = $sqlAndBind['sql'];

        $statement = $this->pdo->prepare($sql);

        /** @var array<string, string> $bind */
        $bind = $sqlAndBind['bind'];

        $statement->execute($bind);

        return $statement;
    }
}
