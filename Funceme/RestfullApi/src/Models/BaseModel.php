<?php

namespace Funceme\RestfullApi\Models;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    public function toArray()
    {
        $array_object = [];
        
        $properties = array_merge($this->attributesToArray(), $this->relationsToArray());

        foreach ($properties as $property=>$value) {
            if (!is_null($value)) {

                if (is_object($value) && ($value instanceof MultiPolygon)) {
                    // TODO: link to geoserver 
                } else {
                    $array_object[$property] = $value;
                }
            }    
        }
        
        return $array_object;
    }   
}
