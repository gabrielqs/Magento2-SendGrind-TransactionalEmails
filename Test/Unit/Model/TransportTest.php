<?php

namespace Gabrielqs\TransactionalEmails\Test\Unit\Model;

use \Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use \Magento\Framework\ObjectManager\ObjectManager as RealObjectManager;
use \Magento\Framework\DataObject;
use \Gabrielqs\TransactionalEmails\Model\Transport as Subject;
use \Gabrielqs\TransactionalEmails\Helper\Data as Helper;
use \Gabrielqs\TransactionalEmails\Model\Email;
use \Gabrielqs\TransactionalEmails\Model\EmailFactory;
use \Gabrielqs\TransactionalEmails\Model\EmailRepository;
use \Gabrielqs\TransactionalEmails\Model\Message;
use \Gabrielqs\TransactionalEmails\Model\Transports\SendGrid;


/**
 * Unit Testcase
 */
class TransportTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SendGrid
     */
    protected $api;

    /**
     * @var String
     */
    protected $className = null;

    /**
     * @var Email
     */
    protected $email;

    /**
     * Transactional Emails Factory
     * @var EmailFactory
     */
    protected $emailFactory;

    /**
     * Transactional Emails Repository
     * @var EmailRepository
     */
    protected $emailRepository;

    /**
     * Transactional Emails Helper
     * @var Helper
     */
    protected $helper;

    /**
     * Message
     * @var Message
     */
    protected $message;

    /**
     * @var RealObjectManager
     */
    protected $objectManager = null;

    /**
     * @var DataObject
     */
    protected $objectManagerMock = null;

    /**
     * @var Subject
     */
    protected $originalSubject = null;

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
            ->setMethods(null)
            ->getMock();


        $this->email = $this->getMockBuilder(Email::class)
            ->disableOriginalConstructor()
            ->setMethods([
                'setBody',
                'setFrom',
                'setTo',
                'setSubject',
                'setBcc',
                'setOrderId',
                'findAndSetCustomerIdFromEmail',
                'setStatus',
                'setRequest',
                'setResponse',
            ])
            ->getMock();


        $this->api = $this->getMockBuilder(SendGrid::class)
            ->disableOriginalConstructor()
            ->setMethods(['send'])
            ->getMock();

        $this->originalSubject = $this
            ->objectManager
            ->getObject($this->className, $arguments);
    }

    protected function getConstructorArguments()
    {
        $arguments = $this->objectManager->getConstructArguments($this->className);

        $this->message = $this->getMockBuilder(Message::class)
            ->disableOriginalConstructor()
            ->setMethods([
                'getBody',
                'getFrom',
                'getToEmail',
                'getSubject',
                'getBccAsAString',
                'getOrderId',
                'getRequest',
                'getResponse',
            ])
            ->getMock();
        $arguments['message'] = $this->message;

        $this->helper = $this->getMockBuilder(Helper::class)
            ->disableOriginalConstructor()
            ->setMethods(['getProviderApiClassName'])
            ->getMock();
        $arguments['helper'] = $this->helper;

        $this->objectManagerMock = $this->getMockBuilder(RealObjectManager::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $arguments['objectManager'] = $this->objectManagerMock;

        $this->emailFactory = $this->getMockBuilder(EmailFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $arguments['emailFactory'] = $this->emailFactory;

        $this->emailRepository = $this->getMockBuilder(EmailRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['save'])
            ->getMock();
        $arguments['emailRepository'] = $this->emailRepository;

        return $arguments;
    }

    public function testSendMailSuccess()
    {
        $from = 'from@example.com';
        $to = 'to@example.com';
        $subject = 'Subject';
        $bcc = 'bcc1@example.com, bcc2@example.com';
        $orderId = 234234;
        $request = '{sendTo: "lala@lala.com"}';
        $response = '{success: true}';
        $bodyContent = 'Hello';

        $body = $this->getMockBuilder(DataObject::class)
            ->disableOriginalConstructor()
            ->setMethods(['getRawContent'])
            ->getMock();

        $body
            ->expects($this->once())
            ->method('getRawContent')
            ->willReturn($bodyContent);

        $this
            ->emailFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn($this->email);

        $this
            ->message
            ->expects($this->once())
            ->method('getBody')
            ->willReturn($body);

        $this
            ->message
            ->expects($this->once())
            ->method('getFrom')
            ->willReturn($from);

        $this
            ->message
            ->expects($this->once())
            ->method('getToEmail')
            ->willReturn($to);

        $this
            ->message
            ->expects($this->once())
            ->method('getSubject')
            ->willReturn($subject);

        $this
            ->message
            ->expects($this->once())
            ->method('getBccAsAString')
            ->willReturn($bcc);

        $this
            ->message
            ->expects($this->once())
            ->method('getRequest')
            ->willReturn($request);

        $this
            ->message
            ->expects($this->once())
            ->method('getResponse')
            ->willReturn($response);

        $this
            ->message
            ->expects($this->once())
            ->method('getOrderId')
            ->willReturn($orderId);

        $this
            ->email
            ->expects($this->once())
            ->method('setBody')
            ->with($bodyContent)
            ->willReturn($this->email);

        $this
            ->email
            ->expects($this->once())
            ->method('setFrom')
            ->with($from)
            ->willReturn($this->email);

        $this
            ->email
            ->expects($this->once())
            ->method('setTo')
            ->with($to)
            ->willReturn($this->email);

        $this
            ->email
            ->expects($this->once())
            ->method('setSubject')
            ->with($subject)
            ->willReturn($this->email);

        $this
            ->email
            ->expects($this->once())
            ->method('setBcc')
            ->with($bcc)
            ->willReturn($this->email);

        $this
            ->email
            ->expects($this->once())
            ->method('setOrderId')
            ->with($orderId)
            ->willReturn($this->email);

        $this
            ->email
            ->expects($this->once())
            ->method('findAndSetCustomerIdFromEmail')
            ->with()
            ->willReturn($this->email);

        $this
            ->helper
            ->expects($this->once())
            ->method('getProviderApiClassName')
            ->willReturn(SendGrid::class);

        $this
            ->objectManagerMock
            ->expects($this->once())
            ->method('create')
            ->with(SendGrid::class)
            ->willReturn($this->api);

        $this
            ->api
            ->expects($this->once())
            ->method('send')
            ->with($this->message)
            ->willReturn(true);

        $this
            ->email
            ->expects($this->once())
            ->method('setStatus')
            ->with(Email::STATUS_SUCCESS)
            ->willReturn($this->email);

        $this
            ->email
            ->expects($this->once())
            ->method('setRequest')
            ->with($request)
            ->willReturn($this->email);


        $this
            ->email
            ->expects($this->once())
            ->method('setResponse')
            ->with($response)
            ->willReturn($this->email);

        $this
            ->emailRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->email);

        $this->assertTrue($this->subject->_sendMail());
    }

    public function testSendMailException()
    {
        $from = 'from@example.com';
        $to = 'to@example.com';
        $subject = 'Subject';
        $bcc = 'bcc1@example.com, bcc2@example.com';
        $orderId = 234234;
        $request = '{sendTo: "lala@lala.com"}';
        $response = '{success: false}';
        $bodyContent = 'Hello';

        $body = $this->getMockBuilder(DataObject::class)
            ->disableOriginalConstructor()
            ->setMethods(['getRawContent'])
            ->getMock();

        $body
            ->expects($this->once())
            ->method('getRawContent')
            ->willReturn($bodyContent);

        $this
            ->emailFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn($this->email);

        $this
            ->message
            ->expects($this->once())
            ->method('getBody')
            ->willReturn($body);

        $this
            ->message
            ->expects($this->once())
            ->method('getFrom')
            ->willReturn($from);

        $this
            ->message
            ->expects($this->once())
            ->method('getToEmail')
            ->willReturn($to);

        $this
            ->message
            ->expects($this->once())
            ->method('getSubject')
            ->willReturn($subject);

        $this
            ->message
            ->expects($this->once())
            ->method('getBccAsAString')
            ->willReturn($bcc);

        $this
            ->message
            ->expects($this->once())
            ->method('getRequest')
            ->willReturn($request);

        $this
            ->message
            ->expects($this->once())
            ->method('getResponse')
            ->willReturn($response);

        $this
            ->message
            ->expects($this->once())
            ->method('getOrderId')
            ->willReturn($orderId);

        $this
            ->email
            ->expects($this->once())
            ->method('setBody')
            ->with($bodyContent)
            ->willReturn($this->email);

        $this
            ->email
            ->expects($this->once())
            ->method('setFrom')
            ->with($from)
            ->willReturn($this->email);

        $this
            ->email
            ->expects($this->once())
            ->method('setTo')
            ->with($to)
            ->willReturn($this->email);

        $this
            ->email
            ->expects($this->once())
            ->method('setSubject')
            ->with($subject)
            ->willReturn($this->email);

        $this
            ->email
            ->expects($this->once())
            ->method('setBcc')
            ->with($bcc)
            ->willReturn($this->email);

        $this
            ->email
            ->expects($this->once())
            ->method('setOrderId')
            ->with($orderId)
            ->willReturn($this->email);

        $this
            ->email
            ->expects($this->once())
            ->method('findAndSetCustomerIdFromEmail')
            ->with()
            ->willReturn($this->email);

        $this
            ->helper
            ->expects($this->once())
            ->method('getProviderApiClassName')
            ->willReturn(SendGrid::class);

        $this
            ->objectManagerMock
            ->expects($this->once())
            ->method('create')
            ->with(SendGrid::class)
            ->willReturn($this->api);

        $this
            ->api
            ->expects($this->once())
            ->method('send')
            ->with($this->message)
            ->willThrowException(new \Exception('Exception'));

        $this
            ->email
            ->expects($this->once())
            ->method('setStatus')
            ->with(Email::STATUS_FAILURE)
            ->willReturn($this->email);

        $this
            ->email
            ->expects($this->once())
            ->method('setRequest')
            ->with($request)
            ->willReturn($this->email);


        $this
            ->email
            ->expects($this->once())
            ->method('setResponse')
            ->with($response)
            ->willReturn($this->email);

        $this
            ->emailRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->email);

        $this->setExpectedException(\Exception::class);

        $this->subject->_sendMail();
    }
}