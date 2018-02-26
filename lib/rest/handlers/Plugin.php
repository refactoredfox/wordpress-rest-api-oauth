<?php

namespace RefactoredFox\REST\Handlers;

class Plugin implements \IteratorAggregate
{
	/**
	 * The plugin's unique ID.
	 *
	 * @var mixed
	 */
	public $pid = null;

	/**
	 * The plugin's name.
	 *
	 * @var mixed
	 */
	public $name = null;

	/**
	 * The plugin's current version number.
	 *
	 * @var string
	 */
	public $currentVersion = null;

	/**
	 * The plugin's latest version.
	 *
	 * @var string
	 */
	public $newVersion = null;

	/**
	 * Any extra data.
	 *
	 * @var array
	 */
	public $extra = array();

	/**
	 * Set a property on the user.
	 *
	 * @param string $key
	 * @param mixed  $value
	 */
	public function __set($key, $value)
	{
		if (isset($this->{$key})) {
			$this->{$key} = $value;
		}
	}

	/**
	 * Get a property from the user.
	 *
	 * @param string $key
	 *
	 * @return mixed
	 */
	public function __get($key)
	{
		if (isset($this->{$key})) {
			return $this->{$key};
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function getIterator()
	{
		return new \ArrayIterator($this);
	}
}
