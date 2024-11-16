<?php

namespace App\Services;

use Doctrine\Inflector\Inflector;
use Doctrine\Inflector\InflectorFactory;
use Doctrine\ORM\Mapping\NamingStrategy;
use ReflectionClass;
use ReflectionException;

class PluralNamingStrategy implements NamingStrategy
{
    private Inflector $inflector;

    public function __construct()
    {
        $this->inflector = InflectorFactory::create()->build();
    }

    public function classToTableName(string $className): string
    {
        if (class_exists($className)) {
            $shortClassName = (new ReflectionClass($className))->getShortName();
        } else {
            $pathParts = explode('\\', $className);
            $shortClassName = end($pathParts);
        }

        return $this->inflector->pluralize(strtolower($shortClassName));
    }

    public function propertyToColumnName(string $propertyName, string $className): string
    {
        $pattern = '/([a-z])([A-Z])/';
        $replacement = '$1_$2';
        $snakeCaseProperty = preg_replace($pattern, $replacement, $propertyName);

        return strtolower($snakeCaseProperty);
    }

    public function embeddedFieldToColumnName(string $propertyName, string $embeddedColumnName, string $className, string $embeddedClassName): string
    {
        return $propertyName . '_' . $embeddedColumnName;
    }

    public function referenceColumnName(): string
    {
        return 'id';
    }

    public function joinColumnName(string $propertyName, string $className): string
    {
        return $propertyName . '_id';
    }

    public function joinTableName(string $sourceEntity, string $targetEntity, string $propertyName): string
    {
        return $this->classToTableName($sourceEntity) . '_' . $this->classToTableName($targetEntity);
    }

    public function joinKeyColumnName(string $entityName, ?string $referencedColumnName): string
    {
        return strtolower($entityName) . '_' . ($referencedColumnName ?: $this->referenceColumnName());
    }
}