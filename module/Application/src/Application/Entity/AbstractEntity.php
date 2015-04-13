<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 4/24/14
 * Time: 3:31 PM
 */

namespace Application\Entity;


abstract class AbstractEntity {
    const CLASS_NAME_SEPERATOR = '@';

    public static function getClass() {

        return get_called_class();
    }

    public function getEntityClass() {
        if ($this instanceof \Doctrine\ORM\Proxy\Proxy) {

            return get_parent_class($this);
        }

        return  get_class($this);
    }

    public function toArray($classNames = false) {
        $array = array ();
        $reflect = new \ReflectionClass($this);
        $properties = $reflect->getProperties(\ReflectionProperty::IS_PROTECTED | \ReflectionProperty::IS_PRIVATE);
        /* @var $property \ReflectionProperty */
        foreach ($properties as $property) {
            if ($property->isStatic()) {
                continue;
            }
            $methodName = 'get' . ucfirst($property->name);
            if (method_exists($this, $methodName)) {
                $value = $this->$methodName();
            } else {
                $value = $this->{$property->name};
            }
            if (is_object($value)) {
                if ($value instanceof \Doctrine\Common\Collections\ArrayCollection) { //In case of ArrayCollection
                    $newValues = array();
                    foreach ($value as $oneValue) { //Find all elements that have "getId()"
                        if (method_exists($oneValue, 'getId')) {
                            $newValues[] = $oneValue->getId();
                        }
                    }
                    if ($classNames) {
                        $value = 'ArrayCollection(' . implode(',', $newValues) . ')';
                    } else {
                        $value = $newValues;
                    }
                } elseif ($value instanceof \DateTime) { //In case of DateTime, find the formatted value
                    if ($classNames) {
                        $value = 'DateTime' . self::CLASS_NAME_SEPERATOR . $value->format('d.m.Y H:i');
                    } else {
                        $value = $value->format('d.m.Y H:i');
                    }
                } elseif ($value instanceof AbstractEntity) { //In case of another entity, find id (if getId() exists)
                    if (method_exists($value, 'getId')) {
                        $id = $value->getId();
                    } else {
                        $id = null;
                    }
                    if ($classNames) {
                        $value = $value->getClass() . (($id != null) ? self::CLASS_NAME_SEPERATOR . $id : null);
                    } else {
                        $value = $id;
                    }
                } else {
                    $value = null;
                }
            }

            $array[$property->name] = $value;
        }

        return $array;
    }

    protected function assembleFieldsString() {
        $fields = array();
        foreach($this->toArray(true) as $field => $value) {
            $fields[] = $field . '=' . (($value == null) ? 'NULL' : '"' . $value) . '"';
        }
        $string = '[' . implode(', ', $fields) . ']';

        return $string;
    }

    public function __toString() {
        return self::getClass() . self::CLASS_NAME_SEPERATOR . $this->assembleFieldsString();
    }
}