<?php
/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.0 on 2013-01-02 at 14:02:06.
 */

class Mock_Assembla_Model extends Assembla_Model_Ticket {
}

class Not_Mock_Assembla_Model {
}

class Mock_Core_Collection extends Core_Collection {


  public function _getModelClassName() {
    return 'Mock_Assembla_Model';
  }

}

class Core_CollectionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Core_Collection
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
  $this->object = new Mock_Core_Collection;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Core_Collection::setFilters
     * @todo   Implement testSetFilters().
     */
    public function testSetFilters()
    {
      $this->object->setFilters(array('Fixed'));
      $this->assertEquals(array('Fixed'), $this->object->_filters);
    }

    /**
     * @covers Core_Collection::append
     * @todo   Implement testAppend().
     */
    public function testAppend()
    {
      $this->otherObject = new Mock_Core_Collection;
      $mockModel = new Mock_Assembla_Model;
      $mockModel->setData("foo", "bar");
      $mockModel->setData("custom", array("field" => "customvalue"));

      $mockModel2 = new Mock_Assembla_Model;
      $mockModel2->setData("a", "b");

      $mockArray = array("foo" => "bar", "custom" => array("field" => "customvalue"));
      $mockArray2 = array("a" => "b");

      $this->object->push($mockModel);
      $this->otherObject->push($mockModel2);

      $this->object->append($this->otherObject);

      $this->assertEquals(array($mockModel, $mockModel2), $this->object->getCollection());
    }

    /**
     * @covers Core_Collection::toXml
     * @todo   Implement testToXml().
     */
    public function testToXml()
    {
      $mockModel = new Mock_Assembla_Model;
      $mockModel->setData("foo", "bar");
      $mockModel2 = new Mock_Assembla_Model;
      $mockModel2->setData("sig", "kap");
      $mockModel->setData("phi", $mockModel2);

      $mockXml = "<foo>bar</foo>\n<phi><sig>kap</sig>\n</phi>\n";
      $this->object->offsetSet('', $mockModel);
      $this->assertEquals($mockXml, $this->object->toXml(array()));
    }

    /**
     * @covers Core_Collection::toArray
     * @todo   Implement testToArray().
     */
    public function testToArray()
    {
      $mockModel = new Mock_Assembla_Model;
      $mockModel->setData("foo", "bar");
      $mockModel->setData("custom", array("field" => "customvalue"));

      $mockModel2 = new Mock_Assembla_Model;
      $mockModel2->setData("a", "b");

      $mockArray = array("foo" => "bar", "custom" => array("field" => "customvalue"));
      $mockArray2 = array("a" => "b");

      $this->object->offsetSet('', $mockModel);
      $this->object->offsetSet('', $mockModel2);
      $this->assertEquals(array($mockArray, $mockArray2), $this->object->toArray());

      $this->assertEquals(2, $this->object->count());
    }

    /**
     * @covers Core_Collection::toJSON
     * @todo   Implement testToJSON().
     */
    public function testToJSON()
    {
      $mockModel = new Mock_Assembla_Model;
      $mockModel->setData("foo", "bar");
      $mockModel2 = new Mock_Assembla_Model;
      $mockModel2->setData("sig", "kap");
      $mockModel->setData("phi", $mockModel2);

      $mockJson = '[{"foo":"bar","phi":{"sig":"kap"}}]';
      $this->object->offsetSet('', $mockModel);
      $this->assertEquals($mockJson, $this->object->toJson());
    }

    /**
     * @covers Core_Collection::offsetSet
     * @todo   Implement testOffsetSet().
     */
    public function testOffsetSet()
    {
      $mockModel = new Mock_Assembla_Model;
      $mockModel->setData('foo', 'bar');
      $mockModel2 = new Mock_Assembla_Model;
      $mockModel2->setData('fuzzy', array('wuzzy' => 'bear'));

      $this->object->offsetSet('', $mockModel);
      $this->object->offsetSet('', $mockModel2);

      $this->assertEquals(array($mockModel, $mockModel2), $this->object->getCollection());
    }

    public function testOffsetSetThrowsExceptionForInvalidValueType() {
      $this->setExpectedException('Exception',
             'Value must be of type Mock_Assembla_Model');
      $notValid = new Not_Mock_Assembla_Model;
      $this->object->offsetSet('', $notValid);
    }

    /**
     * @covers Core_Collection::offsetUnset
     * @todo   Implement testOffsetUnset().
     */
    public function testOffsetUnset()
    {
      $mockModel = new Mock_Assembla_Model;
      $mockModel2 = new Mock_Assembla_Model;
      $mockModel2->setData('foo', 'bar');
      $this->object->push($mockModel);
      $this->object->push($mockModel2);
      $this->assertEquals(array($mockModel, $mockModel2), $this->object->getCollection());
      $this->object->offsetUnset(0);
      $testArray = array($mockModel, $mockModel2);
      unset($testArray[0]);
      $this->assertEquals($testArray, $this->object->getCollection());
    }

    /**
     * @covers Core_Collection::rewind
     * @todo   Implement testRewind().
     */
    public function testRewind()
    {
      $mockModel = new Mock_Assembla_Model;
      $mockModel2 = new Mock_Assembla_Model;
      $mockModel2->setData('foo', 'bar');
      $this->object->push($mockModel);
      $this->object->push($mockModel2);

      $this->object->next();
      $this->object->rewind();
      $this->assertEquals($mockModel, $this->object->current());
    }

    /**
     * @covers Core_Collection::current
     * @todo   Implement testCurrent().
     */
    public function testCurrent()
    {
      $mockModel = new Mock_Assembla_Model;
      $mockModel2 = new Mock_Assembla_Model;
      $mockModel2->setData('foo', 'bar');
      $this->object->push($mockModel);
      $this->object->push($mockModel2);

      $this->assertEquals($mockModel, $this->object->current());
      $this->object->next();
      $this->assertEquals($mockModel2, $this->object->current());
    }

    /**
     * @covers Core_Collection::key
     * @todo   Implement testKey().
     */
    public function testKey()
    {
      $mockModel = new Mock_Assembla_Model;
      $mockModel2 = new Mock_Assembla_Model;
      $mockModel2->setData('foo', 'bar');
      $this->object->push($mockModel);
      $this->object->push($mockModel2);
      $this->object->next();
      $this->assertEquals(1, $this->object->key());
    }

    /**
     * @covers Core_Collection::next
     * @todo   Implement testNext().
     */
    public function testNext()
    {
      $mockModel = new Mock_Assembla_Model;
      $mockModel2 = new Mock_Assembla_Model;
      $mockModel2->setData('foo', 'bar');
      $this->object->push($mockModel);
      $this->object->push($mockModel2);
      $this->object->next();
      $this->assertEquals(1, $this->object->key());
    }

    /**
     * @covers Core_Collection::valid
     * @todo   Implement testValid().
     */
    public function testValid()
    {
      $mockModel = new Mock_Assembla_Model;
      $this->object->push($mockModel);
      $this->object->next();
      $this->assertEquals(false, $this->object->valid());
    }
}
