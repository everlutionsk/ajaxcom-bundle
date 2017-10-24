<?php

declare(strict_types=1);

namespace Everlution\AjaxcomBundle\DataSource;

/**
 * Class BaseDataSource.
 *
 * @author Ivan Barlog <ivan.barlog@everlution.sk>
 */
class BaseDataSource extends \Twig_Extension
{
    protected const TWIG_FUNCTION_SUFFIX = 'Provider';

    /**
     * @return array
     */
    public function getFunctions()
    {
        $functions = [];
        $class = new \ReflectionClass(get_class($this));

        if (self::class === $class->name) {
            return $functions;
        }

        foreach ($class->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            if (false === ($offset = strpos($method->getName(), self::TWIG_FUNCTION_SUFFIX))) {
                continue;
            }

            $functions[] = $this->registerAsTwigFunction(substr($method->getName(), 0, $offset));
        }

        return $functions;
    }

    private function registerAsTwigFunction(string $name): ?\Twig_SimpleFunction
    {
        return new \Twig_SimpleFunction($name, [$this, $name.self::TWIG_FUNCTION_SUFFIX]);
    }
}
