<?php
namespace ZfCompat\Db\Table\Feature;

use Zend\Db\TableGateway\Feature\AbstractFeature;
use Zend\Db\Sql\Insert;
use Zend\Db\Sql\Update;
use Zend\Db\Sql\Expression;

class EmptyToDefault extends AbstractFeature
{
    private $_columnNames = array();

    /**
     * @param string|string[] $columns
     */
    function __construct($columns)
    {
        if (is_string($columns)) $columns = array($columns);
        $this->_columnNames = $columns;
    }

    /**
     * @param Insert $insert
     * @return Insert
     */
    public function preInsert(Insert $insert)
    {
        foreach ($this->_columnNames as $col) {
            if (isset($insert->$col) && $insert->$col === '')  {
                $insert->$col = new Expression('DEFAULT');
            }
        }
        return $insert;
    }

    public function preUpdate(Update $update)
    {
        $params = $update->getRawState('set');
        $replace = array();
        foreach ($this->_columnNames as $col) {
            if (array_key_exists($col, $params) && $params[$col] === '')  {
                $replace[$col] = new Expression('DEFAULT');
            }
        }
        if ($replace !== array()) $update->set($replace, $update::VALUES_MERGE);
        return $update;
    }
}