<?php

namespace GW2Spidy\DB;

use GW2Spidy\DB\om\BaseItemPeer;
use \Propel;


/**
 * Skeleton subclass for performing query and update operations on the 'item' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.gw2spidy
 */
class ItemPeer extends BaseItemPeer {
    protected static $memPool = null;

    /**
     * @return MemcacheInstancePool
     */
    protected static function getMemPool() {
        if (is_null(static::$memPool)) {
            static::$memPool = MemcacheInstancePool::getInstance(static::DATABASE_NAME . "::" . static::TABLE_NAME);
        }

        return static::$memPool;
    }

    public static function addInstanceToPool($obj, $key = null) {
        if (Propel::isInstancePoolingEnabled()) {
            if ($key === null) {
                $key = (string) $obj->getDataId();
            } // if key === null

            if ($memPool = static::getMemPool()) {
                $memPool->addInstanceToPool($obj, $key);
            }

            ItemPeer::$instances[$key] = $obj;
        } else {
            if ($memPool = static::getMemPool()) {
                $memPool->removeInstanceFromPool($key);
            }
        }
    }

    public static function removeInstanceFromPool($value)
    {
        if ($value !== null) {
            if (is_object($value) && $value instanceof Item) {
                $key = (string) $value->getDataId();
            } elseif (is_scalar($value)) {
                // assume we've been passed a primary key
                $key = (string) $value;
            } else {
                $e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or Item object; got " . (is_object($value) ? get_class($value) . ' object.' : var_export($value,true)));
                throw $e;
            }

            if ($memPool = static::getMemPool()) {
                $memPool->removeInstanceFromPool($key);
            }

            parent::removeInstanceFromPool($value);
        }
    }

    public static function getInstanceFromPool($key)
    {
        if (Propel::isInstancePoolingEnabled()) {
            if (isset(ItemPeer::$instances[$key])) {
                return ItemPeer::$instances[$key];
            }

            if ($memPool = static::getMemPool()) {
                return $memPool->getInstanceFromPool($key);
            }
        }

        return null; // just to be explicit
    }

    public static function clearInstancePool()
    {
        if ($memPool = static::getMemPool()) {
            $memPool->clearInstancePool();
        }

        ItemPeer::$instances = array();
    }

} // ItemPeer
