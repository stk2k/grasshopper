<?php
namespace Grasshopper\curl;

use Grasshopper\Grasshopper;
use Grasshopper\exception\GrasshopperException;
use Grasshopper\debug\CurlDebug;

class CurlHandle
{
    /** @var resource */
    private $ch;

    /** @var CurlRequest */
    private $request;

    /**
     * Constructs CurlHandle object
     *
     * @throws GrasshopperException
     */
    public function __construct()
    {
        $this->ch = curl_init();
        if ( !$this->ch ){
            throw new GrasshopperException('curl_init failed',Grasshopper::ERROR_INIT);
        }
    }

    /**
     * close cURL handle and memory file handle
     */
    public function close()
    {
        if ( $this->ch ){
            curl_close($this->ch);
            $this->ch = null;
        }
    }

    /**
     * Set request object
     *
     * @param CurlRequest $request
     */
    public function setRequest( $request )
    {
        $this->request = $request;

        $options = $request->getOptions();
        $this->setOptions($options);
        if ( $request->isVerbose() ){
            CurlDebug::printOptions($options);
        }
    }

    /**
     * Get request object
     *
     * @return CurlRequest $request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Reset cURL handle
     *
     */
    public function reset()
    {
        curl_reset( $this->ch );
    }

    /**
     * Get cURL handle
     *
     * @return resource
     */
    public function getCurlHandle()
    {
        return $this->ch;
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
        $res = curl_setopt( $this->ch, $option, $value );
        if ( !$res ){
            throw new GrasshopperException('curl_setopt failed',Grasshopper::ERROR_SETOPTION);
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
        $res = curl_setopt_array( $this->ch, $options );
        if ( !$res ){
            throw new GrasshopperException('curl_setopt_array failed',Grasshopper::ERROR_SETOPTIONS);
        }
    }


}