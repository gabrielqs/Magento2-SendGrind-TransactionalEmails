<?php

namespace Gabrielqs\TransactionalEmails\Test\Unit\Block\Adminhtml\Sent;

use \Magento\Customer\Model\Customer;
use \Magento\Framework\Registry;
use \Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use \Magento\Sales\Model\Order;
use \Gabrielqs\TransactionalEmails\Block\Adminhtml\Sent\View as Subject;
use \Gabrielqs\TransactionalEmails\Model\Email;
use \Gabrielqs\TransactionalEmails\Model\EmailRepository;
use \Gabrielqs\TransactionalEmails\Controller\Adminhtml\RegistryConstants;
use \Gabrielqs\TransactionalEmails\Helper\Data as Helper;

/**
 * Unit Testcase
 */
class ViewTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var String
     */
    protected $className = null;

    /**
     * @var Email
     */
    protected $email = null;

    /**
     * @var EmailRepository
     */
    protected $emailRepository = null;

    /**
     * @var Helper
     */
    protected $helper = null;

    /**
     * @var ObjectManager
     */
    protected $objectManager = null;

    /**
     * @var Subject
     */
    protected $originalSubject = null;

    /**
     * @var Registry
     */
    protected $registry = null;

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
            ->setMethods(['getUrl'])
            ->getMock();

        $this->email = $this
            ->getMockBuilder(Email::class)
            ->disableOriginalConstructor()
            ->setMethods([
                'getBcc',
                'getBody',
                'getCustomerId',
                'getCustomer',
                'getFrom',
                'getSubject',
                'getOrderId',
                'getOrder',
                'getTo'
            ])
            ->getMock();

        $this
            ->registry
            ->expects($this->once())
            ->method('registry')
            ->with(RegistryConstants::CURRENT_EMAIL_ID)
            ->willReturn(23);

        $this
            ->emailRepository
            ->expects($this->once())
            ->method('getById')
            ->with(23)
            ->willReturn($this->email);

        $this->originalSubject = $this
            ->objectManager
            ->getObject($this->className, $arguments);
    }

    protected function getConstructorArguments()
    {
        $arguments = $this->objectManager->getConstructArguments($this->className);

        $this->emailRepository = $this->getMockBuilder(EmailRepository::class)
            ->setMethods(['getById'])
            ->disableOriginalConstructor()
            ->getMock();
        $arguments['emailRepository'] = $this->emailRepository;

        $this->registry = $this->getMockBuilder(Registry::class)
            ->setMethods(['registry'])
            ->disableOriginalConstructor()
            ->getMock();
        $arguments['registry'] = $this->registry;

        $this->registry = $this->getMockBuilder(Registry::class)
            ->setMethods(['registry'])
            ->disableOriginalConstructor()
            ->getMock();
        $arguments['registry'] = $this->registry;

        $this->helper = $this->getMockBuilder(Helper::class)
            ->setMethods(['getStatusLabel'])
            ->disableOriginalConstructor()
            ->getMock();
        $arguments['helper'] = $this->helper;

        return $arguments;
    }

    public function testGetBcc()
    {
        $email = 'joe@example.com';
        $this
            ->email
            ->expects($this->once())
            ->method('getBcc')
            ->willReturn($email);

        $this->assertEquals($this->subject->getBcc(), $email);
    }

    public function dataProviderTestGetBody()
    {
        return [
            ['<html><head><title>foo</title></head><body><p>bar</p></body></html>', '<p>bar</p>'],
            ['<body><p>bar</p></body>', '<p>bar</p>'],
            ['<BODY><p>bar</p></BODY>', '<p>bar</p>'],
            ['foo bar', 'foo bar'],
        ];
    }

    /**
     * @dataProvider dataProviderTestGetBody
     */
    public function testGetBody($body, $expectedReturn)
    {
        $this
            ->email
            ->expects($this->once())
            ->method('getBody')
            ->willReturn($body);
        $this->assertEquals($this->subject->getBody(), $expectedReturn);
    }

    public function testGetCustomerId()
    {
        $id = 39;
        $this
            ->email
            ->expects($this->once())
            ->method('getCustomerId')
            ->willReturn($id);

        $this->assertEquals($this->subject->getCustomerId(), $id);
    }

    public function testGetCustomerName()
    {
        $customer = $this
            ->getMockBuilder(Customer::class)
            ->disableOriginalConstructor()
            ->setMethods(['getFirstName', 'getLastName'])
            ->getMock();
        $this
            ->email
            ->expects($this->exactly(2))
            ->method('getCustomer')
            ->willReturn($customer);

        $customer
            ->expects($this->once())
            ->method('getFirstName')
            ->willReturn('Foo');

        $customer
            ->expects($this->once())
            ->method('getLastName')
            ->willReturn('Bar');

        $this->assertEquals($this->subject->getCustomerName(), 'Foo Bar');
    }

    public function testGetFrom()
    {
        $from = 'Foo Bar <foobar@gmail.com>';
        $this
            ->email
            ->expects($this->once())
            ->method('getFrom')
            ->willReturn($from);

        $this->assertEquals($this->subject->getFrom(), $from);
    }

    public function testGetSubject()
    {
        $subject = 'New Order #20000023 from Your Store';
        $this
            ->email
            ->expects($this->once())
            ->method('getSubject')
            ->willReturn($subject);

        $this->assertEquals($this->subject->getSubject(), $subject);
    }

    public function testGetOrderId()
    {
        $orderId = 2932;
        $this
            ->email
            ->expects($this->once())
            ->method('getOrderId')
            ->willReturn($orderId);

        $this->assertEquals($this->subject->getOrderId(), $orderId);
    }

    public function testGetOrderIncrementId()
    {
        $orderIncrementId = '00000223';

        $order = $this->getMockBuilder(Order::class)
            ->disableOriginalConstructor()
            ->setMethods(['getIncrementId'])
            ->getMock();
        $order
            ->expects($this->once())
            ->method('getIncrementId')
            ->willReturn($orderIncrementId);

        $this
            ->email
            ->expects($this->once())
            ->method('getOrder')
            ->willReturn($order);

        $this->assertEquals($this->subject->getOrderIncrementId(), $orderIncrementId);
    }

    public function testGetTo()
    {
        $email = 'joe@example.com';
        $this
            ->email
            ->expects($this->once())
            ->method('getTo')
            ->willReturn($email);

        $this->assertEquals($this->subject->getTo(), $email);
    }

    public function testGetOrderUrl()
    {
        $url = 'http://google.com/';
        $orderId = 39382;
        $this
            ->email
            ->expects($this->once())
            ->method('getOrderId')
            ->willReturn($orderId);

        $this
            ->subject
            ->expects($this->once())
            ->method('getUrl')
            ->with('sales/order/view', ['order_id' => $orderId])
            ->willReturn($url);

        $this->assertEquals($this->subject->getOrderUrl(), $url);
    }

    public function testGetStatus()
    {
        $status = 'Success';
        $this
            ->helper
            ->expects($this->once())
            ->method('getStatusLabel')
            ->with($this->email)
            ->willReturn($status);

        $this->assertEquals($this->subject->getStatus(), $status);
    }
}