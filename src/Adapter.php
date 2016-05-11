<?php
namespace ZfCompat\Db;

use Zend\Db\Adapter\Adapter as ZfAdapter;
use Zend\Db\Adapter\Driver\ResultInterface;

class Adapter extends ZfAdapter
{
    /**
     * @return \Zend\Db\Adapter\Driver\ConnectionInterface
     */
    function getConnection()
    {
        return $this->getDriver()->getConnection();
    }

    /**
     * @return \PDO
     */
    function getResource()
    {
        return $this->getDriver()->getConnection()->getResource();
    }


    /**
     * @param \Traversable $result
     * @return array
     */
    static public function toArray(\Traversable $result)
    {
        $data = array();
        foreach ($result as $row) {
            $data[] = $row;
        }
        return $data;
    }

    /**
     * @param string $sql
     * @param array $params
     * @return \Zend\Db\Adapter\Driver\ResultInterface
     */
    function fetchResult($sql, array $params= array())
    {
        $statement = $this->query($sql);
        if (empty($params)) {
            $result = $statement->execute();
        }else {
            $result = $statement->execute($params);
        }
        return $result;
    }

    /**
     * @param string $sql
     * @param array $params
     * @return array
     */
    function fetchAll($sql, array $params= array())
    {
        $result = $this->fetchResult($sql, $params);
        return self::toArray($result);
    }

    /**
     * @param string $sql
     * @param array $params
     * @return array
     */
    function fetchRow($sql, array $params= array())
    {
        $result = $this->fetchResult($sql, $params);
        return $result->current();
    }


    /**
     * @param string $sql
     * @param array $params
     * @return null|string
     */
    function fetchOne($sql, array $params= array())
    {
        $result = $this->fetchResult($sql, $params);
        if ($result->valid() === false) return null;
        $row = $result->current();
        if (empty($row)) {
            return null;
        } else {
            return current($row);
        }
    }

    /**
     * @param string $sql
     * @param array $params
     * @return array
     */
    function fetchPairs($sql, array $params= array())
    {
        $result = $this->fetchResult($sql, $params);

        $line = $result->current();
        if (empty($line)){
            return array();
        } elseif (count($line) < 2) {
            throw new \DomainException('the number of columns is not enough');
        }

        $key = current($line);
        next($line);
        $val = current($line);
        $data = array($key => $val);
        $result->next();

        while($result->valid()) {
            $row = $result->current();
            $key = current($row);
            next($row);
            $data[$key] = current($row);
            $result->next();
        }
        return $data;
    }

    /**
     * @return \Zend\Db\Adapter\Driver\ConnectionInterface
     */
    function beginTransaction()
    {
        return $this->getConnection()->beginTransaction();
    }

    /**
     * @return \Zend\Db\Adapter\Driver\ConnectionInterface
     */
    function commit()
    {
        return $this->getConnection()->commit();
    }

    /**
     * @return \Zend\Db\Adapter\Driver\ConnectionInterface
     */
    function rollback()
    {
        return $this->getConnection()->rollback();
    }

    /**
     * @param  string $sequencename
     * @return string|null|false
     */
    function lastInsertId($sequencename = null)
    {
        return $this->getConnection()->getLastGeneratedValue($sequencename);
    }

}
