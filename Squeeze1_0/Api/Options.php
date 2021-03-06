<?php

namespace Squeeze1_0\Api
{
  /**
   * A class to get, set and manage settings stored in the WordPress database.
   *
   * For each option, create a new instance of this API.
   * @since 1.0
   */
  class Options
  {

    /**
     * @var string
     * @access private
     * @since 1.0
     */
    private $key;

    /**
     * @var mixed
     * @access private
     * @since 1.0
     */
    private $value;

    /**
     * @var string
     * @access private
     * @since 1.0
     */
    private $encoding_type;

    /**
     * Take a given key, fetch the value from the database and attempt to determine encoding type.
     * @param string $key
     * @return null
     * @access public
     * @since 1.0
     */
    public function __construct($key)
    {
      $this->key = $key;
      $this->value = get_option($key);

      if (!$this->value) {
        $this->encoding_type = 'json';
      }
      else {
        if ($this->isJson($this->value)) {
          $this->value = json_decode($this->value);
          $this->encoding_type = 'json';
        }
      }
    }

    /**
     * Return the stored value.
     * @return mixed
     * @access public
     * @since 1.0
     */
    public function get() {
      return $this->value;
    }

    /**
     * Update the value
     * @param mixed $value
     * @return Options $this
     * @access public
     * @since 1.0
     */
    public function set($value) {
      $this->value = $value;
      return $this;
    }

    /**
     * If the stored value is an array, add a value to the end.
     * @param string $value
     * @return Options $this
     * @access public
     * @since 1.0
     */
    public function push($value) {
      if (!is_array($this->value)) return false;

      if (!is_array($value)) {
        $value = array($value);
      }

      $this->value = array_merge($this->value, $value);

      return $this;
    }

    /**
     * Save the value to the database.
     * If the value was previously stored as json, it'll be re-encoded as json.
     * Any other types will be left to WordPress to determine.
     * @access public
     * @return bool
     * @since 1.0
     */
    public function save()
    {
      $value = $this->value;
      if ($this->encoding_type == 'json' && (is_object($this->value) || is_array($this->value)) ) {
        $value = json_encode($value);
      }

      update_option($this->key, $value);
      return true;
    }

    /**
     * Determine if a string is json-encoded
     * @access private
     * @param string $string
     * @return bool
     * @since 1.0
     */
    private function isJson($string)
    {
      json_decode($string);
      return (json_last_error() == JSON_ERROR_NONE);
    }
  }
}