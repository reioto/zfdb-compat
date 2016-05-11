<?php

abstract class DbCase extends \PHPUnit_Extensions_Database_TestCase
{
    private static $_adapter;
    public function getConnection()
    {
        if (self::$_adapter === null) {
            $params = array(
                'driver', 'database', 'username', 'password', 'hostname',
                'port', 'charset'
            );

            $config = array();
            foreach ($params as $name) {
                $val = getenv($name);
                if (empty($val)) continue;
                $config[$name] = $val;
            }
            self::$_adapter = new \Zend\Db\Adapter\Adapter($config);
        }

        $pdo = $this->getAdapter()->getDriver()->getConnection()->getResource();
        return $this->createDefaultDBConnection($pdo);
    }

    function getAdapter()
    {
        return self::$_adapter;
    }
}