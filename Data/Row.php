<?

/**
 * Created by Nkconcept.
 * Date: 14/05/2015
 * Time: 11:14
 * Updated : 02/07/2017
 * Time : 02:18
 */
class Row
{

    /**
     * @var null
     */
    /**
     * @var array|null
     */
    private $index, $columns=array();
    /**
     * @var int
     */
    private $_internal_pointer = -1;

    /**
     * Row constructor.
     * @param array $columns
     * @param null $index
     */
    public function __construct(array $columns = array(), $index = null)
    {
        $this->index = $index;
        foreach($columns As $index=>$value) $this->addColumn(new Column($index,$value));
    }

    /**
     * @return array|null
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * @param $index
     * @return bool
     */
    public function indexExists($index)
    {
        return array_key_exists($index, $this->columns);
    }

    /**
     * @param Column $column
     * @throws Exception
     */
    public function addColumn(Column $column)
    {
        if ($column->getIndex() === null) throw new Exception("Column index needed");
        if ($this->indexExists($column->getIndex())) throw new Exception("Index already exists {$column->getIndex()}.");
        $this->columns[$column->getIndex()] = $column;
    }

    /**
     * @param Column $column
     */
    public function push(Column $column)
    {
        $this->columns[] = $column;
    }

    /**
     * @param callable $function
     */
    public function indexing(callable $function)
    {
        $current_internal_pointer=$this->getInternalPointer();
        $this->reset();
        $copy = $this->copy();
        $this->clear();
        while ($copy->hasNext()) {
            $row = $copy->next();
            $this->addColumn(new Column($row->toArray(), call_user_func($function, $row)));
        }$this->setInternalPointer($current_internal_pointer);
    }

    /**
     * @return int
     */
    public function length()
    {
        return count($this->columns);
    }

    /**
     * @param $order
     * @return Column
     * @throws Exception
     */
    public function getColumnAt($order)
    {
        if ($this->length() < $order) throw new Exception("No column for the requested order.");
        else {
            $column = array_slice($this->columns, $order - 1, 1);
            return $column[key($column)];
        }
    }

    /**
     * @param $index
     * @return Column
     * @throws Exception
     */
    public function getColumn($index)
    {
        if ($this->indexExists($index)) return $this->columns[$index];
        else throw new Exception("Index does not exists");
    }

    /**
     * @return int
     */
    public function getInternalPointer()
    {
        return $this->_internal_pointer;
    }

    /**
     * @param int $internal_pointer
     */
    public function setInternalPointer($internal_pointer)
    {
        $this->_internal_pointer = $internal_pointer;
    }

    /**
     * @return bool
     */
    public function hasNext()
    {
        return ($this->length() - 1 - $this->_internal_pointer) > 0;
    }

    /**
     * @return bool
     */
    public function hasPrevious()
    {
        return $this->_internal_pointer > 0;
    }

    /**
     * @return Column
     * @throws Exception
     */
    public function random()
    {
        if ($this->length()) return array_rand($this->columns);
        else throw new Exception("Row is empty");
    }

    /**
     * @return bool|Column
     */
    public function next()
    {
        if ($this->hasNext()) return $this->getColumnAt(++$this->_internal_pointer + 1);
        else return false;
    }

    /**
     * @return bool|Column
     */
    public function previous()
    {
        if ($this->hasPrevious()) return $this->getColumnAt(--$this->_internal_pointer + 1);
        else return false;
    }

    /**
     * @return Column
     * @throws Exception
     */
    public function first()
    {
        if ($this->length() < 1) throw new Exception("Row is empty");
        else return $this->getColumnAt(1);
    }

    /**
     * @return Column
     * @throws Exception
     */
    public function last()
    {
        if ($this->length() < 1) throw new Exception("Row is empty");
        else return $this->getColumnAt($this->length());
    }

    /**
     *
     */
    public function toLast()
    {
        $this->_internal_pointer = $this->length();
    }

    /**
     * @return Row
     */
    public function copy()
    {
        return new Row($this->toArray());
    }

    /**
     * @param $function
     * @return array
     */
    public function toArrayByFunction($function)
    {
        $data = array();
        $current_internal_pointer=$this->getInternalPointer();
        $this->reset();
        while ($this->hasNext()) {
            $data[] = call_user_func($function, $this->next());
        }$this->setInternalPointer($current_internal_pointer);
        return $data;
    }

    /**
     * @param $function
     * @param $value
     * @return Row
     */
    public function find($function, $value)
    {
        $Row = new Row();
        $current_internal_pointer=$this->getInternalPointer();
        $this->reset();
        while ($this->hasNext()) {
            $column = $this->next();
            if (call_user_func($function, $column) == $value) {
                $Row->addColumn($column);
            }
        }$this->setInternalPointer($current_internal_pointer);
        return $Row;
    }

    /**
     * @param $function
     * @param Column $comparator
     * @return Row
     */
    public function search($function, Column $comparator)
    {
        $Row = new Row();
        $current_internal_pointer=$this->getInternalPointer();
        $this->reset();
        while ($this->hasNext()) {
            $column = $this->next();
            if (call_user_func($function, $comparator, $column)) {
                $Row->addColumn($column);
            }
        }$this->setInternalPointer($current_internal_pointer);
        return $Row;
    }

    /**
     * @param $function
     */
    public function each($function)
    {
        $current_internal_pointer=$this->getInternalPointer();
        $this->reset();
        while ($this->hasNext()) {
            $column = $this->next();
            call_user_func($function, $column);
        }$this->setInternalPointer($current_internal_pointer);
    }

    /**
     * @param $function
     * @return $this
     */
    public function orderByFunction($function)
    {
        foreach ($this->columns As $i => $i_row) {
            foreach ($this->columns As $j => $j_row) {
                if (call_user_func($function, $this->columns[$i], $this->columns[$j]) == -1) {
                    $temp = $this->columns[$i];
                    $this->columns[$i] = $this->columns[$j];
                    $this->columns[$j] = $temp;
                }
            }
        }
        return $this;
    }

    /**
     * @param array $columns
     * @internal param array $rows
     * @internal param array $Rows
     */
    public function addColumns(array $columns = array())
    {
        foreach ($columns As $index => $column) {
            $this->addColumn($column);
        }
    }

    /**
     * @param Row $row
     * @internal param Table $table
     */
    public function merge(Row $row)
    {
        $this->addColumns($row->toArray());
    }

    /**
     *
     */
    public function reset()
    {
        $this->_internal_pointer = -1;
    }

    /**
     * @return array|null
     */
    public function toArray()
    {
        $array=array();
        $current_internal_pointer=$this->getInternalPointer();
        $this->reset();
        while ($this->hasNext()) {
            $column = $this->next();
            $array[$column->getIndex()]=$column->getValue();
        }
        $this->setInternalPointer($current_internal_pointer);
        return $array;
    }

    /**
     *
     */
    public function clear()
    {
        $this->columns = array();
    }

    /**
     * @param $index
     */
    public function removeColumn($index)
    {
        if ($this->indexExists($index)) {
            unset($this->columns[$index]);
            if (array_search($index, array_keys($this->columns)) <= $this->_internal_pointer) {
                --$this->_internal_pointer;
            }
        }
    }

}