<?

/**
 * Created by PhpStorm.
 * User: Nk-concept
 * Date: 16/06/2017
 * Time: 19:24
 */
class WritablePersistence extends ReadablePersistence
{
    public static function insert(ReadablePersistence $Object)
    {
        $set_object_vars = new Row($Object->get_set_object_vars());
        DatabaseAccessor::connection()->open();

        $pstm = DatabaseAccessor::connection()->prepare("INSERT INTO `" . self::get_table_name() . "` (" . implode(",", $set_object_vars->toArrayByFunction(function ($c) {
                return $c->getIndex();
            })) . ") VALUES(" . implode(",", $set_object_vars->toArrayByFunction(function ($c) {
                return ":{$c->getIndex()}";
            })) . ")");
        while ($set_object_vars->hasNext()) {
            $row = $set_object_vars->next();
            $pstm->setValue($row->getIndex(), $row->getValue());
        }
        $pstm->executeUpdate();
        return DatabaseAccessor::connection()->lastInsertedId();
    }

    public function delete()
    {
        $set_object_vars = new Row($this->get_set_object_vars());
        DatabaseAccessor::connection()->open();
        $pstm = DatabaseAccessor::connection()->prepare("DELETE FROM `" . self::get_table_name() . "` " . ($set_object_vars->length() ? " WHERE " . implode(" AND ", $set_object_vars->toArrayByFunction(function ($c) {
                    return "{$c->getIndex()}=:{$c->getIndex()}";
                })) : ""));
        while ($set_object_vars->hasNext()) {
            $row = $set_object_vars->next();
            $pstm->setValue($row->getIndex(), $row->getValue());
        }
        $pstm->executeUpdate();
    }

    public function save()
    {
        DatabaseAccessor::connection()->open();
        $identifiers = self::get_identifiers();
        $columns_to_modify = new Row($this->get_set_object_vars());

        if (!($id_type = self::check_identifiers())) throw new Exception("Missing identifiers.");

        $SQL = "UPDATE `" . self::get_table_name() . "`";

        if( !$columns_to_modify->length() ) return;

        $SQL .= " SET " . implode(" , ", $columns_to_modify->toArrayByFunction(function (Column $column) {
                return "`{$column->getIndex()}`=:{$column->getIndex()}";
            }));

        if ($id_type == 1) {
            $indexes = new Row(array_intersect_key($this->saved_object_vars->toArray(), $identifiers->getRow("unique")->toArray()));
        } else {
            $indexes = new Row(array_intersect_key($this->saved_object_vars->toArray(), $identifiers->getRow("primary")->toArray()));
        }
        $where_clause_columns = $indexes->toArrayByFunction(function (Column $column) {
            return "`{$column->getIndex()}`=:db_{$column->getIndex()}";
        });
        $SQL .= " WHERE " . implode(" AND ", $where_clause_columns);
        $pstm = DatabaseAccessor::connection()->prepare($SQL);
        while ($indexes->hasNext()) {
            $index = $indexes->next();
            $pstm->setValue("db_" . $index->getIndex(), $this->get_saved_object_vars()->getColumn($index->getIndex())->getValue());
        }
        $columns_to_modify->reset();
        while ($columns_to_modify->hasNext()) {
            $var = $columns_to_modify->next();
            $pstm->setValue($var->getIndex(), $this->{$var->getIndex()});
        }
        $pstm->executeUpdate();
    }
}