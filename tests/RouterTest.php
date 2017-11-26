<?php
use PHPUnit\Framework\TestCase;
require 'vendor/autoload.php';
require_once 'system/routes/Router.php';

/**
 * @covers Router
 */
final class RouterTest extends TestCase
{
    public $router;
    public $response;
    const CONTROLLER = 'avatar';
    const METHOD = 'get';
    const PARAMS = ['1'];
    public function setUp()
    {
        $_GET['url'] = 'avatar/get/1';
        $this->router = new Router();
    }

    public function testController()
    {
        $property = $this->getPrivateProperty( $this->router, 'controller' );

        $this->assertInstanceOf(
            Avatar\Controller::class,
            $property
        );
    }
    public function testMethod()
    {
        $property = $this->getPrivateProperty( $this->router, 'method' );

        $this->assertEquals($property, self::METHOD);
    }
    public function testParams()
    {
        $property = $this->getPrivateProperty( $this->router, 'params' );

        $this->assertEquals($property, self::PARAMS);
    }
    public function testResponse()
    {
        $expectedValue = json_encode(['id' => 1, 'name' => "Nacho", 'surname' => "Benítez"]);

        $mockRouter = Mockery::mock($this->router);
        $mockRouter->shouldReceive('doRequest')->with(self::CONTROLLER, self::METHOD)->andReturn($expectedValue);

        $this->assertEquals($mockRouter->doRequest(self::CONTROLLER, self::METHOD), $expectedValue);
    }
    public function testRequestNotFound()
    {
        $expectedValue = json_encode(['status' => 'error', 'code' => 404, 'message' => 'Not found']);

        $mockRouter = Mockery::mock($this->router);
        $mockRouter->shouldReceive('sendError')->andReturn($expectedValue);

        $this->assertEquals($mockRouter->sendError(), $expectedValue);
    }

    private function getPrivateProperty( $className, $propertyName ) {
		$reflection = new \ReflectionClass( $className );
		$property = $reflection->getProperty( $propertyName );
		$property->setAccessible( true );

		return $property->getValue($className);
	}
}
