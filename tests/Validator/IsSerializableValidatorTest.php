<?php
use PHPUnit\Framework\TestCase;

use Pes\Validator\IsSerializableValidator;

class SerializableClassForTest implements \Serializable {
    public function serialize() {
        return 'To je série!';
    }
    public function unserialize($serialized) {
        return;
    }
}
/**
 * Description of IndexedCollectionTest
 *
 * @author pes2704
 */
class IsSerializableValidatorTest extends TestCase {
    
    public function testIsValid() {
        $validator = new IsSerializableValidator();
        $this->assertTrue($validator->validate('asdfghjkl'));
        $this->assertTrue($validator->validate(321321));
        $this->assertTrue($validator->validate([1,2,3,4]));
        $this->assertTrue($validator->validate(FALSE));
        $this->assertTrue($validator->validate(NULL));
        $this->assertFalse($validator->validate(new stdClass()));
        $this->assertTrue($validator->validate(new SerializableClassForTest()));
    }
}
