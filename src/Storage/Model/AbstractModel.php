<?php

namespace Storage\Model;

abstract class AbstractModel
{
    /**
     * @param array $modelData
     * @return void
     */
    public function populate(array $modelData)
    {
        foreach ($modelData as $field => $value) {

            $setterName = 'set'.ucfirst($field);

            if (method_exists($this, $setterName)) {

                $this->$setterName($value);
            }
        }
    }
    /**
     * @return array
     */
    public function asArray()
    {
        $fields = array();

        foreach (get_class_vars(get_class($this)) as $name => $defaultValue) {

            $fields[substr($name, 1)] = $this->{$name};
        }

        return $fields;
    }
}