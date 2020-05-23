<?php
/**
 * Developer: ONUR KAYA
 * Contact: empatisoft@gmail.com
 */

namespace Empatisoft;
use PDO;
use PDOException;

class EmpatiDBCommon {

    /** PDO nesnesi */
    protected $db;

    /**
     * Sorgu içinde bindValue tarafından işlenecek olan dışarıdan gönderilen değerleri tutar.
     * Dizi olarak gönderilmelidir. 3 adet değer alır.
     * 1: Parametre adı
     * 2: Parametre değeri
     * 3: İsteğe bağlı. Değer türü. Varsayılan olarak PDO::PARAM_STR
     * Örnek: ->params([['is_published', '1', PDO::PARAM_INT], ['url', 'hakkimizda']])
     */
    protected $params = [];

    /**
     * Veritabanından çekilecek olan sütun tanımlarını tutar.
     * Örnek: ->select('title, content, image')
     * Tablo takma ismi ile birlikte de kullanılabilir.
     * Örnek: ->select('c.title, c.content, c.image')
     */
    protected $select;

    /**
     * Verinin çekileceği tabloyu tutar.
     * Örnek: ->from('contents')
     * Tablo takma ismi ile birlikte de kullanılabilir.
     * Örnek: ->from('contents', 'c')
     */
    protected $table;

    /**
     * Sayfalama yapılırken verileri limitlerken kullanılır.
     * Örnek: ->limit(limit, offset)
     */
    protected $limit;

    /**
     * Tabloları birleştirmek için kullanılacak sorguyu tutar.
     * Örnek: ->join('contents_strings', 's', ['c.content_id', 's.content_id'])
     * 4 adet parametre alır.
     * 1: Tablo adı
     * 2: Tablo takma adı
     * 3: Koşul. Dizi olarak belirtilir. Örnek: ['c.content_id', 's.content_id', '='].
     * Dizi içindeki 3. parametre zorunlu değil. Koşul eşitliğindeki operatörü belirtir.
     * 4: Birleştirme türü: INNER, LEFT, RIGHT... Zorunlu değil. Varsayılan olarak INNER
     */
    protected $join;

    /**
     * Koşulları tutar.
     * Örnek: ->where('c.is_published', 'is_published')->where('s.url', 'url')
     * 5 adet parametre alabilir.
     * 1: Sütun adı
     * 2: bind edilecek anahtar kelime. Zorunlu değil. Belirtilmezse sütun adını kullanır.
     * 3: Operatör. =, <, >, != vb... Zorunlu değil. Varsayılan =
     * 4: Bir önceki koşul ile nasıl bağlanacağı. AND, OR. Varsayılan AND
     * 5: Parantez gruplandırması. Zorunlu değil, belirtilmediyse gruplandırılmaz. "start" değeri parantez grubunun
     * bu koşul ile başlayacağını "end" değeri ise bu koşul ile sonlandırılacağını belirtir.
     */
    protected $whereCount = 0;
    protected $where;

    /**
     * Sorgu gruplandırılacak ise kullanılır.
     * Örnek: ->group('group_key')
     */
    protected $group;

    /**
     * Sıralama yapılacaksa kullanılır.
     * Örnek: ->order('s.title', 'asc')
     * 2 adet parametre alır.
     * 1: Sütun adı
     * 2: Sıralama türü. Zorunlu değil. Varsayılan "asc"
     */
    protected $order;
    protected $orderCount = 0;

    public function __construct()
    {
        $db = null;
        if ($db === null) {
            try
            {
                $dsn = 'mysql:host='.DB_SERVER.';dbname='.DB_NAME.';port='.DB_PORT.';charset='.DB_CHARSET;
                $db = new PDO($dsn, DB_USERNAME, DB_PASSWORD);
                $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
            }
            catch (PDOException $e)
            {
                echo '<pre>';
                print_r($e->getMessage());
                echo '</pre>';
                die;
            }
        }
        $this->db = $db;
    }

    /**
     * @param array $params
     * @return $this
     *
     * Açıklama 12. satırda yer almaktadır.
     */
    public function params($params = []) {
        if(!empty($params)) {
            foreach ($params as $param) {
                $key = isset($param[0]) ? $param[0] : NULL;
                $value = isset($param[1]) ? $param[1] : NULL;
                $type = isset($param[2]) ? $param[2] : PDO::PARAM_STR;

                if($key != NULL && $value != NULL)
                    $this->params[] = [$key, $value, $type];

                unset($key, $value, $type);
            }
        }
        return $this;
    }

