<?php

namespace Hyiplab\BackOffice\Database;

use Hyiplab\BackOffice\Facade\DB;

class QueryBuilder
{
    protected $model;

    protected $table;

    private $select = '*';

    private $where;

    private $order;

    private $limit;

    private $join;

    private $cleanUp;

    public $finalSql;

    public $count;

    public $attributes = [];

    public function __construct($model, $table)
    {
        $this->model = $model;
        $this->table = $table;
    }

    public function get($arguments = [])
    {
        $table = $this->table;
        $sql = sprintf('SELECT %s FROM {{table_prefix}}%s%s', $this->select, $table, $this->extra());
        $this->finalSql = $sql;
        return DB::execute($this->finalSql);
    }

    public function where($field, $condition, $equal = null)
    {
        $symbol = $condition;
        if (!$this->arithmeticSym($condition)) {
            $symbol = '=';
            $equal = $condition;
        }
        if (is_array($field)) {
            $this->__where($field, symbol: $symbol);
        } else {
            $this->__where([$field => $equal], symbol: $symbol);
        }

        return $this;
    }

    public function orWhere($field, $condition, $value)
    {
        $symbol = $condition;
        if (!$this->arithmeticSym($condition)) {
            $symbol = '=';
        }
        $where = $this->where;
        $where .= sprintf(" OR `%s` $symbol '%s'", $field, $this->escape_string($value));
        $this->where = $where;
        return $this;
    }

    public function whereNot($field, $condition, $equal = null)
    {
        $symbol = $condition;
        if (!$this->arithmeticSym($condition)) {
            $symbol = '=';
            $equal = $condition;
        }
        $where = $this->where;

        if (!is_array($field)) {
            $field = [$field => $equal];
        }

        $type = 'AND';

        foreach ($field as $row => $value) {
            if (empty($where)) {
                $where = sprintf("WHERE NOT `%s` $symbol '%s'", $row, $this->escape_string($value));
            } else {
                $where .= sprintf(" %s NOT `%s` $symbol '%s'", $type, $row, $this->escape_string($value));
            }
        }
        $this->where = $where;
        return $this;
    }

    public function orWhereNot($field, $condition, $equal = null)
    {
        $symbol = $condition;
        if (!$this->arithmeticSym($condition)) {
            $symbol = '=';
            $equal = $condition;
        }
        $where = $this->where;
        if (!is_array($field)) {
            $field = [$field => $equal];
        }

        foreach ($field as $row => $value) {
            $where .= sprintf(" OR NOT `%s` $symbol '%s'", $row, $this->escape_string($value));
        }
        $this->where = $where;
        return $this;
    }

    public function isNull($field)
    {
        $where = $this->where;
        if (!is_array($field)) {
            $field = [$field];
        }
        foreach ($field as $column) {
            if (empty($where)) {
                $where = sprintf("WHERE `%s` IS NULL", $column);
            } else {
                $where .= sprintf(" OR `%s` IS NULL", $column);
            }
        }
        $this->where = $where;
        return $this;
    }

    public function isNotNull($field)
    {
        $where = $this->where;
        if (!is_array($field)) {
            $field = [$field];
        }
        foreach ($field as $column) {
            if (empty($where)) {
                $where = sprintf("WHERE `%s` IS NOT NULL", $column);
            } else {
                $where .= sprintf(" OR `%s` IS NOT NULL", $column);
            }
        }
        $this->where = $where;
        return $this;
    }

    public function tableColumns($table)
    {
        $sql = "DESCRIBE $table";
        $this->finalSql = $sql;
        return DB::execute($this->finalSql);
    }

    public function whereIn($field, $info)
    {
        $where = $this->where;
        $value = '';
        foreach ($info as $inf) {
            $value .= sprintf("'%s'", $this->escape_string($inf)) . ',';
        }
        
        $value = rtrim($value, ',');
        
        if (empty($where)) {
            $where = sprintf("WHERE `%s` IN(%s)", $field, $value);
        } else {
            $where .= sprintf(" AND `%s` IN(%s)", $field, $value);
        }
        
        $this->where = $where;
        return $this;
    }

    public function whereBetween($field, $info)
    {
        $where = $this->where;
        if (empty($where)) {
            $where = sprintf("WHERE `%s` BETWEEN '%s' AND '%s'", $field, $this->escape_string($info[0]), $this->escape_string($info[1]));
        } else {
            $where .= sprintf(" AND `%s` BETWEEN '%s' AND '%s'", $field, $this->escape_string($info[0]), $this->escape_string($info[1]));
        }

        $this->where = $where;
        return $this;
    }

    public function limit($limit, $offset = 0)
    {
        $this->count = $limit;
        $this->limit = 'LIMIT ' . $limit;
        if ($offset) {
            $this->limit .= ' OFFSET ' . $offset;
        }
        return $this;
    }

    public function skip($offset)
    {
        $this->limit .= ' OFFSET ' . $offset;

        return $this;
    }

