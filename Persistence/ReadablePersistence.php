<?

/**
 * Created by Nkconcept.
 * Date: 01/01/2017
 * Time: 20:27
 */
abstract class ReadablePersistence
{
    /* @var $saved_object_vars Row */
    protected $saved_object_vars;

    public function __construct()
    {
        $this->saved_object_vars = new Row();
    }

    /**
     * @param $name
     * @return string
     */
    protected final static function format($name)
    {
        return implode("_", array_map('strtolower', array_filter(preg_split('/(?=[A-Z])/', $name))));
    }

    /**
     * @param $table_name
     * @return bool
     * @throws \Exception
     */
    protected final static function is_table($table_name)
    {
        $pstm = DatabaseAccessor::connection()->open()->prepare("SELECT TABLE_NAME FROM information_schema.tables WHERE table_schema = :table_schema AND table_name = :table_name");
        $pstm->setValue("table_schema", DatabaseAccessor::getDbname());
        $pstm->setValue("table_name", $table_name);
        return $pstm->executeQuery()->hasNext();
    }

    /**
     * @return string
     */
    protected final static function get_table_name()
    {
        return self::format(static::class);
    }

    /**
     * @return Table
     * @throws \Exception
     */
    private final static function get_columns()
    {
        return DatabaseAccessor::connection()->open()->executeQuery("SHOW COLUMNS FROM `" . self::get_table_name() . "`");
    }

    /**
     * @return Table
     */
    protected final static function get_identifiers()
    {
        $columns = self::get_columns()->toArray();
        $columns = array_column($columns, "Key", "Field");
        $uniques = array_flip(array_keys($columns, "UNI"));
        $primaries = array_flip(array_keys($columns, "PRI"));
        return new Table(array(
            "unique" => $uniques,
            "primary" => $primaries
        ));
    }

    public function get_saved_object_vars()
    {
        $this->saved_object_vars->reset();
        return $this->saved_object_vars;
    }

    /**
     * @return array
     */
    protected final function get_set_object_vars()
    {
        $object_vars = array_diff_key(get_object_vars($this), get_class_vars(self::class));
        $set_object_vars = array();
        foreach ($object_vars As $object_var => $value) {
            if ($value !== null) $set_object_vars[$object_var] = $value;
        }
        return $set_object_vars;
    }

    /**
     * @return bool|int
     * @throws \Exception
     */
    protected final function check_identifiers()
    {
        $identifiers = self::get_identifiers();
        $set_object_vars = $this->get_set_object_vars();
        if (count(array_intersect_key($set_object_vars, $identifiers->getRow("unique")->toArray()))) return 1;
        if (count(array_intersect_key($set_object_vars, $identifiers->getRow("primary")->toArray())) == count($identifiers->getRow("primary")->toArray())) return 2;
        return false;
    }

    /**
     * @return Table
     * @throws \Exception
     */
    public final function get_references()
    {
        DatabaseAccessor::connection()->open();
        $pstm = DatabaseAccessor::connection()->prepare("SELECT TABLE_NAME As 'class' ,COLUMN_NAME As 'column',REFERENCED_TABLE_NAME As 'referenced_class',REFERENCED_COLUMN_NAME As 'referenced_column' FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = :TABLE_SCHEMA AND REFERENCED_TABLE_NAME!='' AND REFERENCED_COLUMN_NAME!=''");
        $pstm->setValue("TABLE_SCHEMA", DatabaseAccessor::getDbname());
        return $pstm->executeQuery();
    }

    /**
     * @throws \Exception
     */
    public function load()
    {
        DatabaseAccessor::connection()->open();
        $identifiers = self::get_identifiers();
        $set_object_vars = $this->get_set_object_vars();


        if (!($id_type = self::check_identifiers())) throw new Exception("Missing identifiers.");
        if ($id_type == 1) {
            $uniques = new Row(array_intersect_key($set_object_vars, $identifiers->getRow("unique")->toArray()));
            $pstm = DatabaseAccessor::connection()->prepare("SELECT * FROM `" . self::get_table_name() . "` WHERE " . implode(" OR ", $uniques->toArrayByFunction(function ($c) {
                    return "`{$c->getIndex()}`=:{$c->getIndex()}";
                })));
            while ($uniques->hasNext()) {
                $unique = $uniques->next();
                $pstm->setValue($unique->getIndex(), $this->{$unique->getIndex()});
            }
        } else {
            $primaries = new Row(array_intersect_key($set_object_vars, $identifiers->getRow("primary")->toArray()));
            $pstm = DatabaseAccessor::connection()->prepare("SELECT * FROM `" . self::get_table_name() . "` WHERE " . implode(" AND ", $primaries->toArrayByFunction(function ($c) {
                    return "`{$c->getIndex()}`=:{$c->getIndex()}";
                })));
            while ($primaries->hasNext()) {
                $primary = $primaries->next();
                $pstm->setValue($primary->getIndex(), $this->{$primary->getIndex()});
            }
        }
        $table = $pstm->executeQuery();
        if ($row = $table->next()) {
            $this->saved_object_vars = new Row();
            while ($row->hasNext()) {
                $column = $row->next();
                $this->set($column->getIndex(), $column->getValue());
            }
        } else throw new Exception(static::class . " Not Found");
    }

