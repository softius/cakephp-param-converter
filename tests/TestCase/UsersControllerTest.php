<?php

namespace ParamConverter\Test\TestCase;

use Cake\Controller\Exception\MissingActionException;
use Cake\Core\Configure;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\I18n\FrozenDate;
use Cake\I18n\FrozenTime;
use Cake\I18n\Time;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use DateTime;
use ParamConverter\Test\App\Controller\UsersController;
use ParamConverter\Test\App\Model\Table\UsersTable;

class UsersControllerTest extends TestCase
{
    /**
     * @var UsersController
     */
    protected $Controller;

    public $fixtures = [
        'plugin.ParamConverter.Users',
    ];

    public function setUp(): void
    {
        parent::setUp();

        $this->Controller = new UsersController(new ServerRequest());
        $this->Controller->startupProcess();

        TableRegistry::getTableLocator()->set(
            'Users',
            TableRegistry::getTableLocator()->get('Users', ['className' => UsersTable::class])
        );
    }

    /**
     * @throws \ReflectionException
     */
    public function testScalar(): void
    {
        $event = new Event('Controller.startup', $this->Controller);

        $this->Controller->setRequest($this->Controller->getRequest()
            ->withParam('pass', ["false", "10", "10.5", "foo"])
            ->withParam('action', 'withScalar'));

        $action = $this->Controller->getAction();
        $this->Controller->invokeAction($action, $this->Controller->getRequest()->getParam('pass'));
        $return = Configure::read('Tests.result');

        $this->assertFalse($return['bool']);
        $this->assertSame(10, $return['int']);
        $this->assertSame(10.5, $return['float']);
        $this->assertSame('foo', $return['string']);
    }

    /**
     * @throws \ReflectionException
     */
    public function testDatetime(): void
    {
        $event = new Event('Controller.startup', $this->Controller);

        $this->Controller->setRequest($this->Controller->getRequest()
            ->withParam('pass', ["2020-10-10"])
            ->withParam('action', 'withDatetime'));

        $action = $this->Controller->getAction();
        $this->Controller->invokeAction($action, $this->Controller->getRequest()->getParam('pass'));
        $return = Configure::read('Tests.result');

        $this->assertTrue($return['dateTime'] instanceof Datetime);
    }

    /**
     * @throws \ReflectionException
     */
    public function testTime(): void
    {
        $event = new Event('Controller.startup', $this->Controller);

        $this->Controller->setRequest($this->Controller->getRequest()
            ->withParam('pass', ["2020-10-10"])
            ->withParam('action', 'withTime'));

        $action = $this->Controller->getAction();
        $this->Controller->invokeAction($action, $this->Controller->getRequest()->getParam('pass'));
        $return = Configure::read('Tests.result');

        $this->assertInstanceOf(Time::class, $return['time']);
    }

    /**
     * @throws \ReflectionException
     */
    public function testFrozenDate(): void
    {
        $event = new Event('Controller.startup', $this->Controller);

        $this->Controller->setRequest($this->Controller->getRequest()
            ->withParam('pass', ["2020-10-10"])
            ->withParam('action', 'withFrozenDate'));

        $action = $this->Controller->getAction();
        $this->Controller->invokeAction($action, $this->Controller->getRequest()->getParam('pass'));
        $return = Configure::read('Tests.result');

        $this->assertInstanceOf(FrozenDate::class, $return['date']);
    }

    /**
     * @throws \ReflectionException
     */
    public function testFrozenTime(): void
    {
        $event = new Event('Controller.startup', $this->Controller);

        $this->Controller->setRequest($this->Controller->getRequest()
            ->withParam('pass', ["2020-10-10"])
            ->withParam('action', 'withFrozenTime'));

        $action = $this->Controller->getAction();
        $this->Controller->invokeAction($action, $this->Controller->getRequest()->getParam('pass'));
        $return = Configure::read('Tests.result');

        $this->assertTrue($return['frozenTime'] instanceof FrozenTime);
    }

    /**
     * @throws \ReflectionException
     */
    public function testEntity(): void
    {
        $event = new Event('Controller.startup', $this->Controller);

        $this->Controller->setRequest($this->Controller->getRequest()
            ->withParam('pass', ["00000000-0000-0000-0000-000000000001"])
            ->withParam('action', 'withEntity'));

        $action = $this->Controller->getAction();
        $this->Controller->invokeAction($action, $this->Controller->getRequest()->getParam('pass'));
        $return = Configure::read('Tests.result');

        $this->assertInstanceOf(EntityInterface::class, $return['entity']);
    }

    /**
     * @throws \ReflectionException
     */
    public function testActionNoParams(): void
    {
        $event = new Event('Controller.startup', $this->Controller);

        $this->Controller->setRequest($this->Controller->getRequest()
            ->withParam('pass', [])
            ->withParam('action', 'withNoParams'));

        $action = $this->Controller->getAction();
        $this->Controller->invokeAction($action, $this->Controller->getRequest()->getParam('pass'));
        $return = Configure::read('Tests.result');

        $this->assertEquals([], $return);
    }

    /**
     * @throws \ReflectionException
     */
    public function testActionNoTypehint(): void
    {
        $event = new Event('Controller.startup', $this->Controller);

        $this->Controller->setRequest($this->Controller->getRequest()
            ->withParam('pass', ["1", "2", "3"])
            ->withParam('action', 'withNoTypehint'));

        $action = $this->Controller->getAction();
        $this->Controller->invokeAction($action, $this->Controller->getRequest()->getParam('pass'));
        $return = Configure::read('Tests.result');

        $this->assertEquals(['a' => '1', 'b' => '2', 'c' => '3'], $return);
    }

    /**
     * @throws \ReflectionException
     */
    public function testUndefinedAction(): void
    {
        $event = new Event('Controller.startup', $this->Controller);

        $this->Controller->setRequest($this->Controller->getRequest()
            ->withParam('pass', [])
            ->withParam('action', 'undefined'));

        $this->expectException(MissingActionException::class);
        $action = $this->Controller->getAction();
        $this->Controller->invokeAction($action, $this->Controller->getRequest()->getParam('pass'));
    }

    /**
     * @throws \ReflectionException
     */
    public function testOptional(): void
    {
        $event = new Event('Controller.startup', $this->Controller);

        $this->Controller->setRequest($this->Controller->getRequest()
            ->withParam('pass', [])
            ->withParam('action', 'withOptional'));

        $action = $this->Controller->getAction();
        $this->Controller->invokeAction($action, $this->Controller->getRequest()->getParam('pass'));
        $return = Configure::read('Tests.result');

        $this->assertEquals(['a' => 0], $return);

        $this->Controller->setRequest($this->Controller->getRequest()
            ->withParam('pass', [4])
            ->withParam('action', 'withOptional'));

        $action = $this->Controller->getAction();
        $this->Controller->invokeAction($action, $this->Controller->getRequest()->getParam('pass'));
        $return = Configure::read('Tests.result');

        $this->assertEquals(['a' => 4], $return);
    }
}