    /**
     * @param string $columns
     * @param bool $distinct
     * @return $this
     *
     * Açıklama 22. satırda yer almaktadır.
     */
    public function select($columns = '*', $distinct = false) {
        $query = $distinct == true ? 'SELECT DISTINCT ' : 'SELECT ';
        if($columns != '*') {
            $_count = 0;
            if(!is_array($columns))
                $columns = explode(', ', $columns);

            foreach ($columns as $column) {
                if($_count > 0)
                    $query .= ', ';

                $query .= $this->quote($column);

                $_count++;
            }
        } else {
            $query .= ' * ';
        }
        $this->select = $query;
        return $this;
    }

    /**
     * @param $table
     * @param null $alias
     * @return $this
     *
     * Açıklama 30. satırda yer almaktadır.
     */
    public function from($table, $alias = NULL) {
        $_alias = $alias != NULL ? " AS `$alias` " : " ";
        $this->table = ' FROM `'.$table.'`'.$_alias;
        return $this;
    }

    /**
     * @param $limit
     * @param int $offset
     * @return $this
     *
     * Açıklama 38. satırda yer almaktadır.
     */
    public function limit($limit, $offset = 0) {
        $this->limit = " LIMIT $offset, $limit";
        return $this;
    }

    /**
     * @param $table
     * @param $alias
     * @param $on
     * @param string $type
     * @return $this
     *
     * Açıklama 44. satırda yer almaktadır.
     */
    public function join($table, $alias, $on, $type = 'INNER') {

        if(is_array($on)) {
            $string = " $type JOIN `$table` AS `$alias` ON ";
            $left = isset($on[0]) ? $on[0] : NULL;
            $right = isset($on[1]) ? $on[1] : NULL;
            $operator = isset($on[2]) ? $on[2] : '=';
            if($left != NULL && $right != NULL)
                $string .= $this->quote($left)." $operator ".$this->quote($right);

            unset($left, $right, $operator);

            $this->join .= $string;
        }

        return $this;
    }

    /**
     * @param $column
     * @param null $key
     * @param string $operator
     * @param string $condition
     * @param null $group
     * @return $this
     *
     * Açıklama 56. satırda yer almaktadır.
     */
    public function where($column, $key = NULL, $operator = '=', $condition = 'AND', $group = NULL) {
        $key = $key != NULL ? $key : $column;
        $string = $this->whereCount == 0 ? ' WHERE ' : ' ';
        if($group == 'start')
            $string .= '(';

        $string .= $this->whereCount > 0 ? ' '.$condition.' ' : '';
        $string .= $this->quote($column)." $operator :$key";

        if($group == 'end')
            $string .= ')';

        $this->where .= $string;
        $this->whereCount++;

        return $this;
    }

    /**
     * @param $column
     * @param null $key
     * @param int $count
     * @param string $operator
     * @param string $condition
     * @param null $group
     * @return $this
     *
     * WHERE IN sorgusu için kullanılır.
     * Örnek: ->in('c.is_published', 'is_published')
     * 6 adet parametre alabilir.
     * 1: Sütun adı
     * 2: bind edilecek anahtar kelime. Zorunlu değil. Belirtilmezse sütun adını kullanır.
     * 3: IN içine kaç adet değer gönderildiği. Zorunlu değil. Varsayılan değer 1.
     * 4: Operatör. "" ve "!" değerlerini alır. Zorunlu değil. Varsayılan boştur. NOT IN için "!"
     * 5: Bir önceki koşul ile nasıl bağlanacağı. AND, OR. Varsayılan AND
     * 6: Parantez gruplandırması. Zorunlu değil, belirtilmediyse gruplandırılmaz. "start" değeri parantez grubunun
     * bu koşul ile başlayacağını "end" değeri ise bu koşul ile sonlandırılacağını belirtir.
     */
    public function in($column, $key = NULL, $count = 1, $operator = '', $condition = 'AND', $group = NULL) {
        $key = $key != NULL ? $key : $column;
        $string = $this->whereCount == 0 ? ' WHERE ' : ' ';
        if($group == 'start')
            $string .= '(';

        $operator = $operator == "!" ? 'NOT IN' : 'IN';

        $string .= $this->whereCount > 0 ? ' '.$condition.' ' : '';
        $string .= $this->quote($column).' '.$operator.' (';

        $i_count = 0;
        for ($i = 1; $i<=$count; $i++) {
            $string .= $i_count > 0 ? ', ' : '';
            $string .= ':'.$key.'_'.$i;
            $i_count++;
        }
        $string .= ')';

        if($group == 'end')
            $string .= ')';

        $this->where .= $string;
        $this->whereCount++;

        return $this;
    }

