<?php

namespace App\Model\Entity;

use Kdyby\Doctrine\Entities\BaseEntity;

abstract class AbstractEntity extends BaseEntity
{
    /**
     * @return integer
     */
    abstract protected function getId();

    /**
     * AbstractEntity constructor.
     * @param null $parameters
     */
    public function __construct($parameters = null)
    {
        parent::__construct();

        if (is_null($parameters)) {
            return;
        }

        $this->setData($parameters);
    }

    public function __clone()
    {
        $this->id = null;
    }

    /**
     * @return array
     */
    public function getAsArray()
    {
        $result = array();

        $parameters = get_class_vars(get_called_class());
        foreach ($parameters as $key => $parameter) {
            $getterName = 'get' . ucfirst($key);
            $value = null;

            if (method_exists($this, $getterName)) {
                $value = $this->$getterName();
            }

            if ($value instanceof \Kdyby\Doctrine\Entities\BaseEntity) {
                $value = $value->getId();
            }

            if ($value instanceof \Doctrine\ORM\PersistentCollection) {
                $collection = $value = $value->toArray();
                foreach ($collection as $index => $item) {
                    $value[$index] = $item->getId();
                }
            }

            $result[$key] = $value;
        }

        return $result;
    }

    /**
     * @param null $data
     * @throws \Exception
     */
    public function populateData($data = null)
    {
        $this->setData($data);
    }

    /**
     * @param mixed|null $data
     * @throws \Exception
     */
    protected function setData($data = null)
    {
        if (!is_array($data) && !$data instanceof \Traversable) {
            throw new \Exception('Parameters must be array or instance of Traversable');
        }

        foreach ($data as $key => $parameter) {
            //if (!property_exists($this, $key)) {
            //    throw new \Exception('Parameter:' . $key . ' don\'t exist on object: ' . get_called_class());
            //}

            $setterName = 'set' . ucfirst($key);
            if (method_exists($this, $setterName)) {
                $this->$setterName($parameter);
            } else {
                $this->$key = $parameter;
            }
        }
    }
}