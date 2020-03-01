<?php
use PHPUnit\Framework\TestCase;

use Pes\Validator\IsArrayKeyValidator;


/**
 * Description of IndexedCollectionTest
 *
 * @author pes2704
 */
class IsArrayKeyValidatorTest extends TestCase {
    
    public function testIsValid() {
        // klíč pole může být integer nebo string
        $validator = new IsArrayKeyValidator();
        $this->assertTrue($validator->validate('asdfghjkl'));
        $this->assertTrue($validator->validate(321321));
        $this->assertTrue($validator->validate(''));
        $this->assertFalse($validator->validate([654]));
        $this->assertFalse($validator->validate(new stdClass()));
        $this->assertFalse($validator->validate(FALSE));
        $this->assertFALSE($validator->validate(NULL));
    }
}