    public function orderBy($by, $order_type = 'ASC')
    {
        $order = $this->order;

        if (is_array($by)) {
            foreach ($by as $field => $type) {
                if (is_int($field) && !preg_match('/(DESC|desc|ASC|asc)/', $type)) {
                    $field = $type;
                    $type = $order_type;
                }
                if (empty($order)) {
                    $order = sprintf('ORDER BY %s %s', '{{table_prefix}}' . $this->table . '.' . $field, $type);
                } else {
                    $order .= sprintf(', %s %s', '{{table_prefix}}' . $this->table . '.' . $field, $type);
                }
            }
        } else {
            if (empty($order)) {
                $order = sprintf('ORDER BY %s %s', '{{table_prefix}}' . $this->table . '.' . $by, $order_type);
            } else {
                $order .= sprintf(', %s %s', '{{table_prefix}}' . $this->table . '.' . $by, $order_type);
            }
        }
        $this->order = $order;

        return $this;
    }

    public function distinct($column)
    {
        $distinct = sprintf('DISTINCT `%s`', $column);
        $this->select = $distinct;
        return $this;
    }

    public function selectRaw($sql)
    {
        $this->finalSql = $sql;
        return DB::execute($this->finalSql);
    }

    public function first()
    {
        $table = $this->table;
        $sql = $sql = sprintf('SELECT %s FROM {{table_prefix}}%s%s', $this->select, $table, $this->extra());
        $this->finalSql = $sql;
        $result = DB::getRow($this->finalSql);
        if (!$result) {
            return null;
        }
        $modelInstance = new $this->model;
        $modelInstance->result_data = $result;
        $modelInstance->for_save = true;
        return $modelInstance;
    }

    public function find($id)
    {
        $this->where('id', $id);
        return $this->first();
    }

    public function findOrFail($id)
    {
        $result = $this->find($id);
        if (!$result) {
            hyiplab_abort(404);
        }
        return $result;
    }

    public function firstOrFail()
    {
        $result = $this->first();
        if (!$result) {
            hyiplab_abort(404);
        }
        return $result;
    }

    public function sum($colName)
    {
        $table = $this->table;
        $extra = $this->extra($this->cleanUp);
        $sql = sprintf('SELECT SUM(%s) FROM {{table_prefix}}%s%s', $colName, $table, $extra);
        $this->finalSql = $sql;
        $sum = DB::getVar($this->finalSql);
        return (int) $sum;
    }

    public function avg($colName)
    {
        $table = $this->table;
        $extra = $this->extra($this->cleanUp);
        $sql = sprintf('SELECT AVG(%s) FROM {{table_prefix}}%s%s', $colName, $table, $extra);
        $this->finalSql = $sql;
        return DB::getVar($this->finalSql);
    }

    public function min($colName)
    {
        $table = $this->table;
        $extra = $this->extra($this->cleanUp);
        $sql = sprintf('SELECT MIN(%s) FROM {{table_prefix}}%s%s', $colName, $table, $extra);
        $this->finalSql = $sql;
        return DB::getVar($this->finalSql);
    }

    public function max($colName)
    {
        $table = $this->table;
        $extra = $this->extra($this->cleanUp);
        $sql = sprintf('SELECT MAX(%s) FROM {{table_prefix}}%s%s', $colName, $table, $extra);
        $this->finalSql = $sql;
        return DB::getVar($this->finalSql);
    }

    public function count()
    {
        $table = $this->table;
        $extra = $this->extra($this->cleanUp);
        $sql = sprintf('SELECT COUNT(*) FROM {{table_prefix}}%s%s', $table, $extra);
        $this->finalSql = $sql;
        $count = DB::getVar($this->finalSql);
        return (int) $count;
    }

    public function insert($data)
    {
        $table = $this->table;
        $fields = '';
        $values = '';

        foreach ($data as $col => $value) {
            $fields .= sprintf('`%s`,', $col);
            $values .= sprintf("'%s',", $this->escape_string($value));
        }

        $fields = substr($fields, 0, -1);
        $values = substr($values, 0, -1);

        $sql = sprintf('INSERT INTO {{table_prefix}}%s (%s) VALUES (%s)', $table, $fields, $values);
        $this->finalSql = $sql;
        return DB::query($this->finalSql);
    }

    public function update($info)
    {
        if (empty($this->where)) {
            return die('Where method is required');
        } else {
            $table = $this->table;

            $update = '';

            foreach ($info as $col => $value) {
                $update .= sprintf("`%s`='%s', ", $col, $this->escape_string($value));
            }
            $update = substr($update, 0, -2);
            $sql = sprintf('UPDATE {{table_prefix}}%s SET %s%s', $table, $update, $this->extra());
            $this->finalSql = $sql;
            DB::query($this->finalSql);
        }
    }