    /**
     * @param ReadablePersistence|WritablePersistence $Object
     * @return Table
     * @throws \Exception
     */
    public static function find(ReadablePersistence $Object)
    {
        if (!is_a($Object, static::class)) throw new \Exception("Object is not an instance of " . static::class);
        $Instances = new Table();
        $set_object_vars = $Object->get_set_object_vars();
        $set_object_vars = new Row($set_object_vars);
        $pstm = DatabaseAccessor::connection()->open()->prepare("SELECT * FROM `" . self::get_table_name() . "` " . ($set_object_vars->length() ? " WHERE " . implode(" AND ", $set_object_vars->toArrayByFunction(function ($c) {
                    return "`{$c->getIndex()}`=:{$c->getIndex()}";
                })) : ""));
        $class = static::class;
        while ($set_object_vars->hasNext()) {
            $column = $set_object_vars->next();

            $pstm->setValue($column->getIndex(), $column->getValue());
        }
        $table = $pstm->executeQuery();
        while ($table->hasNext()) {
            $row = $table->next();
            /* @var $Instance ReadablePersistence */
            $Instance = new $class();
            while ($row->hasNext()) {
                $column = $row->next();
                $Instance->set($column->getIndex(), $column->getValue());
            }
            $Instances->push(new Row(array($Instance), $row->getIndex()));
        }
        return $Instances;
    }

    private function set($index, $value)
    {
        $this->{$index} = $value;
        $this->saved_object_vars->addColumn(new Column($index, $value));
    }

    /**
     * @param $method_name
     * @param $args
     * @return Table
     * @throws \Exception
     */
    public static function __callStatic($method_name, $args)
    {
        if (substr($method_name, 0, 9) == "findWhere") {
            $MethodNameParts = array_filter(preg_split('/And/', substr($method_name, 9)));
            $Conditions = new Row();
            foreach ($MethodNameParts As $part) {
                $options = array("Smaller" => "<", "Bigger" => ">", "Like" => "like", "Equal" => "=");
                foreach ($options As $option => $operator) {
                    if (strstr($part, $option)) {
                        $Conditions->addColumn(new Column(self::format(str_replace($option, "", $part)), $operator));
                    }
                }
            }

            DatabaseAccessor::connection()->open();
            $pstm = DatabaseAccessor::connection()->prepare("SELECT * FROM `" . self::get_table_name() . "` WHERE " . implode(" AND ", $Conditions->toArrayByFunction(function ($c) {
                    return "{$c->getIndex()} {$c->getValue()} :{$c->getIndex()}";
                })));
            $c = 0;
            while ($Conditions->hasNext()) {
                $Condition = $Conditions->next();
                $pstm->setValue($Condition->getIndex(), $args[$c]);
                $c++;
            }
            $table = $pstm->executeQuery();
            $Instances = new Table();
            $class = static::class;
            while ($table->hasNext()) {
                $row = $table->next();
                /* @var $Instance ReadablePersistence */
                $Instance = new $class();
                while ($row->hasNext()) {
                    $column = $row->next();
                    $Instance->set($column->getIndex(), $column->getValue());
                }
                $Instances->push(new Row(array($Instance)));
            }
            return $Instances;
        } else throw new \Exception("");
    }

