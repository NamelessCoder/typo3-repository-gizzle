<?php
namespace NamelessCoder\TYPO3RepositoryGizzle\GizzlePlugins;

use NamelessCoder\Gizzle\Payload;

/**
 * Class ExtensionRepositoryReleasePlugin
 */
class ExtensionRepositoryReleasePlugin extends \NamelessCoder\GizzleTYPO3Plugins\GizzlePlugins\ExtensionRepositoryReleasePlugin {

	/**
	 * Validates the credentials "file" - by inspecting
	 * the potential credentials returned from the
	 * readUploadCredentials() method.
	 *
	 * @param string $credentialsFile
	 * @throws \RuntimeException
	 */
	protected function validateCredentialsFile($credentialsFile) {
		$credentials = $this->readUploadCredentials(NULL);
		if (2 !== count($credentials)) {
			throw new \RuntimeException(
				'Invalid credentials provided; must be a string of "username:password" including the colon'
			);
		}
	}

	/**
	 * @param string $credentialsFile
	 * @return array
	 */
	protected function readUploadCredentials($credentialsFile) {
		$uri = $this->readRequestUriParameters();
		return explode(':', (string) end($uri));
	}

	/**
	 * Returns a working directory name either specified in the
	 * URL as very first segment, or if that segment does not
	 * contain the directory name, taken from the name of the
	 * repository from which Payload comes.
	 *
	 * @param Payload $payload
	 * @return string
	 */
	protected function getWorkingDirectoryName(Payload $payload) {
		$uri = $this->readRequestUriParameters();
		$suspect = reset($uri);
		$directory = $payload->getRepository()->getName();
		if (FALSE !== $suspect && FALSE === strpos($suspect, ':')) {
			$directory = $suspect;
		}
		return $directory;
	}

	/**
	 * @return array
	 */
	protected function readRequestUriParameters() {
		return explode('/', trim($_SERVER['REQUEST_URI'], '/'));
	}

}