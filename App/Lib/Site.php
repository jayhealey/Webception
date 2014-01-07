<?php namespace App\Lib;

/*
 * This file is part of the Webception package.
 *
 * (c) James Healey <jayhealey@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Site
{
    /**
     * List of all sites set in the Codeception config.
     *
     * @var array
     */
    private $sites = array();

    /**
     * Hash value of the current site.
     *
     * @var false if not set; string if set.
     */
    private $hash = FALSE;

    /**
     * On construct, prepare the site details and the chosen site.
     *
     * @param array  $config
     * @param string $hash
     */
    public function __construct($sites = array())
    {
        // Filter the sites by creating unique hashes.
        $this->sites = $this->prepare($sites);
    }

    /**
     * Set the current site.
     *
     * @param string if set, false if not.
     */
    public function set($hash=FALSE)
    {
        // If hash matched in sites, set hash.
        if (isset($this->sites[$hash])) {
            $this->hash = $hash;
        } elseif ($hash == FALSE && sizeof($this->sites) > 0) {
            // If no site set, but sites available,
            // pick the first in the list.
            reset($this->sites);
            $this->hash = key($this->sites);
        } else {
            // If no site found or none available,
            // set as false.
            $this->hash = FALSE;
        }
    }

    /**
     * Return the name of the current site.
     *
     * @return string
     */
    public function getName()
    {
        return $this->get('name');
    }

    /**
     * Return the full path to the Codeception.yml for the current site.
     *
     * @return string
     */
    public function getConfig()
    {
        return $this->get('path');
    }

    /**
     * Return just the path of the Codeception.yml file for the current site.
     *
     * @return string
     */
    public function getConfigPath()
    {
        $path = $this->get('path');

        return ($path !== FALSE) ?
             dirname($path) . '/' : FALSE;
    }

    /**
     * Return just the filename of the configuration file for the current site.
     *
     * Usually returns 'Codeception.yml'.
     *
     * @return string
     */
    public function getConfigFile()
    {
        $path = $this->get('path');

        return ($path !== FALSE) ?
             basename($path) : FALSE;
    }

    /**
     * Getter for site details.
     *
     * @param  string $value Name of the required field.
     * @return string (or FALSE if $value not set)
     */
    public function get($value)
    {
        return ($this->hash !== FALSE) && isset($this->sites[$this->hash][$value])
                ? $this->sites[$this->hash][$value] : FALSE;
    }

    /**
     * Return the hash of the current site.
     *
     * @return string (or FALSE if not set)
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * Return full list of sites.
     *
     * @return array
     */
    public function getSites()
    {
        return $this->sites;
    }

    /**
     * Given a list of sites, prepare a unique hash
     * and tidy up the path to the Codeception configuration.
     *
     * @param  array $sites List of sites set in Codeception.php configuration.
     * @return array $filtered
     */
    public function prepare($sites = array())
    {
        $filtered = array();

        foreach ($sites as $name => $path) {
            $filtered[md5($name)] = array(
                'name' => $name,
                'path' => realpath(dirname($path)) .'/' . basename($path)
            );
        }

        return $filtered;
    }

    /**
     * Confirm if the Site details are valid.
     *
     * It counts that Sites are set and a hash was decided on __construct().
     *
     * @return boolean
     */
    public function ready()
    {
        return sizeof($this->sites) > 0 && $this->hash !== FALSE;
    }

    /**
     * Confirm that Site class has more than one option available.
     *
     * This is used on the Webception front-end to decide
     * if a dropdown is required to swap sites.
     *
     * @return boolean Checks if details are ready and more than one site.
     */
    public function hasChoices()
    {
        return $this->ready() && sizeof($this->sites) > 1;
    }
}
