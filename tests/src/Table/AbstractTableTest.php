<?php
namespace Test\Table;

use ZfCompat\Db\Table\AbstractTable;

class TestTable extends AbstractTable
{
    protected $table = 'zftest';
}

class AbstractTableTest extends \DbCase
{
    function getDataset()
    {
        return $this->createFlatXmlDataSet(__DIR__ . '/AbstractTableTest.xml');
    }

    private function getInstance()
    {
        $adapter = $this->getAdapter();
        return new TestTable($adapter);
    }

    /**
     * @group Table
     */
    function testInsert()
    {
        $instance = $this->getInstance();

        $data = array('name'=>'てすと');
        $instance->insert($data);
        $lastNumber = $instance->lastInsertValue;
        $this->assertEquals(3, $lastNumber);

        $ext[] = array('id'=>'3', 'name'=>'てすと');
        $sql = "select id, name from zftest where id = 3";
        $adapter = $this->getAdapter();
        $resultset = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
        $this->assertEquals($ext, $resultset->toArray());
    }

    /**
     * @group Table
     */
    function testGetMeta()
    {
        $instance = $this->getInstance();
        $result = $instance->getMeta();
        $this->assertInstanceOf("\\Zend\\Db\\Metadata\\Metadata", $result);
        $again = $instance->getMeta();

        $this->assertSame($result, $again);
    }

    /**
     * @group Table
     */
    function testInfo()
    {
        $instance = $this->getInstance();
        $result = $instance->info();
        $this->assertInstanceOf(
            "\\Zend\\Db\\Metadata\\Object\\TableObject", $result
        );
        $this->assertEquals($instance->getTable(), $result->getName());
    }

    /**
     * @group Table
     */
    function testInfo_params()
    {
        $instance = $this->getInstance();

        $key = $instance::PRIMARY;
        $result = $instance->info($key);
        $this->assertEquals(array('id'), $result);

        $key = $instance::COLS;
        $result = $instance->info($key);
        $ext = array('id', 'name', 'number', 'deci_number');
        $this->assertEquals($ext, $result);

        $key = $instance::SCHEMA;
        $result = $instance->info($key);
        $this->assertEquals($instance->getTable(), $result);
    }

    /**
     * @expectedException InvalidArgumentException
     * @group Table
     */
    function testInfo_params_unknownkey()
    {
        $instance = $this->getInstance();
        $key = 'unknown';
        $instance->info($key);

        $this->fail();
    }


}