    public function delete()
    {
        $table = $this->table;
        if (array_key_exists('for_save', @Model::$storedData[$this->model] ?? [])) {
            $modelId = Model::$storedData[$this->model]['result_data']->id;
            $this->where('id', $modelId);
        }
        if (empty($this->where)) {
            die("Where is not set. Can't delete whole table.");
        } else {
            $sql = sprintf('DELETE FROM {{table_prefix}}%s%s', $table, $this->extra());
            $this->finalSql = $sql;
            DB::query($this->finalSql);
        }
    }

    public function save()
    {
        if (!array_key_exists('for_save', @Model::$storedData[$this->model] ?? [])) {
            return $this->insert($this->attributes);
        } else {
            $modelId = Model::$storedData[$this->model]['result_data']->id;
            $this->where('id', $modelId);
            $this->update($this->attributes);
        }
    }

    private function __where($info, $type = 'AND', $symbol = '=')
    {
        $where = $this->where;
        foreach ($info as $row => $value) {
            if (empty($where)) {
                $where = sprintf("WHERE `%s` $symbol '%s'", $row, $this->escape_string($value));
            } else {
                $where .= sprintf(" %s `%s` $symbol '%s'", $type, $row, $this->escape_string($value));
            }
        }
        $this->where = $where;
    }

    private function extra($cleanUp = true)
    {
        $extra = '';
        if (!empty($this->where)) {
            $extra .= ' ' . $this->where;
        }
        if (!empty($this->join)) {
            $extra .= ' ' . $this->join;
        }
        if (!empty($this->order)) {
            $extra .= ' ' . $this->order;
        }
        if (!empty($this->limit)) {
            $extra .= ' ' . $this->limit;
        }
        if ($cleanUp) {
            $this->cleanUp();
        }

        return $extra;
    }

    private function cleanUp()
    {
        // cleanup
        $this->where = null;
        $this->order = null;
        $this->limit = null;
    }

    public function escape_string($string = '')
    {
        $wpdb = DB::wpdb();
        return $wpdb->_real_escape($string);
    }

    private function arithmeticSym($symbol)
    {
        $symbols = [
            '=',
            '!=',
            '<',
            '>',
            '<=',
            '>=',
            'LIKE'
        ];
        return in_array($symbol, $symbols);
    }

    public function paginate($number)
    {
        $table         = $this->table;
        $this->cleanUp = false;
        $totalData     = $this->count($table);
        $pnum          = isset($_GET['pnum']) ? intval($_GET['pnum']) : 1;
        $per_page      = $number;
        $query         = $this->limit($per_page, ($pnum - 1) * $per_page);
        $results       = $query->get($table);
        $data          = [
            'data'  => $results,
            'links' => $this->getPageLinks($totalData, $per_page)
        ];
        $data = hyiplab_to_object($data);
        return $data;
    }

    private function getPageLinks($total = 0, $limit = 10)
    {
        $page = (isset($_GET['pnum']) && is_numeric($_GET['pnum'])) ? $_GET['pnum'] : 1;

        $totalPages = ceil($total / $limit);

        // Prev + Next
        $prev = $page - 1;
        $next = $page + 1;

        $html = '';

        if ($total >= $limit) {
            $html = "<nav>";
            $html .= '<ul class="pagination justify-content-center">';
            $dNone = $page <= 1 ? 'd-none' : '';
            $html .= '<li class="page-item ' . $dNone . '"> <a class="page-link" href="' . hyiplab_query_to_url(['pnum' => $prev]) . '"><i class="las la-angle-left"></i></a></li>';
            $linksPerPage = 6;
            $start        = max(1, $page - floor($linksPerPage / 2));
            $end          = min($totalPages, $start + $linksPerPage - 1);
            $start        = max(1, $end - $linksPerPage + 1);
            for ($i = $start; $i <= $end; $i++) {
                $active = ($page == $i) ? ' active' : '';
                $html .= '<li class="page-item' . $active . '"><a class="page-link" href="' . hyiplab_query_to_url(['pnum' => $i]) . '">' . $i . '</a></li>';
            }

            if ($page >= $totalPages) {
                $html .= '<li class="page-item d-none">';
            } else {
                $html .= '<li class="page-item">';
            }

            $html .= '<a class="page-link" href="' . hyiplab_query_to_url(['pnum' => $next]) . '"><i class="las la-angle-right"></i></a>
                    </li>
                </ul>
            </nav>';
        }
        return $html;
    }

    public function filter($columns)
    {
        foreach ($columns as $columName) {
            $columns = array_keys(hyiplab_request()->all());
            if (in_array($columName, $columns) && hyiplab_request()->$columName != null) {
                $this->where($columName, 'LIKE', "%" . hyiplab_request()->$columName . "%");
            }
        }
        return $this;
    }

    public function truncate()
    {
        $table = $this->table;
        $sql = sprintf('TRUNCATE TABLE {{table_prefix}}%s', $table);
        $this->finalSql = $sql;
        DB::query($this->finalSql);
    }
}
