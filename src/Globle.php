<?php
declare(strict_types = 1);

namespace Brunty\Globle;

use Brunty\Globle\Exceptions\NotFoundException;
use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;

class Globle implements ContainerInterface
{

    /**
     * The items we'll have in our container
     *
     * @var array
     */
    private $items = [];

    /**
     * A separate array of keys for items in our container.
     *
     * @var array
     */
    private $keys = [];

    /**
     * @var array
     */
    private $resolved = [];

    /**
     * This will hold the list of keys for things in our container that are not to be stored as resolved.
     *
     * Getting one of these will return a new instance of whatever it is every single time.
     *
     * @var array
     */
    private $factories = [];

    /**
     * Globle constructor.
     *
     * @param array $items
     * @param array $factories
     */
    public function __construct(array $items = [], array $factories = [])
    {
        $this->items = $items;
        $this->keys = array_keys($items);
        $this->factories = $factories;
    }

    /**
     * Bind into our container
     *
     * @param          $key
     * @param callable $closure
     */
    public function bind($key, callable $closure)
    {
        $this->addItem($key, $closure);
    }

    /**
     * Bind into our container
     *
     * If you bind an item like this, each time you request it from the container, a new instance will be created.
     *
     * @param          $key
     * @param callable $closure
     */
    public function factory($key, callable $closure)
    {
        $this->addItem($key, $closure);
        $this->factories[] = $key;
    }

    /**
     * Finds an entry of the container by its key and returns it.
     *
     * @param string $key Identifier (key) of the entry to look for.
     *
     * @throws NotFoundException  No entry was found for this identifier.
     * @throws ContainerException Error while retrieving the entry.
     *
     * @return mixed Entry.
     */
    public function get($key)
    {
        if ( ! $this->has($key)) {
            throw new NotFoundException(sprintf('%s does not exist as a binding', $key));
        }

        return $this->resolve($key);
    }

    /**
     * Returns true if the container can return an entry for the given key.
     * Returns false otherwise.
     *
     * @param string $key Identifier (key) of the entry to look for.
     *
     * @return boolean
     */
    public function has($key) : bool
    {
        return in_array($key, $this->keys);
    }

    /**
     * @param $key
     *
     * @return mixed
     */
    private function resolve($key)
    {
        if (isset($this->resolved[$key])) {
            return $this->resolved[$key];
        }

        $item = $this->items[$key]($this);
        if ( ! in_array($key, $this->factories)) {
            $this->resolved[$key] = $item;
        }

        return $item;

    }

    /**
     * @param          $key
     * @param callable $closure
     */
    private function addItem($key, callable $closure)
    {
        $this->items[$key] = $closure;
        $this->keys[] = $key;
    }
}
