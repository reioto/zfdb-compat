<?php
namespace Test\Table;

use ZfCompat\Db\Table\Table;

class TableTest extends \DbCase
{
    function getDataset()
    {
        return $this->createFlatXmlDataSet(__DIR__ . '/TableTest.xml');
    }

    private function getInstance()
    {
        $adapter = $this->getAdapter();
        return new Table('zftest', $adapter);
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
}