<?php

/*
 * This file is part of the Webception package.
 *
 * (c) James Healey <jayhealey@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class SiteClassTestsTest extends \Codeception\TestCase\Test
{
   /**
    * @var \CodeGuy
    */
    protected $codeGuy;

    private $config         = FALSE;
    private $site           = FALSE;
    private $site_1         = 'Webception';
    private $site_2         = 'Another Webception';
    private $multiple_sites = array();
    private $single_site    = array();

    protected function _before()
    {
        // Default Site List
        $this->multiple_sites = array(
            'Webception'  => dirname(__FILE__) .'/../_config/codeception_pass.yml',
            'Another Webception'  => dirname(__FILE__) .'/../_config/codeception_pass.yml'
        );

        $this->single_site = array(
            'Another Webception'  => dirname(__FILE__) .'/../_config/codeception_pass.yml',
        );

        // Load the Codeception config
        $this->config = require(dirname(__FILE__) . '/../_config/codeception_pass.php');

        // Set the hashes!
        $this->hash_1 = md5($this->site_1);
        $this->hash_2 = md5($this->site_2);
    }

    protected function _after()
    {
    }

    public function testSettingNoSites()
    {
        $site = new \App\Lib\Site();
        $this->assertFalse($site->ready());
        $this->assertFalse($site->getHash());
        $this->assertFalse($site->hasChoices());
        $this->assertFalse($site->getName());
        $this->assertFalse($site->getConfig());
        $this->assertFalse($site->getConfigPath());
        $this->assertFalse($site->getConfigFile());
        $this->assertEquals(count($site->getSites()), 0);
        $this->assertEquals($site->getSites(), array());
    }

    public function testSettingSites()
    {
        $site = new \App\Lib\Site($this->config['sites']);
        $this->assertEquals(count($site->getSites()), 2);
        $filtered = $site->prepare($this->multiple_sites);
        $this->assertEquals($filtered, $site->getSites());
    }

    public function testSettingInvalidSite()
    {
        $site = new \App\Lib\Site($this->config['sites']);
        $this->assertFalse($site->ready());
        $site->set(md5('junk-site'));
        $this->assertFalse($site->ready());
        $this->assertFalse($site->getHash());
        $this->assertFalse($site->hasChoices());
        $this->assertFalse($site->getName());
        $this->assertFalse($site->getConfig());
        $this->assertFalse($site->getConfigPath());
        $this->assertFalse($site->getConfigFile());
        $this->assertEquals(count($site->getSites()), 2);
    }

    public function testSettingMultipleValidSites()
    {
        $site = new \App\Lib\Site($this->config['sites']);

        $filtered = $site->prepare($this->multiple_sites);

        $this->assertEquals(count($site->getSites()), 2);

        $this->assertFalse($site->hasChoices());
        $this->assertFalse($site->ready());

        // Set to first site.
        $site->set($this->hash_1);
        $this->assertTrue($site->ready());
        $this->assertEquals($site->getName(), $this->site_1);
        $this->assertEquals($this->hash_1, $site->getHash());
        $this->assertEquals($filtered[$this->hash_1]['path'], $site->getConfig());
        $this->assertEquals(basename($filtered[$this->hash_1]['path']), $site->getConfigFile());
        $this->assertEquals(dirname($filtered[$this->hash_1]['path']) .'/', $site->getConfigPath());

        // Swap the site over!
        $site->set($this->hash_2);
        $this->assertTrue($site->ready());
        $this->assertNotEquals($site->getName(), $this->site_1);
        $this->assertEquals($site->getName(), $this->site_2);
        $this->assertEquals($this->hash_2, $site->getHash());
        $this->assertEquals($filtered[$this->hash_2]['path'], $site->getConfig());
        $this->assertEquals(basename($filtered[$this->hash_2]['path']), $site->getConfigFile());
        $this->assertEquals(dirname($filtered[$this->hash_2]['path']) .'/', $site->getConfigPath());
        $this->assertEquals(count($site->getSites()), 2);

        // Sites are set and more than one site available
        $this->assertTrue($site->hasChoices());

    }

    public function testSettingSingleValidSites()
    {
        $sites = $this->config['sites'];

        // Remove the first site
        unset($sites[$this->site_1]);

        $site = new \App\Lib\Site($sites);

        $filtered = $site->prepare($this->single_site);

        // Confirm the single set has been set
        $this->assertEquals($filtered, $site->getSites());
        $this->assertEquals(count($site->getSites()), 1);
        $this->assertFalse($site->hasChoices());
        $this->assertFalse($site->ready());

        // Set to first site.
        $site->set($this->hash_2);
        $this->assertTrue($site->ready());
        $this->assertEquals($site->getName(), $this->site_2);
        $this->assertEquals($this->hash_2, $site->getHash());
        $this->assertEquals($filtered[$this->hash_2]['path'], $site->getConfig());
        $this->assertEquals(basename($filtered[$this->hash_2]['path']), $site->getConfigFile());
        $this->assertEquals(dirname($filtered[$this->hash_2]['path']) .'/', $site->getConfigPath());

        // Check there's only one site but we don't have choices
        $this->assertEquals(count($site->getSites()), 1);
        $this->assertFalse($site->hasChoices());
    }

}
