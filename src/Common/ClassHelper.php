<?php declare(strict_types=1);

namespace Jad\Common;

use Jad\Exceptions\JadException;

/**
 * Class ClassHelper
 * @package Jad\Common
 */
class ClassHelper
{
    /**
     * @param object $class
     * @param string $property
     * @return mixed
     * @throws JadException
     * @throws \ReflectionException
     */
    public static function getPropertyValue($class, string $property)
    {
        try {
            $method = 'get' . ucfirst($property);

            if (method_exists($class, $method)) {
                $result = $class->$method();
            } else {
                $result = $class->$property ?? null;
            }

            return $result;
        } catch (\Throwable $throwable) {
            throw new JadException('Property [' . $property . '] not found in class [' . get_class($class) . ']');
        }
    }

    /**
     * @param object $class
     * @param string $property
     * @param mixed $value
     * @throws \ReflectionException
     */
    public static function setPropertyValue($class, string $property, $value): void
    {
        $method = 'set' . ucfirst($property);

        if (method_exists($class, $method)) {
            $class->$method($value);
            return;
        }

        $reflection = new \ReflectionClass($class);

        if ($reflection->hasProperty($property)) {
            $reflectionProperty = $reflection->getProperty($property);
            $reflectionProperty->setAccessible(true);
            $reflectionProperty->setValue($class, $value);
        }
    }

    /**
     * @param object $class
     * @param string $property
     * @return bool
     * @throws \ReflectionException
     */
    public static function hasPropertyValue($class, string $property): bool
    {
        $method = 'set' . ucfirst($property);

        if (method_exists($class, $method)) {
            return true;
        }

        $reflection = new \ReflectionClass($class);

        return $reflection->hasProperty($property);
    }
}
