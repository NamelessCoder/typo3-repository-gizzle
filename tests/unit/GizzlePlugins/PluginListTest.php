<?php
namespace NamelessCoder\TYPO3RepositoryGizzle\Tests\Unit\GizzlePlugins;
use NamelessCoder\TYPO3RepositoryGizzle\GizzlePlugins\PluginList;

/**
 * Class PluginListTest
 */
class PluginListTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @return void
	 */
	public function testInitializeSettings() {
		$instance = new PluginList();
		$settings = array('foo' => 'bar');
		$instance->initialize($settings);
		$this->assertAttributeEquals($settings, 'settings', $instance);
	}

	/**
	 * @return void
	 */
	public function testGetPluginClassNames() {
		$instance = new PluginList();
		$classes = $instance->getPluginClassNames();
		$this->assertContains('NamelessCoder\\TYPO3RepositoryGizzle\\GizzlePlugins\\ExtensionReleasePlugin', $classes);
	}

}
