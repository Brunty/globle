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
    private $ids = [];

    /**
     * @var array
     */
    private $resolved = [];

    /**
     * This will hold the list of IDs for things in our container that are not to be stored as resolved.
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
        $this->ids = array_keys($items);
        $this->factories = $factories;
    }

    /**
     * Bind into our container
     *
     * @param          $id
     * @param callable $closure
     */
    public function bind($id, callable $closure)
    {
        $this->addItem($id, $closure);
    }

    /**
     * Bind into our container
     *
     * If you bind an item like this, each time you request it from the container, a new instance will be created.
     *
     * @param          $id
     * @param callable $closure
     */
    public function factory($id, callable $closure)
    {
        $this->addItem($id, $closure);
        $this->factories[] = $id;
    }

    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @throws NotFoundException  No entry was found for this identifier.
     * @throws ContainerException Error while retrieving the entry.
     *
     * @return mixed Entry.
     */
    public function get($id)
    {
        if ( ! $this->has($id)) {
            throw new NotFoundException(sprintf('%s does not exist as a binding', $id));
        }

        return $this->resolve($id);
    }

    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return boolean
     */
    public function has($id) : bool
    {
        return in_array($id, $this->ids);
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    private function resolve($id)
    {
        if (isset($this->resolved[$id])) {
            return $this->resolved[$id];
        }

        $item = $this->items[$id]($this);
        if ( ! in_array($id, $this->factories)) {
            $this->resolved[$id] = $item;
        }

        return $item;

    }

    /**
     * @param          $id
     * @param callable $closure
     */
    private function addItem($id, callable $closure)
    {
        $this->items[$id] = $closure;
        $this->ids[] = $id;
    }
}