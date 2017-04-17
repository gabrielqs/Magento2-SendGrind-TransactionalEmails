<?php

namespace Gabrielqs\TransactionalEmails\Test\Unit\Model;

use \Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use \Magento\Sales\Model\Order;
use \Gabrielqs\TransactionalEmails\Model\Message;
use \Gabrielqs\TransactionalEmails\Model\OrderTransportBuilder as Subject;

/**
 * Unit Testcase
 */
class OrderTransportBuilderTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var String
     */
    protected $className = null;

    /**
     * @var Message
     */
    protected $message = null;

    /**
     * @var ObjectManager
     */
    protected $objectManager = null;

    /**
     * @var Subject
     */
    protected $originalSubject = null;

    /**
     * @var Order
     */
    protected $order = null;

    /**
     * @var \ReflectionMethod
     */
    protected $prepareMessageMethod = null;

    /**
     * @var Subject
     */
    protected $subject = null;

    protected function setUp()
    {
        $this->objectManager = new ObjectManager($this);
        $this->className = Subject::class;
        $arguments = $this->getConstructorArguments();

        $this->subject = $this
            ->getMockBuilder($this->className)
            ->setConstructorArgs($arguments)
            ->setMethods(['_callParentPrepareMessage'])
            ->getMock();

        $this->order = $this
            ->getMockBuilder(Order::class)
            ->disableOriginalConstructor()
            ->setMethods(['getId'])
            ->getMock();

        $this->originalSubject = $this
            ->objectManager
            ->getObject($this->className, $arguments);

        $reflection = new \ReflectionClass($this->objectManager->getObject($this->className));
        $this->prepareMessageMethod = $reflection->getMethod('prepareMessage');
        $this->prepareMessageMethod->setAccessible(true);

    }

    protected function getConstructorArguments()
    {
        $arguments = $this->objectManager->getConstructArguments($this->className);

        $this->message = $this
            ->getMockBuilder(Message::class)
            ->disableOriginalConstructor()
            ->setMethods(['setOrderId'])
            ->getMock();
        $arguments['message'] = $this->message;

        return $arguments;
    }

    public function testPrepareMessageSetsOrderIdWhenNeeded()
    {
        $orderId = 98938;

        $this
            ->subject
            ->setTemplateVars([
                'foo' => 'bar',
                'order' => $this->order,
            ]);

        $this
            ->order
            ->expects($this->once())
            ->method('getId')
            ->willReturn($orderId);

        $this
            ->message
            ->expects($this->once())
            ->method('setOrderId')
            ->with($orderId);

        $this->assertEquals($this->subject, $this->prepareMessageMethod->invoke($this->subject));
    }

}