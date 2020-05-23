<?php
/**
 * Developer: ONUR KAYA
 * Contact: empatisoft@gmail.com
 */

namespace Empatisoft;
use PDO;
use PDOException;

class EmpatiDB extends EmpatiDBCommon {

    /**
     * @return int
     * Son eklenen verinin birincil anahtar değerini döndürür.
     */
    public function lastId() {
        return (int)$this->db->lastInsertId();
    }

    /**
     * @param string $data
     * @param int $fetch
     * @return array|mixed|string
     *
     * Veritabanından veri çekme işleminde kullanılır, son parametre olarak kullanılmalıdır.
     */
    public function get($data = 'all', $fetch = PDO::FETCH_OBJ) {
        return $this->fetch($this->select.$this->table.$this->join.$this->where.$this->group.$this->order.$this->limit, $data, $fetch);
    }

    /**
     * @param $primaryKey
     * @param string $param
     * @return int|null
     *
     * Belirtilen kriterlere göre sonraki verinin birincil anahtar değerini döndürür.
     */
    public function nextId($primaryKey, $param = 'current') {

        $primaryKey = $this->quote($primaryKey);
        $data = $this->fetch("SELECT MIN($primaryKey) AS id $this->table$this->join$this->where AND $primaryKey > :$param$this->group$this->order LIMIT 1", 'one');

        return $data != NULL ? (int)$data->id : NULL;
    }

    /**
     * @param $primaryKey
     * @param string $param
     * @return int|null
     *
     * Belirtilen kriterlere göre önceki verinin birincil anahtar değerini döndürür.
     */
    public function prevId($primaryKey, $param = 'current') {

        $primaryKey = $this->quote($primaryKey);
        $data = $this->fetch("SELECT MAX($primaryKey) AS id $this->table$this->join$this->where AND $primaryKey < :$param$this->group$this->order LIMIT 1", 'one');

        return $data != NULL ? (int)$data->id : NULL;
    }

    /**
     * @param $primaryKey
     * @return int
     *
     * Belirtilen kriterlere göre toplam veri sayısını döndürür.
     */
    public function total($primaryKey) {
        $primaryKey = $this->quote($primaryKey);
        $data = $this->fetch("SELECT COUNT($primaryKey) AS total $this->table$this->join$this->where$this->group", 'one');
        $total = $data != NULL ? $data->total : 0;
        return (int)$total;
    }

    /**
     * @param $table
     * @param array $data
     * @param bool $returnLastId
     * @return bool|int
     * @throws Exception
     *
     * Veri eklemek için kullanılabilir.
     */
    public function insert($table, $data = [], $returnLastId = false) {
        try {
            $values_string = "";
            $colums_string = "";
            $value_count = 0;
            foreach ($data as $key => $value)
            {
                if($value_count > 0) {
                    $colums_string .= ', ';
                    $values_string .= ', ';
                }
                $colums_string .= $this->quote($key);
                $values_string .= ':'.$key;
                $value_count++;
            }
            $query = $this->db->prepare('INSERT INTO ' . $this->quote($table) .' ('.$colums_string.') VALUES ('.$values_string.')');
            foreach ($data as $key => &$value)
            {
                $query->bindParam(':'.$key, $value);
            }
            $insert = $query->execute();

            return $returnLastId == true ? (int)$this->lastId() : $insert;

        } catch(PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @param $table
     * @param array $data
     * @return bool
     * @throws Exception
     *
     * Veri düzenleme işlemi
     */
    public function update($table, $data = [])
    {
        try {
            $values_string = "";
            $value_count = 0;
            foreach ($data as $key => $value)
            {
                $values_string = $value_count == 0 ? $this->quote($key) . ' = :' . $key : $values_string . ', ' . $this->quote($key) . ' = :' . $key;
                $value_count++;
            }

            $query = $this->db->prepare('UPDATE ' . $this->quote($table) .' SET '.$values_string . $this->where. '');
            foreach ($data as $key => &$value)
            {
                $query->bindValue(':'.$key, $value);
            }

            if(!empty($this->params)) {
                foreach ($this->params as $param) {
                    $key = isset($param[0]) ? $param[0] : NULL;
                    $value = isset($param[1]) ? $param[1] : NULL;
                    $type = isset($param[2]) ? $param[2] : PDO::PARAM_STR;
                    if($key != NULL && $value != NULL)
                        $query->bindValue(":$key", $value, $type);

                    unset($key, $value, $type);
                }
            }
            return $query->execute();
        } catch(PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @param $table
     * @return bool
     * @throws Exception
     *
     * Veri silme işlemi
     */
    public function delete($table)
    {
        try {
            $query = $this->db->prepare('DELETE FROM ' . $this->quote($table).$this->where);
            if(!empty($this->params)) {
                foreach ($this->params as $param) {
                    $key = isset($param[0]) ? $param[0] : NULL;
                    $value = isset($param[1]) ? $param[1] : NULL;
                    $type = isset($param[2]) ? $param[2] : PDO::PARAM_STR;
                    if($key != NULL && $value != NULL)
                        $query->bindValue(":$key", $value, $type);

                    unset($key, $value, $type);
                }
            }
            return $query->execute();
        } catch(PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }

}
