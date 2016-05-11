<?php
namespace Test;
use ZfCompat\Db\Sql;

class SqlTest extends \DbCase
{
    function getDataset()
    {
        return $this->createFlatXmlDataSet(__DIR__ . '/SqlTest.xml');
    }

    private function _getAdapter()
    {
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

        return new \ZfCompat\Db\Adapter($config);
    }

    private function getInstance()
    {
        $adapter = $this->_getAdapter();
        return new Sql($adapter, 'zftest');
    }

    function dataProvider_testFetchAll()
    {
        $sql = $this->getInstance();
        return array(
            array(
                $sql->select(),
                array(
                    array("id"=>1, "name"=>"山田", "number"=>765, "deci_number"=>"0.999"),
                    array("id"=>2, "name"=>"本田", "number"=>765, "deci_number"=>"0.999")
                )
            ),
            array(
                $sql->select()->where(array('id'=>1)),
                array(array("id"=>1, "name"=>"山田", "number"=>765, "deci_number"=>"0.999"))
            )
        );
    }

    /**
     * @dataProvider dataProvider_testFetchAll
     * @group Sql
     */
    function testFetchAll($sql, $ext)
    {
        $instance = $this->getInstance();
        $result = $instance->fetchAll($sql);
        $this->assertEquals($ext, $result);
    }

    /**
     * @group Sql
     */
    function testFetchRow()
    {
        $adapter = $this->getInstance();

        $sql = $adapter->select()->where(array('id'=>1));
        $ext = array(
            "id"=>1,
            "name"=>"山田", 
            "number"=>765, 
            "deci_number"=>"0.999"
        );

        $result = $adapter->fetchRow($sql);
        $this->assertEquals($ext, $result);

        $sql = $adapter->select();
        $result2 = $adapter->fetchRow($sql);
        $this->assertEquals($result, $result2);
    }

    /**
     * @group Sql
     */
    function testFetchRow_emptyresult()
    {
        $adapter = $this->getInstance();

        $sql = $adapter->select()->where(array('id'=>999999));

        $result = $adapter->fetchRow($sql);
        $this->assertSame(false, $result);
    }

    /**
     * @group Sql
     */
    function testFetchOne()
    {
        $adapter = $this->getInstance();

        $sql = $adapter->select()
                       ->columns(array('cnt' => new \Zend\Db\Sql\Expression('count(id)')))
                       ->where(array('id'=>1));

        $result = $adapter->fetchOne($sql);
        $this->assertEquals(1, $result);
    }

    function testFetchOne_emptyresult()
    {
        $adapter = $this->getInstance();

        $sql = $adapter->select()
                       ->where(array('id'=>999999));

        $result = $adapter->fetchOne($sql);
        $this->assertSame(null, $result);
    }
}
