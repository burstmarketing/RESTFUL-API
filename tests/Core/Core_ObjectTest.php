<?php

if (!defined("ASSEMBLA_REST_API_ROOT")) {
  define("ASSEMBLA_REST_API_ROOT", realpath(dirname(__DIR__)));
}

require_once ASSEMBLA_REST_API_ROOT . '/../Autoload.php';
require_once ASSEMBLA_REST_API_ROOT . '/../Core/Object.php';

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.0 on 2012-12-23 at 10:50:22.
 */
class Core_ObjectTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Core_Object
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
      $this->object = new Core_Object;
    }

    public function testHasDataChanges() {
      // Tests the actual attribute is equal to what the method returns
      $this->assertAttributeEquals($this->object->hasDataChanges(),
				   '_hasDataChanges', $this->object);
    }

    public function testHasDataChangesDefaultsToFalse() {
      $this->assertAttributeEquals(false, '_hasDataChanges', $this->object);
    }

    public function testHasDataChangesWorksWithSetData() {
      $this->object->setData('foo', 'bar');
      $this->assertAttributeEquals(true, '_hasDataChanges', $this->object);
    }

    public function testHasDataChangesWorksWithUnsetData() {
      // non-existent key, but unset doesn't care
      $this->object->unsetData('some-key');
      $this->assertAttributeEquals(true, '_hasDataChanges', $this->object);
    }

    public function testHasDataChangesWorksWithCallSet() {
      $this->object->setFoo('bar');
      $this->assertAttributeEquals(true, '_hasDataChanges', $this->object);
    }

    public function testAddData() {
      $this->object->addData(array('example1' => 'value1',
				   'example2' => 'value2'));

      $this->assertAttributeContains('value1', '_data', $this->object);
      $this->assertAttributeContains('value1', '_data', $this->object);
    }

    public function testAddDataReturnsCoreObject() {
      $this->assertInstanceOf('Core_Object',
			      $this->object->addData(array()));
    }

    /**
     * I don't think it's extremely clear what's going on here,
     * but passing an array to setData as the key, should
     * set all of _data to be that array, so we're adding a key
     * of foo, then overwriting _data, and ensuring foo doesn't
     * still exist.
     **/
    public function testSetDataWithArrayOverwritesData() {
      $this->object->setData('foo', 'bar');

      $this->object->setData(array('key1' => 'value1',
				   'key2' => 'value2'));

      $this->assertAttributeNotContains('foo', '_data', $this->object);
    }

    public function testSetDataReturnsCoreObject() {
      $this->assertInstanceOf('Core_Object',
			      $this->object->setData('key', 'value'));
    }

    public function testSetDataSetsData() {
      $this->object->setData('some-key', 'some-value');

      $this->assertAttributeContains('some-value', '_data', $this->object);
    }

    public function testUnsetDataUnsetsAllDataWhenPassedNull() {
      $this->object->setData(array('key1' => 'value1',
				   'key2' => 'value2'));

      $this->object->unsetData();

      $this->assertAttributeEmpty('_data', $this->object);
    }

    public function testUnsetDataUnsetsData() {
      $this->object->setData('test', 'testval');

      $this->object->unsetData('test');

      $this->assertAttributeNotContains('test', '_data', $this->object);
    }

    public function testUnsetDataReturnsCoreObject() {
      $this->assertInstanceOf('Core_Object', $this->object->unsetData(null));
      $this->assertInstanceOf('Core_Object', $this->object->unsetData('key'));
    }

    public function testGetDataReturnsAllWhenGivenNoKey() {
      $sample_data = array('key1' => 'value1',
			   'key2' => 'value2');

      $this->object->addData($sample_data);

      $this->assertAttributeEquals($this->object->getData(), '_data', $this->object);
    }

    public function testGetDataReturnsNullForNonExistentKey() {
      $this->assertNull($this->object->getData('some-non-existent-key'));
    }

    public function testGetDataReturnsKeyDataWithNoSlashes() {
      $this->object->setData('examplekey', 'exampleval');

      $this->assertEquals('exampleval', $this->object->getData('examplekey'));
    }

    // Needs many more test cases...
    public function testGetDataWithSlashes() {
      $this->object->setData('a', array('b' => 'value'));

      $this->assertEquals('value', $this->object->getData('a/b'));
    }

    /**
     * @covers Core_Object::__toArray
     * @todo   Implement test__toArray().
     */
    public function test__toArray()
    {
      $this->object->setData("foo", "bar");
      $mock = new Core_Object;
      $mock->setData("is", "cool");
      $this->object->setData("mike", $mock);

      $testArray = array("foo" => "bar", "mike" => array("is" => "cool"));

      $this->assertEquals($testArray, $this->object->__toArray());
    }

    public function test__toArrayReturnsArray() {
      $this->assertTrue(is_array($this->object->__toArray()));
    }

    /**
     * @covers Core_Object::toArray
     * @todo   Implement testToArray().
     */
    public function testToArray()
    {
      $this->assertEquals($this->object->toArray(), $this->object->__toArray());
    }

    /**
     * @covers Core_Object::toXml
     * @todo   Implement testToXml().
     */
    public function testToXml()
    {
      $this->object->setData("foo", "bar");
      $mock = new Core_Object;
      $mock->setData("sig", "kap");
      $this->object->setData("phi", $mock);

      $testXml = "<foo>bar</foo>\n<phi><sig>kap</sig>\n</phi>\n";

      $this->assertEquals($testXml, $this->object->toXml());
    }

    /**
     * @covers Core_Object::toJson
     * @todo   Implement testToJson().
     */
    public function testToJson()
    {
      $this->object->setData("foo", "bar");
      $mock = new Core_Object;
      $mock->setData("sig", "kap");
      $this->object->setData("phi", $mock);
      $testJson = '{"foo":"bar","phi":{"sig":"kap"}}';
      $this->assertEquals($testJson, $this->object->toJson());
    }


    /**
     * @covers Core_Object::__call
     * @todo   Implement test__call().
     */
    public function test__callSet() {
      $this->object->setFoo("bar");
      $this->refObject = new ReflectionClass($this->object);
      $this->_data = $this->refObject->getProperty('_data');
      $this->_data->setAccessible(true);
      $this->_data = $this->_data->getValue($this->object);
      $this->assertEquals("bar", $this->_data['foo']);
    }

    public function test__callGet() {
      $this->object->setFoo("bar");
      $this->refObject = new ReflectionClass($this->object);
      $this->_data = $this->refObject->getProperty('_data');
      $this->_data->setAccessible(true);
      $this->_data = $this->_data->getValue($this->object);
      $this->assertEquals($this->_data['foo'], $this->object->getFoo());
    }

    public function test__callUns() {
      $this->object->setData("foo", "bar");
      $this->object->unsFoo();
      $this->refObject = new ReflectionClass($this->object);
      $this->_data = $this->refObject->getProperty('_data');
      $this->_data->setAccessible(true);
      $this->_data = $this->_data->getValue($this->object);
      $this->assertFalse(isset($this->_data['foo']));
    }

    public function test__call()
    {
      $this->object->setFoo("bar");
      $this->assertEquals("bar", $this->object->getFoo());

      $this->assertEquals(true, $this->object->hasFoo());
      $this->object->unsFoo();
      $this->assertEquals(false, $this->object->hasFoo());
    }

    public function testIsEmpty() {
      $this->assertTrue($this->object->isEmpty());

      $this->object->setData('key', 'val');

      $this->assertFalse($this->object->isEmpty());
    }

    /**
     * @covers Core_Object::getOrigData
     * @todo   Implement testGetOrigData().
     */
    public function testGetOrigData()
    {
      $this->object->setData("foo", "bar");
      $this->object->setOrigData();
      $this->assertEquals($this->object->getData(), $this->object->getOrigData());

      $this->assertEquals("bar", $this->object->getOrigData("foo"));
    }

    /**
     * @covers Core_Object::setOrigData
     * @todo   Implement testSetOrigData().
     */
    public function testSetOrigData()
    {
      $this->object->setData("foo", "bar");
      $this->object->setOrigData();
      $this->assertEquals($this->object->getOrigData(), $this->object->getData());

      $this->object->setOrigData("foo", "baz");
      $this->assertEquals("baz", $this->object->getOrigData("foo"));
    }

    /**
     * @covers Core_Object::dataHasChangedFor
     * @todo   Implement testDataHasChangedFor().
     */
    public function testDataHasChangedFor()
    {
      $this->object->setData("bar", "baz");
      $this->object->setOrigData();
      $this->object->setData("bar", "foo");
      $this->assertTrue($this->object->dataHasChangedFor("bar"));
    }
}
