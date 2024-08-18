<?php

use Illuminate\Database\Eloquent\Model;

function getApprovableRelatedColumnValue(Model $relatedModel): ?string
{
    if (property_exists($relatedModel, 'approvable_related_column')) {
        $reflector = new ReflectionClass($relatedModel);
        $property = $reflector->getProperty('approvable_related_column');
        $property->setAccessible(true);
        return $property->getValue($relatedModel);
    }
    return null;
}