    /**
     * @param $method_name
     * @param $args
     * @return static|Table
     * @throws \Exception
     */
    public function __call($method_name, $args)
    {
        if (substr($method_name, 0, 3) == "get") {
            $references = $this->get_references();
            $requested_class = substr($method_name, 3);
            $Parts = explode("By", $requested_class);
            if (self::is_table(self::format($requested_class)) and $reference = $references->search(function (Row $row, Row $comparator) {
                    if ($row->getColumn("class")->getValue() != $comparator->getColumn("class")->getValue()) return false;
                    else if ($row->getColumn("referenced_class")->getValue() != $comparator->getColumn("referenced_class")->getValue()) return false;
                    else return true;
                }, new Row(array("class" => self::format(static::class), "referenced_class" => self::format($requested_class))))
            ) {
                $Instance = new $requested_class();
                $reference = $reference->next();
                $Instance->{$reference->getColumn("referenced_column")->getValue()} = $this->{$reference->getColumn("column")->getValue()};
                $Instance->load();
                return $Instance;
            } else if (
                self::is_table(self::format(substr($requested_class, 0, strlen($requested_class) - 4)))
                and
                substr($requested_class, strlen($requested_class) - 4) == "List"
                and
                $reference = $references->search(function (Row $row, Row $comparator) {
                    if ($row->getColumn("class")->getValue() != $comparator->getColumn("class")->getValue()) return false;
                    else if ($row->getColumn("referenced_class")->getValue() != $comparator->getColumn("referenced_class")->getValue()) return false;
                    else return true;
                }, new Row(array("class" => self::format(substr($requested_class, 0, strlen($requested_class) - 4)), "referenced_class" => self::format(static::class))))
            ) {
                $requested_class = substr($requested_class, 0, strlen($requested_class) - 4);
                if (is_a($args[0], $requested_class)) $Instance = $args[0];
                else $Instance = new $requested_class();
                $reference = $reference->next();
                $Instance->{$reference->getColumn("column")->getValue()} = $this->{$reference->getColumn("referenced_column")->getValue()};
                return $requested_class::find($Instance);
            } else if (
                self::is_table(self::format(substr($Parts[0], 0, strlen($Parts[0]) - 4)))
                and
                $reference = $references->search(function (Row $row, Row $comparator) {
                    if ($row->getColumn("class")->getValue() != $comparator->getColumn("class")->getValue()) return false;
                    else if ($row->getColumn("referenced_class")->getValue() != $comparator->getColumn("referenced_class")->getValue()) return false;
                    else return true;
                }, new Row(array("class" => self::format($Parts[count($Parts) - 1]), "referenced_class" => self::format(static::class))))
            ) {
                $Parts[0] = substr($Parts[0], 0, strlen($Parts[0]) - 4);
                $Parts = array_reverse($Parts);
                $method_call_name = "get{$Parts[0]}List";
                $steps[0] = $this->$method_call_name();
                for ($i = 1; $i < count($Parts); $i++) {
                    $FilterInstance = (($i == (count($Parts) - 1)) ? $args[0] : null);
                    $steps[$i] = new Table();
                    while ($steps[$i - 1]->hasNext()) {
                        if ($references->search(function (Row $row, Row $comparator) {
                            if ($row->getColumn("class")->getValue() != $comparator->getColumn("class")->getValue()) return false;
                            else if ($row->getColumn("referenced_class")->getValue() != $comparator->getColumn("referenced_class")->getValue()) return false;
                            else return true;
                        }, new Row(array("class" => self::format($Parts[$i]), "referenced_class" => self::format($Parts[$i - 1]))))
                        ) {
                            $method_call_name = "get{$Parts[$i]}List";
                            $steps[$i]->merge($steps[$i - 1]->next()->next()->getValue()->$method_call_name($args[0]));
                        } else if ($references->search(function (Row $row, Row $comparator) {
                            if ($row->getColumn("class")->getValue() != $comparator->getColumn("class")->getValue()) return false;
                            else if ($row->getColumn("referenced_class")->getValue() != $comparator->getColumn("referenced_class")->getValue()) return false;
                            else return true;
                        }, new Row(array("class" => self::format($Parts[$i - 1]), "referenced_class" => self::format($Parts[$i]))))
                        ) {
                            $method_call_name = "get{$Parts[$i]}";
                            $Instance = $steps[$i - 1]->next()->next()->getValue()->$method_call_name($args[0]);
                            if (is_a($FilterInstance, $Parts[count($Parts) - 1])) {
                                foreach (array_filter(get_object_vars($FilterInstance)) As $index => $var) {
                                    if ($var != $Instance->$index) continue 2;
                                }
                            }
                            $steps[$i]->push(new Row(array($Instance)));
                        } else throw new \Exception("{$Parts[ $i ]} Has No Relation With {$Parts[ $i-1 ]}");
                    }
                }
                return $steps[$i - 1];
            }
        }
        die("Call to undefined method $method_name.");
    }

    public static function columnValueExists($column_name, $value)
    {
        DatabaseAccessor::connection()->open();
        $pstm = DatabaseAccessor::connection()->prepare("SELECT * FROM `" . self::get_table_name() . "` WHERE `$column_name`=:$column_name");
        $pstm->setValue($column_name, $value);
        if ($pstm->executeQuery()->length()) return true;
        else return false;
    }

    public static function exists(ReadablePersistence $Object)
    {
        if (!is_a($Object, static::class)) throw new \Exception("Object is not an instance of " . static::class);
        $set_object_vars = $Object->get_set_object_vars();
        $set_object_vars = new Row($set_object_vars);
        $pstm = DatabaseAccessor::connection()->open()->prepare("SELECT count(*) FROM `" . self::get_table_name() . "` " . ($set_object_vars->length() ? " WHERE " . implode(" AND ", $set_object_vars->toArrayByFunction(function ($c) {
                    return "`{$c->getIndex()}`=:{$c->getIndex()}";
                })) : ""));
        while ($set_object_vars->hasNext()) {
            $column = $set_object_vars->next();

            $pstm->setValue($column->getIndex(), $column->getValue());
        }
        return $pstm->executeQuery()->next()->next()->getValue();
    }


}