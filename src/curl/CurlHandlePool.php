<?php
namespace Grasshopper\curl;

class CurlHandlePool
{
    /** @var CurlHandle[] */
    private $availables;

    /** @var CurlHandle[] */
    private $in_use;

    /**
     * Constructs CurlHandlePool object
     *
     * @param integer $pool_size
     */
    public function __construct( $pool_size )
    {
        $this->in_use = array();

        for($i=0; $i<$pool_size; $i++){
            $handle = new CurlHandle();
            $key = (string)$handle->getCurlHandle();
            $this->availables[$key] = $handle;
        }
    }

    /**
     * Get available count
     *
     * @return integer
     */
    public function availableCount()
    {
        return count($this->availables);
    }

    /**
     * Get in-used count
     *
     * @return CurlHandle $handle
     */
    public function inUseCount()
    {
        return count($this->in_use);
    }

    /**
     * Get handle object
     *
     * @return CurlHandle $handle
     */
    public function acquireObject()
    {
        /** @var CurlHandle $available */
        if ( count($this->availables) > 0 ){
            $available = array_shift($this->availables);
            $available->reset();
        }
        else{
            $available = new CurlHandle();
        }
        $key = (string)$available->getCurlHandle();
        $this->in_use[$key] = $available;
        return $available;
    }

    /**
     * Remove handle object by handle
     *
     * @param CurlHandle $handle
     */
    public function releaseObject( $handle )
    {
        $key = (string)$handle->getCurlHandle();
        $this->availables[$key] = $handle;
        if ( isset($this->in_use[$key]) ){
            unset($this->in_use[$key]);
        }
    }

    /**
     * Find handle object by handle
     *
     * @param resource $handle
     *
     * @return CurlHandle
     */
    public function findObject( $handle )
    {
        $key = (string)$handle;
        return isset($this->in_use[$key]) ? $this->in_use[$key] : null;
    }

}