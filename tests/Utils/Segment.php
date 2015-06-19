<?php
namespace tests\Utils;

use Tuum\Respond\Service\SessionStorageInterface;

class Segment implements SessionStorageInterface
{
    public $session = [];

    public $flash = [];

    /**
     * @param string $key
     * @param mixed  $value
     */
    public function set($key, $value)
    {
        $this->session[$key] = $value;
    }

    /**
     * @param string     $key
     * @param null|mixed $alt
     * @return mixed
     */
    public function get($key, $alt = null)
    {
        return array_key_exists($key, $this->session) ? $this->session[$key] : $alt;
    }

    /**
     * @param string $key
     * @param mixed  $value
     */
    public function setFlash($key, $value)
    {
        $this->flash[$key] = $value;
    }

    /**
     * @param string     $key
     * @param null|mixed $alt
     * @return mixed
     */
    public function getFlash($key, $alt = null)
    {
        return array_key_exists($key, $this->flash) ? $this->flash[$key] : $alt;
    }

    /**
     * @param string     $key
     * @param null|mixed $alt
     * @return mixed
     */
    public function getFlashNext($key, $alt = null)
    {
        return $this->getFlash($key, $alt);
    }
}