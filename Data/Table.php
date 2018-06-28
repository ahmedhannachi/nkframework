<?

/**
 * Created by Nkconcept.
 * Date: 14/05/2015
 * Time: 11:14
 */
class Table
{
    /**
     * @var
     */
    private $rows = array();
    /**
     * @var int
     */
    private $_internal_pointer = -1;

    /**
     * Table constructor.
     * @param array $rows
     */
    public function __construct(array $rows = array())
    {
        foreach ($rows As $index => $value) $this->addRow(new Row($value, $index));
    }

    /**
     * @param $index
     * @return bool
     */
    public function indexExists($index)
    {
        return array_key_exists($index, $this->rows);
    }

    /**
     * @param Row $row
     * @throws Exception
     */
    public function addRow(Row $row)
    {
        if ($row->getIndex() === null) throw new Exception("Row index needed");
        if ($this->indexExists($row->getIndex())) throw new Exception("Index already exists {$row->getIndex()}");
        $this->rows[$row->getIndex()] = $row;
    }

    /**
     * @param callable $function
     */
    public function indexing(callable $function)
    {
        $current_internal_pointer = $this->getInternalPointer();
        $this->reset();
        $copy = $this->copy();
        $this->clear();
        while ($copy->hasNext()) {
            $row = $copy->next();
            $this->addRow(new Row($row->toArray(), call_user_func($function, $row)));
        }
        $this->setInternalPointer($current_internal_pointer);
    }

    /**
     * @param $index
     * @return int
     * @throws Exception
     */
    public function getOrderOf($index)
    {
        if (!$this->indexExists($index)) throw new Exception("Index does not exist {$index}.");
        return array_search($index, array_keys($this->rows)) + 1;
    }

    /**
     * @param Row $row
     */
    public function push(Row $row)
    {
        $this->rows[] = $row;
    }

    /**
     * @return int
     */
    public function length()
    {
        return count($this->rows);
    }

    /**
     * @param $order
     * @return Row
     * @throws Exception
     */
    public function getRowAt($order)
    {
        $array_keys = array_keys($this->rows);
        if (!array_key_exists($order - 1, $array_keys)) throw new Exception("No row for the requested order.");
        $index = $array_keys[$order - 1];
        return $this->getRow($index);
    }

    /**
     * @param $index
     * @return Row
     * @throws Exception
     */
    public function getRow($index)
    {
        if ($this->indexExists($index)) return $this->rows[$index];
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
     * @param $internal_pointer
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
     * @return Row
     * @throws Exception
     */
    public function random()
    {
        if ($this->length()) {
            $index = array_rand($this->rows);
            return $this->rows[$index];
        } else throw new Exception("Table is empty");
    }

    /**
     * @return bool|Row
     */
    public function next()
    {
        if ($this->hasNext()) return $this->getRowAt(++$this->_internal_pointer + 1);
        else return false;
    }

    /**
     * @return bool|Row
     */
    public function previous()
    {
        if ($this->hasPrevious()) return $this->getRowAt(--$this->_internal_pointer + 1);
        else return false;
    }

    /**
     * @return Row
     * @throws Exception
     */
    public function first()
    {
        if ($this->length() < 1) throw new Exception("Table is empty");
        else return $this->getRowAt(1);
    }

    /**
     * @return Row
     * @throws Exception
     */
    public function last()
    {
        if ($this->length() < 1) throw new Exception("Table is empty");
        else return $this->getRowAt($this->length());
    }

    /**
     *
     */
    public function toLast()
    {
        $this->_internal_pointer = $this->length();
    }

    /**
     * @return Table
     */
    public function copy()
    {
        return new Table($this->toArray());
    }

    /**
     * @param array $args
     * @return array
     * @internal param $function
     */
    public function toArrayByFunction(...$args)
    {
        $data = array();
        $current_internal_pointer = $this->getInternalPointer();
        $this->reset();
        $function = $args[0];
        while ($this->hasNext()) {
            $args[0] = $this->next();
            $data[] = call_user_func_array($function, $args);
        }
        $this->setInternalPointer($current_internal_pointer);
        return $data;
    }

    /**
     * @param array ...$args
     */
    public function each(...$args)
    {
        $current_internal_pointer = $this->getInternalPointer();
        $this->reset();
        $function = $args[0];
        while ($this->hasNext()) {
            $args[0] = $this->next();
            call_user_func_array($function, $args);
        }
        $this->setInternalPointer($current_internal_pointer);
    }

    /**
     * @param $function
     * @param $value
     * @return Table
     */
    public function find($function, $value)
    {
        $Table = new Table();
        $current_internal_pointer = $this->getInternalPointer();
        $this->reset();
        while ($this->hasNext()) {
            $row = $this->next();
            if (call_user_func($function, $row) == $value) {
                $Table->addRow($row);
            }
        }
        $this->setInternalPointer($current_internal_pointer);

        return $Table;
    }

    /**
     * @param $function
     * @param $value
     * @return bool|Row
     */
    public function findOne($function, $value)
    {
        $current_internal_pointer = $this->getInternalPointer();
        $this->reset();
        while ($this->hasNext()) {
            $row = $this->next();
            if (call_user_func($function, $row, $value)) {
                return $row;
            }
        }
        $this->setInternalPointer($current_internal_pointer);
        return false;
    }

    /**
     * @param $function
     * @param $comparator
     * @return Table
     * @throws Exception
     */
    public function search($function,$comparator)
    {
        $Table = new Table();
        $current_internal_pointer = $this->getInternalPointer();
        $this->reset();
        while ($this->hasNext()) {
            $row = $this->next();
            if (call_user_func($function, $row, $comparator)) {
                $Table->addRow($row);
            }
        }
        $this->setInternalPointer($current_internal_pointer);

        return $Table;
    }

    /**
     * @param $function
     */
    public function filter($function)
    {
        $this->reset();
        while ($this->hasNext()) {
            $row = $this->next();
            if (!call_user_func($function, $row)) {
                $this->removeRow($row->getIndex());
            };
        }
    }

    /**
     * @param $function
     * @return $this
     */
    public function orderByFunction($function)
    {
        $this->reset();
        uasort($this->rows,$function);
        return $this;
    }

    /**
     * @param array $rows
     * @internal param array $Rows
     */
    public function addRows(array $rows = array())
    {
        foreach ($rows As $index => $row) {
            $this->addRow($row);
        }
    }

    /**
     * @param Table $table
     */
    public function merge(Table $table)
    {
        $this->addRows($table->toArray());
    }

    /**
     *
     */
    public function reset()
    {
        $this->_internal_pointer = -1;
        foreach ($this->rows As $row)
            $row->reset();
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $array = array();
        $current_internal_pointer = $this->getInternalPointer();
        $this->reset();
        while ($this->hasNext()) {
            $row = $this->next();
            $array[$row->getIndex()] = $row->toArray();
        }
        $this->setInternalPointer($current_internal_pointer);

        return $array;
    }

    /**
     *
     */
    public function clear()
    {
        $this->rows = array();
    }

    /**
     * @param $index
     */
    public function removeRow($index)
    {
        if ($this->indexExists($index)) {
            if (array_search($index, array_keys($this->rows)) <= $this->_internal_pointer) {
                --$this->_internal_pointer;
            }
            unset($this->rows[$index]);
        }
    }

}