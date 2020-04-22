<?php

namespace Zaeder\PhpUnit;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    /**
     * Set a value to a protected class attribute
     * @param $entity
     * @param $value
     * @param string $propertyName
     * @throws \ReflectionException
     */
    protected function set($entity, $value, $propertyName = 'id')
    {
        $class = new \ReflectionClass($entity);
        $property = $class->getProperty($propertyName);
        $property->setAccessible(true);
        $property->setValue($entity, $value);
        $property->setAccessible(false);
    }

    /**
     * Get the value of a protected class attribute
     * @param $entity
     * @param string $propertyName
     * @return mixed
     * @throws \ReflectionException
     */
    protected function get($entity, $propertyName = 'id')
    {
        $class = new \ReflectionClass($entity);
        $property = $class->getProperty($propertyName);
        $property->setAccessible(true);
        $value = $property->getValue($entity);
        $property->setAccessible(false);
        return $value;
    }

    /**
     * Get mocked object
     * @param string $className
     * @param null|array $params
     * @param array $methodsToMock
     * @return MockObject
     * @throws \ReflectionException
     */
    protected function getMockObject(string $className, $params = null, array $methodsToMock = [])
    {
        if ($this->isAbstractClass($className)) {
            if (is_null($params)) {
                $params = [];
            }
            return $this->getMockForAbstractClass($className, $params, '', true, true, true, $methodsToMock);
        } else {
            $mockBuilder = $this->getMockBuilder($className);
            if (null === $params) {
                $mockBuilder->disableOriginalConstructor();
            } elseif (is_array($params) && count($params) > 0) {
                $mockBuilder->setConstructorArgs($params);
            }
            if (count($methodsToMock) > 0) {
                $mockBuilder->setMethods($methodsToMock);
            }
            return $mockBuilder->getMock();
        }
    }

    /**
     * Get object to test with(out) mocked methods
     * @param string $className
     * @param array $params
     * @param array $methodsToMock
     * @return MockObject|mixed
     * @throws \ReflectionException
     */
    protected function getObjectToTest(string $className, array $params = [], array $methodsToMock = [])
    {
        if (count($methodsToMock) > 0 || $this->isAbstractClass($className)) {
            $object = $this->getMockObject($className, $params, $methodsToMock);
        } else {
            $object = new $className(...$params);
        }
        return $object;
    }

    /**
     * @param $className
     * @return bool
     * @throws \ReflectionException
     */
    private function isAbstractClass($className)
    {
        $class = new \ReflectionClass($className);
        return $class->isAbstract();
    }

    /**
     * @param $className
     * @param $methodName
     * @return bool
     * @throws \ReflectionException
     */
    protected function isAbstractMethod($className, $methodName)
    {
        $class = new \ReflectionClass($className);
        return $class->isAbstract()&& $class->getMethod($methodName)->isAbstract();
    }
}