    /**
     * @param $column
     * @param null $key
     * @param string $condition
     * @param null $group
     * @return $this
     *
     * FIND_IN_SET metodunu çalıştırır.
     * Örnek: ->find('c.parent_id', 'parent_id')
     * 4 adet parametre alabilir.
     * 1: Sütun adı
     * 2: bind edilecek anahtar kelime. Zorunlu değil. Belirtilmezse sütun adını kullanır.
     * 3: Bir önceki koşul ile nasıl bağlanacağı. AND, OR. Varsayılan AND
     * 4: Parantez gruplandırması. Zorunlu değil, belirtilmediyse gruplandırılmaz. "start" değeri parantez grubunun
     * bu koşul ile başlayacağını "end" değeri ise bu koşul ile sonlandırılacağını belirtir.
     */
    public function find($column, $key = NULL, $condition = 'AND', $group = NULL) {
        $key = $key != NULL ? $key : $column;
        $string = $this->whereCount == 0 ? ' WHERE ' : ' ';
        if($group == 'start')
            $string .= '(';

        $string .= $this->whereCount > 0 ? ''.$condition.' ' : '';
        $string .= 'FIND_IN_SET('.$this->quote($column).', :'.$key.')';

        if($group == 'end')
            $string .= ')';

        $this->where .= $string;
        $this->whereCount++;

        return $this;
    }

    /**
     * @param $column
     * @param string $start
     * @param string $end
     * @param string $operator
     * @param string $condition
     * @param null $group
     * @return $this
     *
     * Değer aralıklarını bulur.
     * Örnek: ->between('c.published_date')
     * 6 adet parametre alabilir.
     * 1: Sütun adı
     * 2: Aralıktaki ilk değer için anahtar kelime. Varsayılan "start"
     * 3: Aralıktaki son değer için anahtar kelime. Varsayılan "end"
     * 4: Operatör. "=" ve "!" değerlerini alır. Zorunlu değil. Varsayılan "=". NOT BETWEEN için "!"
     * 5: Bir önceki koşul ile nasıl bağlanacağı. AND, OR. Varsayılan AND
     * 6: Parantez gruplandırması. Zorunlu değil, belirtilmediyse gruplandırılmaz. "start" değeri parantez grubunun
     * bu koşul ile başlayacağını "end" değeri ise bu koşul ile sonlandırılacağını belirtir.
     */
    public function between($column, $start = 'start', $end = 'end', $operator = '=', $condition = 'AND', $group = NULL) {
        $string = $this->whereCount == 0 ? ' WHERE ' : ' ';
        if($group == 'start')
            $string .= '(';

        $operator = $operator == "!" ? 'NOT BETWEEN' : 'BETWEEN';
        $string .= $this->whereCount > 0 ? ' '.$condition.' ' : '';
        $string .= $this->quote($column)." $operator :$start AND :$end";

        if($group == 'end')
            $string .= ')';

        $this->where .= $string;
        $this->whereCount++;

        return $this;
    }

