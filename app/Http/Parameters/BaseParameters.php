<?php

namespace App\Http\Parameters;

class BaseParameters
{
    private $data;
    public $fields = [];
    public $order = [];

    /**
     * constructor
     * @param array $data
     * @return void
     */
    public function __construct(
        array $data
    ) {
        $this->data = $data;
    }
    
    /**
     * get and return fields
     * @return array
     */
    public function fields() : array
    {
        $dataFields = $this->data['fields'];
        if (empty($dataFields)) {
            return $this->fields;
        }
        $dataFields = str_replace(' ', '', $dataFields);
        $fieldsArray = explode(',', $dataFields);
        
        $allowed = array_intersect(
            $this->fields,
            $fieldsArray
        );
        if (empty($allowed)) {
            return $this->fields;
        }
        return $allowed;
    }

    /**
     * get and return order
     * @return string
     */
    public function order() : string
    {
        $dataOrder = $this->data['order'];
        if (empty($dataOrder)) {
            return $this->order[0];
        }
        if (in_array($dataOrder, $this->order)) {
            return $dataOrder;
        }

        return $this->order[0];
    }

    /**
     * get and return classification
     * @return string
     */
    public function classification() : string
    {
        $class = $this->data['class'];
        if (empty($class)) {
            return 'desc';
        }
        return $class;
    }
}
