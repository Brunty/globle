<?php
declare(strict_types = 1);

namespace Brunty\Globle;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Interop\Container\Exception\NotFoundException;

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
     * Globle constructor.
     *
     * @param array $items
     */
    public function __construct(array $items = [])
    {
        $this->items = $items;
        $this->keys = array_fill_keys(array_keys($items), 'Globle');
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
            throw new \InvalidArgumentException(sprintf('%s does not exist as a binding', $id));
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
        return isset($this->keys[$id]);
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


    }
}