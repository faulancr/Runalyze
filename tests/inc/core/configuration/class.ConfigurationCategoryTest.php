<?php
class ConfigurationCategory_MockTester extends ConfigurationCategory {
	public function key() { return 'key'; }
	protected function createHandles() {
		$this->addHandle(new ConfigurationHandle('TEST', new ParameterInt(42)));
		$this->addHandle(new ConfigurationHandle('SECOND', new ParameterString('foobar')));
	}
	public function test() {
		return $this->get('TEST');
	}
	public function Second() {
		return $this->object('SECOND');
	}
}

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.0 on 2014-09-15 at 20:11:37.
 */
class ConfigurationCategoryTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var ConfigurationCategory
	 */
	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		$this->object = new ConfigurationCategory_MockTester();
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown() {
		
	}

	/**
	 * @covers ConfigurationCategory::object
	 */
	public function testAccess() {
		$this->assertEquals(42, $this->object->test());
		$this->assertEquals('foobar', $this->object->Second()->value());

		$this->object->Second()->set('test');
		$this->assertEquals('test', $this->object->Second()->value());
	}

	/**
	 * @covers ConfigurationCategory::keys
	 */
	public function testKeys() {
		$this->assertEquals( array('TEST', 'SECOND'), $this->object->keys() );
	}

}