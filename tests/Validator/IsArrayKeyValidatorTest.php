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
        $this->assertTrue($validator->isValid('asdfghjkl'));
        $this->assertTrue($validator->isValid(321321));
        $this->assertTrue($validator->isValid(''));
        $this->assertFalse($validator->isValid([654]));
        $this->assertFalse($validator->isValid(new stdClass()));
        $this->assertFalse($validator->isValid(FALSE));
        $this->assertFALSE($validator->isValid(NULL));
    }
}
