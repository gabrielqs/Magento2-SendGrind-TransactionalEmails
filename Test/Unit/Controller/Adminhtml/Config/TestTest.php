<?php

namespace Gabrielqs\TransactionalEmails\Test\Unit\Controller\Adminhtml\Config;

use \Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use \Magento\Backend\App\Action\Context;
use \Magento\Framework\Message\Manager as MessageManager;
use \Magento\Framework\Controller\ResultFactory;
use \Magento\Framework\Controller\Result\Redirect;
use \Magento\Framework\Mail\Template\TransportBuilder;
use \Gabrielqs\TransactionalEmails\Controller\Adminhtml\Config\Test as Subject;
use \Magento\Framework\App\Area;
use \Gabrielqs\TransactionalEmails\Helper\Data as Helper;
use \Gabrielqs\TransactionalEmails\Model\Transport;


/**
 * Unit Testcase
 */
class TestTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var String
     */
    protected $className = null;

    /**
     * @var Context
     */
    protected $context = null;

    /**
     * @var Helper
     */
    protected $helper = null;

    /**
     * @var MessageManager
     */
    protected $messageManager = null;

    /**
     * @var ObjectManager
     */
    protected $objectManager = null;

    /**
     * @var Subject
     */
    protected $originalSubject = null;

    /**
     * @var ResultFactory
     */
    protected $resultFactory = null;

    /**
     * @var Redirect
     */
    protected $resultRedirect = null;

    /**
     * @var Subject
     */
    protected $subject = null;

    /**
     * @var TransportBuilder
     */
    protected $transportBuilder = null;

    /**
     * @var Transport
     */
    protected $transport = null;

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

        $this->originalSubject = $this
            ->objectManager
            ->getObject($this->className, $arguments);
    }

    protected function getConstructorArguments()
    {
        $arguments = $this->objectManager->getConstructArguments($this->className);

        $this->messageManager = $this->getMockBuilder(MessageManager::class)
            ->disableOriginalConstructor()
            ->setMethods(['addSuccessMessage', 'addExceptionMessage'])
            ->getMock();
        $this->resultRedirect = $this->getMockBuilder(Redirect::class)
            ->setMethods(['setRefererUrl'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->resultFactory = $this->getMockBuilder(ResultFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->resultFactory
            ->expects($this->any())
            ->method('create')
            ->with(ResultFactory::TYPE_REDIRECT)
            ->willReturn($this->resultRedirect);
        $this->context = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->setMethods(['getMessageManager', 'getResultFactory'])
            ->getMock();
        $this->context
            ->expects($this->any())
            ->method('getMessageManager')
            ->will($this->returnValue(($this->messageManager)));
        $this->context
            ->expects($this->any())
            ->method('getResultFactory')
            ->willReturn($this->resultFactory);
        $arguments['context'] = $this->context;

        $this->helper = $this->getMockBuilder(Helper::class)
            ->setMethods(['getTestSender', 'getTestEmailRecipient'])
            ->disableOriginalConstructor()
            ->getMock();
        $arguments['helper'] = $this->helper;

        $this->transport = $this->getMockBuilder(Transport::class)
            ->setMethods(['sendMessage'])
            ->disableOriginalConstructor()
            ->getMock();
        $arguments['transport'] = $this->transport;

        $this->transportBuilder = $this->getMockBuilder(TransportBuilder::class)
            ->setMethods([
                'setTemplateIdentifier',
                'setFrom',
                'addTo',
                'setTemplateVars',
                'setTemplateOptions',
                'getTransport',
            ])
            ->disableOriginalConstructor()
            ->getMock();

        $this->transportBuilder
            ->expects($this->any())
            ->method('getTransport')
            ->will($this->returnValue(($this->transport)));
        $arguments['transportBuilder'] = $this->transportBuilder;

        return $arguments;
    }

    public function testExecuteSuccess()
    {
        $sender = 'John Doe <john@example.com>';
        $recipient = 'foo@bar.com';
        $this
            ->helper
            ->expects($this->once())
            ->method('getTestSender')
            ->willReturn($sender);
        $this
            ->helper
            ->expects($this->once())
            ->method('getTestEmailRecipient')
            ->willReturn($recipient);

        $this
            ->transportBuilder
            ->expects($this->once())
            ->method('setTemplateIdentifier')
            ->with(Subject::EMAIL_TEMPLATE_TEST_ID);
        $this
            ->transportBuilder
            ->expects($this->once())
            ->method('setFrom')
            ->with($sender);
        $this
            ->transportBuilder
            ->expects($this->once())
            ->method('addTo')
            ->with($recipient);
        $this
            ->transportBuilder
            ->expects($this->once())
            ->method('setTemplateVars')
            ->with([]);
        $this
            ->transportBuilder
            ->expects($this->once())
            ->method('setTemplateOptions')
            ->with([
                'area'  => Area::AREA_ADMINHTML,
                'store' => 1
            ]);

        $this
            ->transport
            ->expects($this->once())
            ->method('sendMessage');

        $this
            ->messageManager
            ->expects($this->once())
            ->method('addSuccessMessage');

        $this->assertEquals($this->subject->execute(), $this->resultRedirect);
    }

    public function testExecuteFailure()
    {
        $sender = 'John Doe <john@example.com>';
        $recipient = 'foo@bar.com';
        $this
            ->helper
            ->expects($this->once())
            ->method('getTestSender')
            ->willReturn($sender);
        $this
            ->helper
            ->expects($this->once())
            ->method('getTestEmailRecipient')
            ->willReturn($recipient);

        $this
            ->transportBuilder
            ->expects($this->once())
            ->method('setTemplateIdentifier')
            ->with(Subject::EMAIL_TEMPLATE_TEST_ID);
        $this
            ->transportBuilder
            ->expects($this->once())
            ->method('setFrom')
            ->with($sender);
        $this
            ->transportBuilder
            ->expects($this->once())
            ->method('addTo')
            ->with($recipient);
        $this
            ->transportBuilder
            ->expects($this->once())
            ->method('setTemplateVars')
            ->with([]);
        $this
            ->transportBuilder
            ->expects($this->once())
            ->method('setTemplateOptions')
            ->with([
                'area'  => Area::AREA_ADMINHTML,
                'store' => 1
            ]);

        $this
            ->transport
            ->expects($this->once())
            ->method('sendMessage')
            ->willThrowException(new \Exception());

        $this
            ->messageManager
            ->expects($this->once())
            ->method('addExceptionMessage');

        $this->assertEquals($this->subject->execute(), $this->resultRedirect);
    }
}