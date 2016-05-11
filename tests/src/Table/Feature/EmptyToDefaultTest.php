<?php
namespace Test\Table\Feature;

use Zend\Db\TableGateway\TableGateway;
use ZfCompat\Db\Table\Feature\EmptyToDefault;

class EmptyToDefaultTest extends \DbCase
{
    function getDataset()
    {
        return $this->createFlatXmlDataSet(__DIR__ . '/EmptyToDefaultTest.xml');
    }

    private function getInstance()
    {
        return new EmptyToDefault(array('name', 'number'));
    }

    private function getTable()
    {
        $feature = $this->getInstance();
        $adapter = $this->getAdapter();
        return new TableGateway('zftest', $adapter, $feature);
    }

    /**
     * @group Table
     * @group Table_Feature
     * @group Table_Feature_EmptyToDefault
     */
    function testPreInsert()
    {
        $instance = $this->getTable();
        $params = array(
            'name' => '',
            'number' => 123
        );
        $instance->insert($params);
        $id = $instance->getLastInsertValue();

        $adapter = $this->getAdapter();
        $sql = 'select id, name, number from zftest where id = ?';
        $result = $adapter->query($sql, array($id));

        $ext = array(
            'id' => (string)$id,
            'name' => null,
            'number' => '123'
        );

        $row = $result->current();
        $this->assertSame($ext, (array) $row);
    }

    /**
     * @group Table
     * @group Table_Feature
     * @group Table_Feature_EmptyToDefault
     */
    function testPreUpdate()
    {
        $instance = $this->getTable();
        $params = array(
            'name' => '',
            'number' => 123
        );
        $id = 2;
        $instance->update($params, array('id'=>$id));

        $adapter = $this->getAdapter();
        $sql = 'select id, name, number from zftest where id = ?';
        $result = $adapter->query($sql, array($id));

        $ext = array(
            'id' => (string)$id,
            'name' => null,
            'number' => '123'
        );

        $row = $result->current();
        $this->assertSame($ext, (array) $row);
    }
}