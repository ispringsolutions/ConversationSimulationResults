<?php

class RequestParameters implements ArrayAccess, Countable
{
    /**
     * @var array
     */
    private $params;

    /**
     * @param array $params
     */
    public function __construct(array $params)
    {
        $this->params = $params;
    }

    /**
     * @param string $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->params[$offset]);
    }

    /**
     * @param string $offset
     * @return string|null
     */
    public function offsetGet($offset)
    {
        return $this->offsetExists($offset) ? trim($this->params[$offset]) : null;
    }

    /**
     * @param string $offset
     * @param mixed  $value
     */
    public function offsetSet($offset, $value)
    {
        $this->params[$offset] = $value;
    }

    /**
     * @param string $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->params[$offset]);
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->params);
    }
}
