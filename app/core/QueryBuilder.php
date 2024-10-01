<?php
    namespace app\core;
    use \PDO;
    use \PDOException;
    use app\core\Registry;
    use app\core\AppException;

    class QueryBuilder{
        private $columns;
        private $from;
        private $distinct = false;
        private $joins;
        private $wheres;
        private $groups;
        private $havings;
        private $orders;
        private $limit;
        private $offset;

        public function __construct($tableName)
        {
            $this->from = $tableName;
            echo $this->from;
        }

        public static function table($tableName){
            return new self($tableName);
        }

        public function select($columns){
            $this->columns = is_array($columns) ? $columns : func_get_args();
            return $this;
        }

        public function distinct(){
            $this->distinct = true;
            return $this;
        }

        public function join($table,$first,$operator,$second,$type='inner') {
            $this->joins[] = [$table,$first,$operator,$second,$type];
            return $this;
        }

        public function leftJoin($table,$first,$operator,$second){
            return $this->join($table,$first,$operator,$second,'left');
        }

        public function rightJoin($table,$first,$operator,$second){
            return $this->join($table,$first,$operator,$second,'right');
        }

        public function where($column,$operator,$value,$boolean = 'and'){
            $this->wheres[] = [$column,$operator,$value,$boolean];
            return $this;
        }

        public function orWhere($column,$operator,$value){
            return $this->where($column,$operator,$value,'or');
        }

        public function groupBy($columns){
            $this->groups = is_array($columns) ? $columns : func_get_args();
            return $this;
        }

        public function having($column,$operator,$value,$boolean = 'and'){
            $this->havings[] = [$column,$operator,$value,$boolean];
            return $this;
        }

        public function orHaving($column,$operator,$value){
            return $this->having($column,$operator,$value,'or');
        }

        public function orderBy($column,$direction='asc'){
            $this->orders[] = [$column,$direction];
            return $this;
        }

        public function limit($limit){
            $this->limit = $limit;
            return $this;
        }

        public function offset($offset){
            $this->offset = $offset;
            return $this;
        }

        public function get(){
            if(!isset($this->from) || empty($this->from)){
                return false;
            }

            $sql = $this->distinct ? 'SELECT DISTINCT' : 'SELECT';

            if(isset($this->columns) && is_array($this->columns)){
                $sql .= ' '.implode(' ,',$this->columns);
            }else{
                $sql .= ' *';
            }

            $sql .= ' FROM '.$this->from;

            if(isset($this->joins) && is_array($this->joins)){
                foreach($this->joins as $join){
                    switch(strtolower($join[4])){
                        case 'inner':
                            $sql .= ' INNER JOIN ';
                            break;
                        case 'left':
                            $sql .= ' LEFT JOIN ';
                            break;
                        case 'right':
                            $sql .= ' RIGHT JOIN ';
                            break;
                        default:
                            $sql .= ' INNER JOIN ';
                            break;
                    }
                    $sql .= "$join[0] ON $join[1] $join[2] $join[3]";
                }
            }
            if(isset($this->wheres) && is_array($this->wheres)){
                $sql .= ' WHERE ';
                foreach ($this->wheres as $wk => $where){
                    $sql .= "$where[0] $where[1] $where[2]";
                    if($wk < count($this->wheres)-1){
                        $sql .= (strtolower($where[3]) === 'and') ? ' AND' : ' OR';
                    }
                }
            }
            if(isset($this->groups) && is_array($this->groups)){
                $sql .= ' GROUP BY'.implode(' ,',$this->groups);
            }
            if(isset($this->havings) && is_array($this->havings)){
                $sql .= ' HAVING ';
                foreach ($this->havings as $hk => $having){
                    $sql .= "$having[0] $having[1] $having[2]";
                    if($wk < count($this->havings)-1){
                        $sql .= (strtolower($having[3]) === 'and') ? ' AND' : ' OR';
                    }
                }
            }
            if(isset($this->orders) && is_array($this->orders)){
                $sql .= ' ORDER BY';
                foreach($this->orders as $ok => $order){
                    $sql .= "$order[0] $order[1]";
                    if($ok < (count($this->orders)-1)){
                        $sql .= " ,";
                    }
                }
            }

            if(isset($this->limit)){
                $sql .= " LIMIT $this->limit";
            }

            if(isset($this->offset)){
                $sql .= " OFFSET $this->offset";
            }
            return $sql;
        }
    }
?>