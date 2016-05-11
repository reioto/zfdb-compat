<?php
namespace ZfCompat\Db;

use Zend\Db\Sql\Sql as ZfSql;
use Zend\Db\Sql\PreparableSqlInterface;
use Zend\Db\Sql\Select;

class Sql extends ZfSql
{
    /**
     * @return null|\Zend\Db\Adapter\Driver\ResultInterface
     */
    function fetchResult(PreparableSqlInterface $select)
    {
        $statement = $this->prepareStatementForSqlObject($select);
        return $statement->execute();
    }

    /**
     * @return array
     */
    function fetchAll(Select $select)
    {
        $data = array();
        foreach ($this->fetchResult($select) as $row) {
            $data[] = (array) $row;
        }

        return $data;
    }

    /**
     * @return false|array
     */
    function fetchRow(Select $select)
    {
        $result = $this->fetchResult($select);
        $row = $result->current();
        if (empty($row)) {
            return false;
        }else {
            return (array) $row;
        }
    }

    /**
     * @return null|string
     */
    function fetchOne(Select $select)
    {
        $result = $this->fetchResult($select);
        if ($result->valid() === false) return null;
        $row = $result->current();
        if ($row === false) return null;
        return current($row);
    }
}
