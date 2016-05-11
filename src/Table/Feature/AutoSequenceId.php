<?php
namespace ZfCompat\Db\Table\Feature;

use Zend\Db\TableGateway\Feature\AbstractFeature;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\Adapter\Driver\StatementInterface;

class AutoSequeceId extends AbstractFeature
{
    private $_sequenceName = null;

    /**
     * @param string $sequenceName
     */
    function __construct($sequenceName)
    {
        $this->_sequenceName = $sequenceName;
    }

    /**
     * @param StatementInterface $statement
     * @param ResultInterface $result
     */
    function postInsert(StatementInterface $statement, ResultInterface $result)
    {
        $connection = $this->tableGateway->getAdapter()
                                         ->getDriver()
                                         ->getConnection();
        if ($connection->getDriverName() === 'pgsql') {
            $this->tableGateway->lastInsertValue = $connection->getLastGeneratedValue($this->_sequenceName);
        }
    }


}