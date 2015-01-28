<?php
/** {license_text}  */
namespace Core;

use Closure;
use ReflectionClass;
use Illuminate\Foundation\Application as ApplicationAbstract;
use Illuminate\Container\BindingResolutionException;

class Application 
    extends ApplicationAbstract
{
    /**
     * @param ReflectionClass $reflector
     * @return bool
     */
    protected function isInstantiable(ReflectionClass $reflector)
    {
        if ($reflector->isInstantiable() || (
                $reflector->getConstructor()->isProtected()
                && $reflector->hasMethod('instance')
                && ($dummyConstructor = $reflector->getMethod('instance'))
                && $dummyConstructor->isStatic() && $dummyConstructor->isPublic()
            )
        ) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Instantiate a concrete instance of the given type.
     *
     * @param  string  $concrete
     * @param  array   $parameters
     * @return mixed
     *
     * @throws BindingResolutionException
     */
    public function build($concrete, $parameters = [])
    {
        // If the concrete type is actually a Closure, we will just execute it and
        // hand back the results of the functions, which allows functions to be
        // used as resolvers for more fine-tuned resolution of these objects.
        if ($concrete instanceof Closure)
        {
            return $concrete($this, $parameters);
        }

        $reflector = new ReflectionClass($concrete);

        // If the type is not instantiable, the developer is attempting to resolve
        // an abstract type such as an Interface of Abstract Class and there is
        // no binding registered for the abstractions so we need to bail out.
        
        if ( ! $this->isInstantiable($reflector))
        {
            $message = "Target [$concrete] is not instantiable.";

            throw new BindingResolutionException($message);
        }

        $this->buildStack[] = $concrete;

        $constructor = $reflector->getConstructor();

        // If there are no constructors, that means there are no dependencies then
        // we can just resolve the instances of the objects right away, without
        // resolving any other types or dependencies out of these containers.
        if (is_null($constructor))
        {
            array_pop($this->buildStack);

            return new $concrete;
        }
        
        if ($constructor->isPublic()) {
            $dependencies = $constructor->getParameters();

            // Once we have all the constructor's parameters we can create each of the
            // dependency instances and then use the reflection instances to make a
            // new instance of this class, injecting the created dependencies in.
            $parameters = $this->keyParametersByArgument(
                $dependencies, $parameters
            );

            $instances = $this->getDependencies(
                $dependencies, $parameters
            );

            array_pop($this->buildStack);

            return $reflector->newInstanceArgs($instances);
        } else if ($constructor->isProtected() && ($dummyConstructor = $reflector->getMethod('instance'))) {
            $dependencies = $dummyConstructor->getParameters();
            $parameters = $this->keyParametersByArgument(
                $dependencies, $parameters
            );

            $instances = $this->getDependencies(
                $dependencies, $parameters
            );

            return $dummyConstructor->invokeArgs(null, $instances);
        } else {
            $message = "Target [$concrete] is not instantiable.";

            throw new BindingResolutionException($message);
        }
    }
}
