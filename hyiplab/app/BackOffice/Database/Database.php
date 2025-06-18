<?php

namespace Hyiplab\BackOffice\Database;

class Database
{
    public $table_prefix;
    public $wpdb;

    public function __construct()
    {
        global $table_prefix, $wpdb;
        $this->table_prefix = $table_prefix;
        $this->wpdb = $wpdb;
    }

    public function tablePrefix()
    {
        return $this->table_prefix;
    }

    public function wpdb()
    {
        return $this->wpdb;
    }

    public function execute($sql)
    {
        $sql = $this->setPrefix($sql);
        $result = $this->wpdb->get_results($sql);
        $this->throwException();
        return $result;
    }

    public function getRow($sql)
    {
        $sql = $this->setPrefix($sql);
        $result = $this->wpdb->get_row($sql);
        $this->throwException();
        return $result;
    }

    public function getVar($sql)
    {
        $sql = $this->setPrefix($sql);
        $result = $this->wpdb->get_var($sql);
        $this->throwException();
        return $result;
    }

    public function query($sql)
    {
        $sql = $this->setPrefix($sql);
        $this->wpdb->query($sql);
        $this->throwException();
        return $this->wpdb->insert_id;
    }

    private function setPrefix($sql)
    {
        return str_replace("{{table_prefix}}",$this->table_prefix,$sql);
    }

    private function throwException()
    {
        if ($this->wpdb->last_error) {
            throw new \Exception($this->wpdb->last_error);
        }
    }
}