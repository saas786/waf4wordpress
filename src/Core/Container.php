<?php

namespace SzepeViktor\WordPress\Waf\Core;

use ArrayAccess;
use Closure;
use ReflectionClass;
use SzepeViktor\WordPress\Waf\Contracts\Container as ContainerContract;

class Container implements ContainerContract, ArrayAccess {

    /**
     * The current globally available container (if any).
     *
     * @var static
     */
    protected static $instance;

    /**
     * The container's bindings.
     *
     * @var    array
     */
    protected $bindings = [];

    /**
     * The container's shared instances.
     *
     * @var object[]
     */
    protected $instances = [];

    /**
     * Get the globally available instance of the container.
     *
     * @return static
     */
    public static function getInstance() {
        if ( is_null( static::$instance ) ) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * Set the shared instance of the container.
     *
     * @param \SzepeViktor\WordPress\Waf\Contracts\Container|null $container
     * @return \SzepeViktor\WordPress\Waf\Contracts\Container|static
     */
    public static function setInstance( ContainerContract $container = null ) {
        return static::$instance = $container;
    }

    /**
     * Register a binding with the container.
     *
     * @param  string               $abstract
     * @param  \Closure|string|null $concrete
     * @param  bool                 $shared
     * @return void
     *
     * @access public
     */
    public function bind( $abstract, $concrete = null, $shared = false ) {
        $this->dropStaleInstances( $abstract );

        // If no concrete type was given, we will simply set the concrete type to the
        // abstract type. After that, the concrete type to be registered as shared
        // without being forced to state their classes in both of the parameters.
        if ( is_null( $concrete ) ) {
            $concrete = $abstract;
        }

        // If the factory is not a Closure, it means it is just a class name which is
        // bound into this container to the abstract type and we will just wrap it
        // up inside its own Closure to give us more convenience when extending.
        if ( ! $concrete instanceof Closure ) {
            if ( ! is_string( $concrete ) ) {
                throw new \TypeError( self::class . '::bind(): Argument #2 ($concrete) must be of type Closure|string|null' );
            }

            $concrete = $this->getClosure( $abstract, $concrete );
        }

        $this->bindings[ $abstract ] = compact( 'concrete', 'shared' );
    }

    /**
     * Determine if the given abstract type has been bound.
     *
     * @param  string $abstract
     * @return bool
     */
    public function bound( $abstract ) {
        return isset( $this->bindings[ $abstract ] ) ||
            isset( $this->instances[ $abstract ] );
    }

    /**
     * Resolve the given type from the container.
     *
     * @param  string|callable $abstract
     * @return mixed
     */
    public function resolve( $abstract ) {

        // If an instance of the type is currently being managed as a singleton we'll
        // just return an existing instance instead of instantiating new instances
        // so the developer can keep using the same objects instance every time.
        if ( isset( $this->instances[ $abstract ] ) ) {
            return $this->instances[ $abstract ];
        }

        if ( ! array_key_exists( $abstract, $this->bindings ) ) {
            throw new \Exception( "No matching binding found for {$abstract}" );
        }

        $concrete = $this->getConcrete( $abstract );

        // We're ready to instantiate an instance of the concrete type registered for
        // the binding. This will instantiate the types, as well as resolve any of
        // its "nested" dependencies recursively until all have gotten resolved.
        $object = $this->isBuildable( $concrete, $abstract )
            ? $this->build( $concrete ) : $this->make( $concrete );

        // If the requested type is registered as a singleton we'll want to cache off
        // the instances in "memory" so we can return it later without creating an
        // entirely new instance of an object on each subsequent request for it.
        if ( $this->isShared( $abstract ) ) {
            $this->instances[ $abstract ] = $object;
        }

        return $object;
    }

    /**
     * Resolve the given type from the container.
     *
     * @param  string|callable $abstract
     * @return mixed
     */
    public function make( $abstract ) {
        return $this->resolve( $abstract );
    }

    /**
     * Register an existing instance as shared in the container.
     *
     * @param  string $abstract
     * @param  mixed  $instance
     * @return mixed
     *
     * @access public
     */
    public function instance( $abstract, $instance ) {

        // We'll check to determine if this type has been bound before, and if it has
        // we will fire the rebound callbacks registered with the container and it
        // can be updated with consuming classes that have gotten resolved here.
        $this->instances[ $abstract ] = $instance;

        return $instance;
    }

    /**
     * Determine if a given offset exists.
     *
     * @param  string $key
     * @return bool
     */
    public function offsetExists( $key ): bool {
        return $this->bound( $key );
    }

    /**
     * Get the value at a given offset.
     *
     * @param  string $key
     * @return mixed
     */
    public function offsetGet( $key ): mixed {
        return $this->make( $key );
    }

    /**
     * Set the value at a given offset.
     *
     * @param  string $key
     * @param  mixed  $value
     * @return void
     */
    public function offsetSet( $key, $value ): void {
        $this->bind( $key, $value instanceof Closure ? $value : static fn() => $value );
    }

    /**
     * Unset the value at a given offset.
     *
     * @param  string $key
     * @return void
     */
    public function offsetUnset( $key ): void {
        unset( $this->bindings[ $key ], $this->instances[ $key ], $this->resolved[ $key ] );
    }

    /**
     * Register a shared binding in the container.
     *
     * @param  string               $abstract
     * @param  \Closure|string|null $concrete
     * @return void
     */
    public function singleton( $abstract, $concrete = null ) {
        $this->bind( $abstract, $concrete, true );
    }

    /**
     * Determine if the given concrete is buildable.
     *
     * @param  mixed  $concrete
     * @param  string $abstract
     * @return bool
     */
    protected function isBuildable( $concrete, $abstract ) {
        return $concrete === $abstract || $concrete instanceof Closure;
    }

    /**
     * Get the concrete type for a given abstract.
     *
     * @param  string|callable $abstract
     * @return mixed
     */
    protected function getConcrete( $abstract ) {

        // If we don't have a registered resolver or concrete for the type, we'll just
        // assume each type is a concrete name and will attempt to resolve it as is
        // since the container should be able to resolve concretes automatically.
        if ( isset( $this->bindings[ $abstract ] ) ) {
            return $this->bindings[ $abstract ]['concrete'];
        }

        return $abstract;
    }

    /**
     * Determine if a given type is shared.
     *
     * @param  string $abstract
     * @return bool
     */
    public function isShared( $abstract ) {
        return isset( $this->instances[ $abstract ] ) ||
            ( isset( $this->bindings[ $abstract ]['shared'] ) &&
                $this->bindings[ $abstract ]['shared'] === true );
    }

    /**
     * Instantiate a concrete instance of the given type.
     *
     * @param  \Closure|string $concrete
     * @return mixed
     * @throws \ReflectionException
     *
     * @access protected
     */
    public function build( $concrete ) {

        // If the concrete type is actually a Closure, we will just execute it and
        // hand back the results of the functions, which allows functions to be
        // used as resolvers for more fine-tuned resolution of these objects.
        if ( $concrete instanceof Closure ) {
            return $concrete( $this );
        }

        try {
            $reflector = new ReflectionClass( $concrete );
        } catch ( \ReflectionException $e ) {
            throw new \Exception( "Target class [$concrete] does not exist.", 0, $e );
        }

        $constructor = $reflector->getConstructor();
        if ( is_null( $constructor ) || $constructor->getNumberOfRequiredParameters() === 0 ) {
            return $reflector->newInstance();
        }

        $params = [];
        foreach ( $constructor->getParameters() as $param ) {
            if ( $type = $param->getType() ) {
                $params[] = $this->get( $type->getName() );
            }
        }

        return $reflector->newInstanceArgs( $params );
    }

    /**
     * Drop all of the stale instances and aliases.
     *
     * @param  string $abstract
     * @return void
     */
    protected function dropStaleInstances( $abstract ) {
        unset( $this->instances[ $abstract ] );
    }

    /**
     * Get the Closure to be used when building a type.
     *
     * @param  string $abstract
     * @param  string $concrete
     * @return \Closure
     */
    protected function getClosure( $abstract, $concrete ) {
        return static function ( $container ) use ( $abstract, $concrete ) {
            if ( $abstract === $concrete ) {
                return $container->build( $concrete );
            }

            return $container->resolve( $concrete );
        };
    }

}
