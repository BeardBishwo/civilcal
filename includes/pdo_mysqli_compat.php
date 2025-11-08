<?php
/**
 * PDO -> mysqli-style compatibility layer (minimal)
 * Provides PDOPreparedStatementWrapper and PDOPreparedResult so legacy modules
 * using bind_param()/get_result()/fetch_assoc() continue to work.
 *
 * Note: This is a lightweight adapter. It maps positional ? placeholders to
 * PDO positional parameters and executes with an array of values. It does not
 * implement every mysqli feature; extend as needed.
 */

class PDOPreparedResult {
    private $rows = [];
    private $pos = 0;

    public function __construct(array $rows)
    {
        $this->rows = $rows;
    }

    /**
     * Fetch next row as associative array (mysqli_result::fetch_assoc)
     * @return array|null
     */
    public function fetch_assoc()
    {
        if (isset($this->rows[$this->pos])) {
            return $this->rows[$this->pos++];
        }
        return null;
    }

    /**
     * Fetch all rows as an array of assoc arrays
     * @return array
     */
    public function fetch_all()
    {
        return $this->rows;
    }

    /**
     * Alias commonly used in code
     */
    public function fetch_all_assoc()
    {
        return $this->fetch_all();
    }

    public function num_rows()
    {
        return count($this->rows);
    }
}

class PDOPreparedStatementWrapper {
    /** @var PDOStatement */
    private $stmt;
    private $pdo;
    private $sql;
    private $params = [];

    public function __construct(PDO $pdo, $sql)
    {
        $this->pdo = $pdo;
        $this->sql = $sql;
        $this->stmt = $pdo->prepare($sql);
    }

    /**
     * Minimal bind_param implementation: collects parameters in order.
     * mysqli uses a types string (e.g. "iss"). We ignore types and rely on PDO.
     * Usage: $stmt->bind_param('is', $id, $name);
     */
    public function bind_param()
    {
        $args = func_get_args();
        if (count($args) < 2) {
            return false;
        }
        // drop types string
        array_shift($args);
        // flatten references/values
        $this->params = $args;
        return true;
    }

    /**
     * Execute the statement. Returns true/false like mysqli_stmt::execute
     */
    public function execute()
    {
        try {
            // PDO expects 1-indexed array for positional placeholders or 0-indexed? It accepts 1-indexed numeric keys or a plain array.
            $params = $this->params;
            return $this->stmt->execute($params);
        } catch (Exception $e) {
            error_log('PDOPreparedStatementWrapper execute error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Convenience: run execute and return boolean
     */
    public function run()
    {
        return $this->execute();
    }

    /**
     * Return a PDOPreparedResult object with fetched rows (mysqli_stmt::get_result)
     */
    public function get_result()
    {
        // Ensure statement executed
        if ($this->stmt->rowCount() === 0) {
            // Try executing if not yet executed
            try {
                $this->stmt->execute($this->params);
            } catch (Exception $e) {
                error_log('PDOPreparedStatementWrapper get_result execute error: ' . $e->getMessage());
                return new PDOPreparedResult([]);
            }
        }

        $rows = $this->stmt->fetchAll(PDO::FETCH_ASSOC);
        return new PDOPreparedResult($rows ?: []);
    }

    /**
     * Shortcut to last insert id if code expects $this->db->insert_id after execute.
     * Note: this is provided on Database level normally. Kept for parity.
     */
    public function insert_id()
    {
        try {
            return $this->pdo->lastInsertId();
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * Bind value by index (1-based) - optional helper if some code uses it
     */
    public function bindValue($index, $value, $type = null)
    {
        // PDO bindValue uses 1-based indices for positional
        $this->stmt->bindValue((int)$index, $value);
    }

    /**
     * Allow calling execute/get_result directly via magic
     */
    public function __call($name, $args)
    {
        if ($name === 'bind_param') {
            return call_user_func_array([$this, 'bind_param'], $args);
        }
        if ($name === 'get_result') {
            return call_user_func_array([$this, 'get_result'], $args);
        }
        if ($name === 'execute') {
            return call_user_func_array([$this, 'execute'], $args);
        }
        return null;
    }
}

// End of compat file
