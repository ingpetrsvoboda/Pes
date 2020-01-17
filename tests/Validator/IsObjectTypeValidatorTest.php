<?php
use PHPUnit\Framework\TestCase;

use Pes\Collection\MapCollection;
use Pes\Validator\IsObjectTypeValidator;

interface InterfaceForIsTypeValidatorTest {
    
}
class ObjectForIsTypeValidatorTest {
    
}
class InterfacedObjectForIsTypeValidatorTest implements InterfaceForIsTypeValidatorTest {
    
}
class AnotherObjectForIsTypeValidatorTest {
    
}
/**
 * Description of IndexedCollectionTest
 *
 * @author pes2704
 */
class IsObjectTypeValidatorTest extends TestCase {
    public function testConstructor() {
        try {
            $validator = new IsObjectTypeValidator('Blabla');   // Vyhodí výjimku
        } catch (\InvalidArgumentException $uve) {
            $this->assertStringStartsWith('Nenalezen zadaný typ (interface nebo class)', $uve->getMessage());
        }
        try {
            $validator = new IsObjectTypeValidator(188);   // Vyhodí výjimku
        } catch (\InvalidArgumentException $uve) {
            $this->assertStringStartsWith('Jméno typu musí být zadáno jako string.', $uve->getMessage());
        }

        $validator = new IsObjectTypeValidator('InterfaceForIsTypeValidatorTest');      
        $validator = new IsObjectTypeValidator('ObjectForIsTypeValidatorTest');      
    }
    
    public function testIsValid() {
        $validator = new IsObjectTypeValidator('ObjectForIsTypeValidatorTest');      
        $this->assertTrue($validator->isValid(new ObjectForIsTypeValidatorTest()));
        $this->assertFalse($validator->isValid(new AnotherObjectForIsTypeValidatorTest()));
        $validator = new IsObjectTypeValidator('InterfaceForIsTypeValidatorTest');      
        $this->assertTrue($validator->isValid(new InterfacedObjectForIsTypeValidatorTest()));
        $this->assertFalse($validator->isValid(new AnotherObjectForIsTypeValidatorTest()));    }
    
    
}
