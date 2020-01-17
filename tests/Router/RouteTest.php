<?php
use PHPUnit\Framework\TestCase;

use Pes\Router\Route;
use Pes\Router\RouteInterface;
use Pes\Router\MethodEnum;

/**
 * Test Pes\Type\DbTypeEnum
 *
 * @author pes2704
 */
class RouteTest extends TestCase {

    /**
     *
     */
    public function testConstructor() {
        $route = new Route();
        $this->assertTrue($route instanceof RouteInterface);
        $this->assertTrue($route instanceof Route);
    }

    /**
     *
     */
    public function testSetMethodGetMethod() {
        $route = new Route();
        $route->setMethod(MethodEnum::GET);
        $this->assertEquals(MethodEnum::GET, $route->getMethod());
        $route->setMethod(MethodEnum::POST);
        $this->assertEquals(MethodEnum::POST, $route->getMethod());
        $route->setMethod(MethodEnum::PUT);
        $this->assertEquals(MethodEnum::PUT, $route->getMethod());
        $route->setMethod(MethodEnum::DELETE);
        $this->assertEquals(MethodEnum::DELETE, $route->getMethod());
        $route->setMethod(MethodEnum::OPTIONS);
        $this->assertEquals(MethodEnum::OPTIONS, $route->getMethod());
        $route->setMethod(MethodEnum::PATCH);
        $this->assertEquals(MethodEnum::PATCH, $route->getMethod());

        try {
            $route->setMethod('PIST');
        } catch (Pes\Type\Exception\ValueNotInEnumException $vnieException) {
            $this->assertStringStartsWith('Value is not in enum', $vnieException->getMessage());
        }
    }

        /**
     *
     */
    public function testSetUrlPattern() {
        $route = new Route();

        $route->setUrlPattern('/');
        $route->setUrlPattern('/kuk/');
        $route->setUrlPattern('/kuk/:id/');
        $this->assertTrue(true);   // vždy splněno - testuji jen, že nenastala výjimka
    }

    public function testExceptionEmptyPattern() {
        $route = new Route();
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('Chybný formát pattern.');  // testuje, message obsahuje řetězec
        $route->setUrlPattern('');
    }

    public function testExceptionMissingLeftSlash() {
        $route = new Route();
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('Chybný formát pattern.');  // testuje, message obsahuje řetězec
        $route->setUrlPattern('kuk/');
    }

//    public function testExceptionMissingRightSlash() {
//        $route = new Route();
//        $this->expectException(\UnexpectedValueException::class);
//        $this->expectExceptionMessage('Chybný formát pattern.');  // testuje, message obsahuje řetězec
//        $route->setUrlPattern('/kuk');
//    }

    public function testExceptionParemeterInFirstSection() {
        $route = new Route();
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('Chybný formát pattern.');  // testuje, message obsahuje řetězec
        $route->setUrlPattern('/:id/');
    }

    public function testSetGetUrlPattern() {
        $route = new Route();
        $route->setUrlPattern('/trdlo/');
        $this->assertEquals('/trdlo/', $route->getUrlPattern());
    }

    public function testGetPatternPreg() {
        $route = new Route();
        $route->setUrlPattern('/');
        $patternPreg = $route->getPatternPreg();
        $this->assertEquals("@^/$@D", $route->getPatternPreg());
        $route->setUrlPattern('/trdlo/');
        $patternPreg = $route->getPatternPreg();
        $this->assertEquals("@^/trdlo/$@D", $route->getPatternPreg());
        $route->setUrlPattern('/trdlo/:id/');
        $patternPreg = $route->getPatternPreg();
        $this->assertEquals("@^/trdlo/([a-zA-Z0-9\-\_]+)/$@D", $route->getPatternPreg());
    }

    public function testSetGetAction() {
        $route = new Route();
        $action = function() {
            return 'Test action!';
        };
        $route->setAction($action);
        $this->assertEquals($action, $route->getAction());
    }

    public function testGetPathFor() {
        $route = new Route();
        $route->setUrlPattern('/trdlo/:id/ruka/:lp/');

        $path = $route->getPathFor(['lp'=>'levá', 'id'=>88]);
        $this->assertEquals("/trdlo/88/ruka/lev%C3%A1/", $path);
        $decodedPath = rawurldecode($path);
        $this->assertEquals("/trdlo/88/ruka/levá/", $decodedPath);  // enkóduje rezervované znaky v path
        $path = $route->getPathFor(['lp'=>'lev%C3%A1', 'id'=>88]);
        $this->assertEquals("/trdlo/88/ruka/lev%C3%A1/", $path);  // neenkóduje již enkódované rezervované znaky v path - po dekódování by vznikl nesmysl
        $decodedPath = rawurldecode($path);
        $this->assertEquals("/trdlo/88/ruka/levá/", $decodedPath);
    }

}
