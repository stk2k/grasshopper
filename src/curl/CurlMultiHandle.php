<?php
namespace Grasshopper\curl;

use Grasshopper\Grasshopper;
use Grasshopper\exception\GrasshopperException;

class CurlMultiHandle
{
    /** @var resource */
    private $mh;

    /**
     * Constructs CurlMultiHandle object
     *
     * @throws GrasshopperException
     */
    public function __construct()
    {
        $this->mh = curl_multi_init();
        if ( !$this->mh ){
            throw new GrasshopperException('curl_multi_init failed',Grasshopper::ERROR_MULTI_INIT);
        }
    }

    /**
     * Destructs CurlMultiHandle object
     */
    public function __destruct()
    {
        $this->close();
    }

    /**
     * close cURL handle and memory file handle
     */
    public function close()
    {
        if ( $this->mh ){
            curl_multi_close( $this->mh );
            $this->mh = null;
        }
    }

    /**
     * Get cURL multi handle
     *
     * @return resource
     */
    public function getCurlHandle()
    {
        return $this->mh;
    }

    /**
     * Set cURL option
     *
     * @param integer $option
     * @param mixed $value
     *
     * @throws GrasshopperException
     */
    public function setOption( $option, $value )
    {
        $res = curl_multi_setopt( $this->mh, $option, $value );
        if ( !$res ){
            throw new GrasshopperException('curl_setopt failed',Grasshopper::ERROR_M_SETOPTION);
        }
    }

    /**
     * Set cURL option
     *
     * @param array $options
     *
     * @throws GrasshopperException
     */
    public function setOptions( $options )
    {
        foreach( $options as $key => $value ){
            $res = curl_multi_setopt( $this->mh, $key, $value );
            if ( !$res ){
                throw new GrasshopperException('curl_setopt_array failed',Grasshopper::ERROR_M_SETOPTIONS);
            }
        }
    }

    /**
     * Add cURL handle
     *
     * @param CurlHandle $handle
     *
     * @return integer
     */
    public function addHandle( $handle )
    {
        return curl_multi_add_handle( $this->mh, $handle->getCurlHandle() );
    }

    /**
     * Remove cURL handle
     *
     * @param CurlHandle $handle
     *
     * @return integer
     */
    public function removeHandle( $handle )
    {
        return curl_multi_remove_handle( $this->mh, $handle->getCurlHandle() );
    }

    /**
     * Wait for activity on any curl_multi connection
     *
     * @return integer
     */
    public function select()
    {
        return curl_multi_select( $this->mh );
    }

    /**
     * Run the sub-connections of the current cURL handle
     *
     * @param integer& $running
     *
     * @return integer
     */
    public function execute( &$running )
    {
        return curl_multi_exec($this->mh, $running);
    }

    /**
     * Run the sub-connections of the current cURL handle
     *
     * @param integer& $msgs_in_queue
     *
     * @return array
     */
    public function getInfo( &$msgs_in_queue )
    {
        return curl_multi_info_read($this->mh, $msgs_in_queue);
    }


}