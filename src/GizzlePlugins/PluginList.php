<?php
namespace NamelessCoder\TYPO3RepositoryGizzle\GizzlePlugins;

use NamelessCoder\Gizzle\PluginListInterface;

/**
 * Class PluginList
 */
class PluginList implements PluginListInterface {

	/**
	 * @var array
	 */
	protected $settings = array();

	/**
	 * Initialize the plugin lister with an array of settings.
	 *
	 * @param array $settings
	 * @return void
	 */
	public function initialize(array $settings) {
		$this->settings = $settings;
	}

	/**
	 * Get all class names of plugins delivered from implementer package.
	 *
	 * @return string[]
	 */
	public function getPluginClassNames() {
		return array(
			'NamelessCoder\\TYPO3RepositoryGizzle\\GizzlePlugins\\ExtensionReleasePlugin'
		);
	}

}
