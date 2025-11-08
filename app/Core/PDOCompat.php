<?php
namespace App\Core;

use PDO;
use PDOStatement;

/**
 * Lightweight wrapper around PDOStatement to provide mysqli-like methods
 * (fetch_assoc) while delegating to PDO for fetchAll/fetch etc.
 */
class PDOCompat
{
    /** @var PDOStatement */
    private $stmt;

    public function __construct(PDOStatement $stmt)
    {
        $this->stmt = $stmt;
    }

    /**
     * Fetch next row as associative array (mysqli::fetch_assoc compatible)
     * @return array|false
     */
    public function fetch_assoc()
    {
        return $this->stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Fetch all rows
     */
    public function fetchAll($mode = PDO::FETCH_ASSOC)
    {
        return $this->stmt->fetchAll($mode);
    }

    /**
     * Generic fetch
     */
    public function fetch($mode = PDO::FETCH_ASSOC)
    {
        return $this->stmt->fetch($mode);
    }

    /**
     * rowCount proxy
     */
    public function rowCount()
    {
        return $this->stmt->rowCount();
    }

    /**
     * Allow calling other PDOStatement methods
     */
    public function __call($name, $args)
    {
        return call_user_func_array([$this->stmt, $name], $args);
    }

    /**
     * Get inner PDOStatement
     */
    public function getInnerStatement()
    {
        return $this->stmt;
    }
}
