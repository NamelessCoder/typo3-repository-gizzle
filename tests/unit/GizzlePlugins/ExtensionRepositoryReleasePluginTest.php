<?php
namespace NamelessCoder\TYPO3RepositoryGizzle\Tests\Unit\GizzlePlugins;

use NamelessCoder\Gizzle\Payload;
use NamelessCoder\Gizzle\Repository;
use NamelessCoder\TYPO3RepositoryGizzle\GizzlePlugins\ExtensionRepositoryReleasePlugin;

/**
 * Class ExtensionRepositoryReleasePluginTest
 */
class ExtensionRepositoryReleasePluginTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @return void
	 */
	public function testValidateCredentialsFileReturnsNullOnNoError() {
		$credentials = array('foo', 'bar');
		$instance = $this->getMock(ExtensionRepositoryReleasePlugin::class, array('readUploadCredentials'));
		$instance->expects($this->once())->method('readUploadCredentials')->willReturn($credentials);
		$method = new \ReflectionMethod($instance, 'validateCredentialsFile');
		$method->setAccessible(TRUE);
		$result = $method->invokeArgs($instance, array(NULL));
		$this->assertNull($result);
	}

	/**
	 * @return void
	 */
	public function testValidateCredentialsFileThrowsExceptionOnMissingUriParameters() {
		$credentials = array('foo');
		$instance = $this->getMock(ExtensionRepositoryReleasePlugin::class, array('readUploadCredentials'));
		$instance->expects($this->once())->method('readUploadCredentials')->willReturn($credentials);
		$method = new \ReflectionMethod($instance, 'validateCredentialsFile');
		$method->setAccessible(TRUE);
		$this->setExpectedException('RuntimeException');
		$method->invokeArgs($instance, array(NULL));
	}

	/**
	 * @return void
	 */
	public function testReadUploadCredentials() {
		$uri = array('foo:bar');
		$instance = $this->getMock(ExtensionRepositoryReleasePlugin::class, array('readRequestUriParameters'));
		$instance->expects($this->once())->method('readRequestUriParameters')->willReturn($uri);
		$method = new \ReflectionMethod($instance, 'readUploadCredentials');
		$method->setAccessible(TRUE);
		$result = $method->invokeArgs($instance, array(NULL));
		$this->assertEquals(array('foo', 'bar'), $result);
	}

	/**
	 * @dataProvider getWorkingDirectoryTestValues
	 * @param array $uri
	 * @param string $expected
	 * @return void
	 */
	public function testGetWorkingDirectoryName(array $uri, $expected) {
		$instance = $this->getMock(ExtensionRepositoryReleasePlugin::class, array('readRequestUriParameters'));
		$instance->expects($this->once())->method('readRequestUriParameters')->willReturn($uri);
		$method = new \ReflectionMethod($instance, 'getWorkingDirectoryName');
		$method->setAccessible(TRUE);
		$repository = new Repository();
		$repository->setName('default');
		$payload = $this->getMock(Payload::class, array('getRepository'), array(), '', FALSE);
		$payload->expects($this->once())->method('getRepository')->willReturn($repository);
		$result = $method->invokeArgs($instance, array($payload));
		$this->assertEquals($expected, $result);
	}

	/**
	 * @return array
	 */
	public function getWorkingDirectoryTestValues() {
		return array(
			array(array(), 'default'),
			array(array('foo:bar'), 'default'),
			array(array('foo'), 'foo'),
		);
	}

	/**
	 * @return void
	 */
	public function testReadRequestUriParameters() {
		$instance = new ExtensionRepositoryReleasePlugin();
		$method = new \ReflectionMethod($instance, 'readRequestUriParameters');
		$method->setAccessible(TRUE);
		$_SERVER['REQUEST_URI'] = '/foo/bar/';
		$result = $method->invoke($instance);
		$this->assertEquals(array('foo', 'bar'), $result);
		unset($_SERVER['REQUEST_URI']);
	}

	/**
	 * @return void
	 */
	public function testReadRequestUriParametersReturnsHttpAuthenticationIfSet() {
		$instance = new ExtensionRepositoryReleasePlugin();
		$method = new \ReflectionMethod($instance, 'readRequestUriParameters');
		$method->setAccessible(TRUE);
		$_SERVER['REQUEST_URI'] = '/foo/bar/';
		$_SERVER['PHP_AUTH_USER'] = 'dummy';
		$_SERVER['PHP_AUTH_PW'] = 'password';
		$result = $method->invoke($instance);
		$this->assertEquals(array('dummy:password'), $result);
		unset($_SERVER['REQUEST_URI'], $_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']);
	}

}
