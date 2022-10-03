<?php

/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Commercial License (PCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     GPLv3 and PCL
 */

namespace Pimcore\Bundle\EcommerceFrameworkBundle\Model;

use Pimcore\Logger;
use Pimcore\Model\DataObject;

class DefaultMockup implements ProductInterface, LinkGeneratorAwareInterface
{
    /** @var int */
    protected $id;

    /** @var array */
    protected $params;

    /** @var array */
    protected $relations;

    /**
     * contains link generators by class type (just for caching)
     */
    protected static array $linkGenerators = [];

    public function __construct($id, $params, $relations)
    {
        $this->id = $id;
        $this->params = $params;

        $this->relations = [];
        if ($relations) {
            foreach ($relations as $relation) {
                $this->relations[$relation['fieldname']][] = ['id' => $relation['dest'], 'type' => $relation['type']];
            }
        }
    }

    public function getLinkGenerator(): ?DataObject\ClassDefinition\LinkGeneratorInterface
    {
        if ($classId = $this->params['classId'] ?? null) {
            return static::$linkGenerators[$classId] ??= DataObject\ClassDefinition::getById($classId)->getLinkGenerator();
        }

        return null;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function getParam($key)
    {
        return $this->params[$key];
    }

    /**
     * @param array $params
     *
     * @return $this
     */
    public function setParams($params)
    {
        $this->params = $params;

        return $this;
    }

    /**
     * @return array
     */
    public function getRelations()
    {
        return $this->relations;
    }

    /**
     * @param array $relations
     *
     * @return $this
     */
    public function setRelations($relations)
    {
        $this->relations = $relations;

        return $this;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function getRelationAttribute($attributeName)
    {
        $relationObjectArray = [];
        if ($this->relations[$attributeName]) {
            foreach ($this->relations[$attributeName] as $relation) {
                $relationObject = \Pimcore\Model\Element\Service::getElementById($relation['type'], $relation['id']);
                if ($relationObject) {
                    $relationObjectArray[] = $relationObject;
                }
            }
        }

        if (count($relationObjectArray) == 1) {
            return $relationObjectArray[0];
        } elseif (count($relationObjectArray) > 1) {
            return $relationObjectArray;
        } else {
            return null;
        }
    }

    public function __call($method, $args)
    {
        $attributeName = $method;
        if (substr($method, 0, 3) == 'get') {
            $attributeName = lcfirst(substr($method, 3));
        }

        if (is_array($this->params) && array_key_exists($attributeName, $this->params)) {
            return $this->params[$attributeName];
        }

        if (is_array($this->relations) && array_key_exists($attributeName, $this->relations)) {
            $relation = $this->getRelationAttribute($attributeName);
            if ($relation) {
                return $relation;
            }
        }
        $msg = "Method $method not in Mockup implemented, delegating to object with id {$this->id}.";

        if (\Pimcore::inDebugMode()) {
            Logger::warn($msg);
        } else {
            Logger::info($msg);
        }

        $object = $this->getOriginalObject();
        if ($object) {
            if (method_exists($object, $method)) {
                return call_user_func_array([$object, $method], $args);
            }

            $method = 'get' . ucfirst($method);
            if (method_exists($object, $method)) {
                return call_user_func_array([$object, $method], $args);
            }
        }

        throw new \Exception("Object with {$this->id} not found.");
    }

    public function getOriginalObject()
    {
        Logger::notice("Getting original object {$this->id}.");

        return \Pimcore\Model\DataObject::getById($this->id);
    }

    /**
     * called by default CommitOrderProcessor to get the product name to store it in the order item
     * should be overwritten in mapped sub classes of product classes
     *
     * @return string|null
     */
    public function getOSName(): ?string
    {
        return $this->__call('getOSName', []);
    }

    /**
     * called by default CommitOrderProcessor to get the product number to store it in the order item
     * should be overwritten in mapped sub classes of product classes
     *
     * @return string|null
     */
    public function getOSProductNumber(): ?string
    {
        return $this->__call('getOSProductNumber', []);
    }

    /**
     * returns array of categories.
     * has to be overwritten either in pimcore object or mapped sub class.
     *
     * @return array
     */
    public function getCategories()
    {
        return $this->__call('getCategories', []);
    }
}
