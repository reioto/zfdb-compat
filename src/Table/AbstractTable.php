<?php
namespace ZfCompat\Db\Table;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\TableGateway\Feature;
use Zend\Db\Metadata\Metadata;

abstract class AbstractTable extends AbstractTableGateway
{
    const SCHEMA = 'schema';
    const PRIMARY = 'primary';
    const COLS  = 'cols';

    /**
     * @param AdapterInterface $adapter
     * @param Feature\AbstractFeature|Feature\FeatureSet|Feature\AbstractFeature[]|null $features
     */
    function __construct(Adapter $adapter, $features = null)
    {
        $this->adapter = $adapter;
        $this->_setFeatures($features);
        $this->initialize();
    }

    private function _setFeatures($features)
    {
        if ($features !== null) {
            if ($features instanceof Feature\AbstractFeature) {
                $features = array($features);
            }
            if (is_array($features)) {
                $this->featureSet = new Feature\FeatureSet($features);
            } elseif ($features instanceof Feature\FeatureSet) {
                $this->featureSet = $features;
            } else {
                throw new \InvalidArgumentException(
                    'TableGateway expects $feature to be an instance of an AbstractFeature or a FeatureSet, or an array of AbstractFeatures'
                );
            }
        } else {
            $this->featureSet = new Feature\FeatureSet();
        }
    }

    private static $_meta = null;

    /**
     * @return Zend\Db\Metadata\Metadata
     */
    function getMeta()
    {
        if (self::$_meta === null) {
            self::$_meta = new Metadata($this->getAdapter());
        }
        return self::$_meta;
    }


    private $_info = null;

    private function _getInfo()
    {
        if ($this->_info === null) {
            $this->_info = $this->getMeta()->getTable($this->getTable());
        }
        return $this->_info;
    }

    /**
     * @return null|\Zend\Db\Metadata\Object\TableObject|string[]|string
     */
    function info($key=null)
    {
        $info = $this->_getInfo();

        if ($key === null) {
            return $info;
        } else if ($key === self::PRIMARY) {
            foreach ($info->getConstraints() as $col) {
                if ($col->isPrimaryKey()) {
                    return $col->getColumns();
                }
            }
            return null;
        } else if ($key === self::COLS) {
            $cols = array();
            foreach ($info->getColumns() as $col) {
                $cols[] = $col->getName();
            }
            return $cols;
        } else if ($key === self::SCHEMA) {
            foreach ($info->getConstraints() as $col) {
                if ($col->isPrimaryKey()) {
                    return $col->getSchemaName();
                }
            }

            foreach ($info->getColumns() as $col) {
                return $col->getSchemaName();
            }
            return null;
        }

        throw new \InvalidArgumentException(sprintf('%s is invalid key', $key));
    }
    
}
