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
        $this->assertTrue($validator->isValid('asdfghjkl'));
        $this->assertTrue($validator->isValid(321321));
        $this->assertTrue($validator->isValid([1,2,3,4]));
        $this->assertTrue($validator->isValid(FALSE));
        $this->assertTrue($validator->isValid(NULL));
        $this->assertFalse($validator->isValid(new stdClass()));
        $this->assertTrue($validator->isValid(new SerializableClassForTest()));
    }
}
