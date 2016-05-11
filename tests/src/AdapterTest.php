<?php
namespace Test;
use ZfCompat\Db\Adapter;

class AdapterTest extends \DbCase
{
    function getDataset()
    {
        return $this->createFlatXmlDataSet(__DIR__ . '/AdapterTest.xml');
    }

    private function getInstance()
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

        return new Adapter($config);
    }

    function dataProvider_testFetchResult()
    {
        return array(
            array('select * from zftest', array()),
            array('select * from zftest where id = ?', array(1))
        );
    }

    /**
     * @dataProvider dataProvider_testFetchResult
     * @group Adapter
     */
    function testFetchResult_returnType($sql, $params)
    {
        $instance = $this->getInstance();
        $result = $instance->fetchResult($sql, $params);
        $this->assertInstanceOf(
            '\\Zend\\Db\\Adapter\\Driver\\ResultInterface', $result
        );
    }

    /**
     * @group Adapter
     */
    function testFetchAll()
    {
        $sql = "select * from zftest where id in(1,2) and number = ?";
        $ext = array(
            array(
                "id"=>"1",
                "name"=>"山田",
                "number"=>"765",
                "deci_number"=>"0.999"
            ),
            array(
                "id"=>"2",
                "name"=>"本田",
                "number"=>"765",
                "deci_number"=>"0.999"
            )
        );
            

        $adapter = $this->getInstance();
        $result = $adapter->fetchAll($sql, array(765));

        $this->assertSame($ext, $result);
    }

    /**
     * @group Adapter
     */
    function testFetchRow()
    {
        $sql = "select * from zftest";
        $ext = array(
            "id"=>"1",
            "name"=>"山田",
            "number"=>"765",
            "deci_number"=>"0.999"
        );

        $adapter = $this->getInstance();
        $result = $adapter->fetchRow($sql);

        $this->assertSame($ext, $result);
    }

    /**
     * @group Adapter
     */
    function testFetchRow_where_id()
    {
        $sql = "select * from zftest where id = ?";
        $ext = array(
            "id"=>"2",
            "name"=>"本田",
            "number"=>"765",
            "deci_number"=>"0.999"
        );

        $adapter = $this->getInstance();
        $result = $adapter->fetchRow($sql, array(2));

        $this->assertSame($ext, $result);
    }

    /**
     * @group Adapter
     */
    function testFetchOne()
    {
        $sql = "select count(*) as cnt from zftest where id = ?";
        $ext = "1";

        $adapter = $this->getInstance();
        $result = $adapter->fetchOne($sql, array(2));

        $this->assertSame($ext, $result);
    }

    /**
     * @group Adapter
     */
    function testFetchOne_emptyrows()
    {
        $sql = "select * from zftest where id = ?";
        $ext = null;

        $adapter = $this->getInstance();
        $result = $adapter->fetchOne($sql, array(999999));

        $this->assertSame($ext, $result);
    }

    /**
     * @expectedException DomainException
     * @group Adapter
     */
    function testFetchPairs_less2cols()
    {
        $sql = "select id from zftest where id in(1,2) ";
        $adapter = $this->getInstance();
        $result = $adapter->fetchPairs($sql);

        $this->fail();
    }

    /**
     * @group Adapter
     */
    function testFetchPairs()
    {
        $sql = "select id, name from zftest where id in(1,2) ";
        $ext = array(1=>'山田',2=>'本田');

        $adapter = $this->getInstance();
        $result = $adapter->fetchPairs($sql);

        $this->assertSame($ext, $result);
    }

    /**
     * @group Adapter
     */
    function testFetchPairs_emptyRows()
    {
        $sql = "select id, name from zftest where id = ? ";
        $ext = array();

        $adapter = $this->getInstance();
        $result = $adapter->fetchPairs($sql, array(99999));

        $this->assertSame($ext, $result);
    }

   /**
     * @group Adapter
     */
    function testTransaction_commit()
    {
        $data = array('id' => '55', 'name'=> 'new row');
        $sql = new \Zend\Db\Sql\Sql($this->getAdapter(), 'zftest');
        $insert = $sql->insert()->values($data);
        $sqlstr = $sql->buildSqlString($insert);

        $instance = $this->getInstance();
        $instance->beginTransaction();
        $this->assertTrue($instance->getConnection()->inTransaction());

        $instance->query($sqlstr, $instance::QUERY_MODE_EXECUTE);
        $instance->commit();

        $adapter = $this->getAdapter();
        $resultset = $adapter->query(
            'select id, name from zftest where id = 55',
            $adapter::QUERY_MODE_EXECUTE
        );
        $this->assertEquals($data, (array) $resultset->current());
    }

   /**
     * @group Adapter
     */
    function testTransaction_rollback()
    {
        $data = array('id' => '55', 'name'=> 'new row');
        $sql = new \Zend\Db\Sql\Sql($this->getAdapter(), 'zftest');
        $insert = $sql->insert()->values($data);
        $sqlstr = $sql->buildSqlString($insert);

        $instance = $this->getInstance();
        $instance->beginTransaction();
        $this->assertTrue($instance->getConnection()->inTransaction());

        $instance->query($sqlstr, $instance::QUERY_MODE_EXECUTE);
        $instance->rollback();

        $adapter = $this->getAdapter();
        $resultset = $adapter->query(
            'select id, name from zftest where id = 55',
            $adapter::QUERY_MODE_EXECUTE
        );
        $this->assertSame(0, count($resultset));
    }
}