    /**
     * @param $column
     * @param null $key
     * @param string $operator
     * @param string $condition
     * @param null $group
     * @return $this
     *
     * Örnek: ->is('c.is_published', 'is_published')
     * 5 adet parametre alabilir.
     * 1: Sütun adı
     * 2: Bind edilecek anahtar kelime. Varsayılan: "sütun adı"
     * 3: Operatör. "=" ve "!" değerlerini alır. Zorunlu değil. Varsayılan "=". NOT BETWEEN için "!"
     * 4: Bir önceki koşul ile nasıl bağlanacağı. AND, OR. Varsayılan AND
     * 5: Parantez gruplandırması. Zorunlu değil, belirtilmediyse gruplandırılmaz. "start" değeri parantez grubunun
     * bu koşul ile başlayacağını "end" değeri ise bu koşul ile sonlandırılacağını belirtir.
     */
    public function is($column, $key = NULL, $operator = '=', $condition = 'AND', $group = NULL) {
        $key = $key != NULL ? $key : $column;
        $string = $this->whereCount == 0 ? ' WHERE ' : ' ';
        if($group == 'start')
            $string .= '(';

        $operator = $operator == "!" ? 'IS NOT' : 'IS';

        $string .= $this->whereCount > 0 ? ' '.$condition.' ' : '';
        $string .= $this->quote($column).' '.$operator.' :'.$key.'';

        if($group == 'end')
            $string .= ')';

        $this->where .= $string;
        $this->whereCount++;

        return $this;
    }

    /**
     * @param $column
     * @param null $key
     * @param string $operator
     * @param string $condition
     * @param null $group
     * @return $this
     *
     * Örnek: ->like('c.is_published', 'is_published')
     * 5 adet parametre alabilir.
     * 1: Sütun adı
     * 2: Bind edilecek anahtar kelime. Varsayılan: "sütun adı"
     * 3: Operatör. "=" ve "!" değerlerini alır. Zorunlu değil. Varsayılan "=". NOT LIKE için "!"
     * 4: Bir önceki koşul ile nasıl bağlanacağı. AND, OR. Varsayılan AND
     * 5: Parantez gruplandırması. Zorunlu değil, belirtilmediyse gruplandırılmaz. "start" değeri parantez grubunun
     * bu koşul ile başlayacağını "end" değeri ise bu koşul ile sonlandırılacağını belirtir.
     */
    public function like($column, $key = NULL, $operator = '=', $condition = 'AND', $group = NULL) {
        $key = $key != NULL ? $key : $column;
        $string = $this->whereCount == 0 ? ' WHERE ' : ' ';
        if($group == 'start')
            $string .= '(';

        $string .= $this->whereCount > 0 ? ' '.$condition.' ' : '';

        $operator = $operator == "!" ? 'NOT LIKE' : 'LIKE';
        $string .= $this->quote($column)." $operator :$key";

        if($group == 'end')
            $string .= ')';

        $this->where .= $string;
        $this->whereCount++;

        return $this;
    }

    /**
     * @param $key
     * @return $this
     *
     * Açıklama 70. satırda yer almaktadır.
     */
    public function group($key) {
        $this->group = ' GROUP BY '.$this->quote($key);
        return $this;
    }

    /**
     * @param $key
     * @param string $direction
     * @return $this
     *
     * Açıklama 76. satırda yer almaktadır.
     */
    public function order($key, $direction = 'ASC') {
        $string = $this->orderCount > 0 ? ', ' : ' ORDER BY ';
        $string .= $this->quote($key).' '.$direction;
        $this->order .= $string;
        $this->orderCount++;
        return $this;
    }

    /**
     * @param $string
     * @return string
     *
     * MySQL sorgusuna tek tırnak ekler. c.title olan ifadeyi `c`.`title` yapar.
     */
    protected function quote($string) {
        $result = "`$string`";

        if (strstr($string,'.')) {
            $split = explode('.', $string);
            $table = isset($split[0]) ? $split[0] : NULL;
            $key = isset($split[1]) ? $split[1] : NULL;
            if($table != NULL && $key != NULL)
                $result = "`$table`.`$key`";

        }

        return $result;
    }

    /**
     * @param $query
     * @param string $_type
     * @param int $fetch
     * @return array|mixed|string
     *
     * Veri çekmek için kullanılır. 3 adet parametre alabilir.
     * 1: Sorgu
     * 2: Birden fazla veri çekmek için "all", tek veri çekmek için "one" kullanılır. Varsayılan: all
     * 3: Tür. Varsayılan: PDO::FETCH_OBJ
     */
    protected function fetch($query, $_type = 'all', $fetch = PDO::FETCH_OBJ)
    {
        try {
            $query = $this->db->prepare($query);
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
            $query->execute();
            return $_type == 'all' ? $query->fetchAll($fetch) : $query->fetch($fetch);

        } catch(PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function __destruct()
    {
        $this->db = null;
    }

}
