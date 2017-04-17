<?php

namespace Gabrielqs\TransactionalEmails\Controller\Adminhtml\Sent;

use \Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use \Magento\Backend\Model\View\Result\PageFactory;
use \Magento\Backend\Model\View\Result\Page;
use \Magento\Backend\App\Action\Context;
use \Magento\Framework\Message\Manager as MessageManager;
use \Magento\Framework\Registry;
use \Magento\Framework\Controller\Result\RedirectFactory;
use \Magento\Framework\Controller\Result\Redirect;
use \Gabrielqs\TransactionalEmails\Model\Email;
use \Gabrielqs\TransactionalEmails\Model\EmailRepository;
use \Gabrielqs\TransactionalEmails\Controller\Adminhtml\Sent\View as Subject;
use \Gabrielqs\TransactionalEmails\Controller\Adminhtml\RegistryConstants;

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
     * @var Context
     */
    protected $context = null;

    /**
     * @var Registry
     */
    protected $coreRegistry = null;

    /**
     * @var Email
     */
    protected $email = null;

    /**
     * @var EmailRepository
     */
    protected $emailRepository = null;

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
     * @var Page
     */
    protected $page = null;

    /**
     * @var PageFactory
     */
    protected $pageFactory = null;

    /**
     * @var Redirect
     */
    protected $redirect = null;

    /**
     * @var RedirectFactory
     */
    protected $redirectFactory = null;

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
            ->setMethods(['initPage', '_getEmailIdFromRequest', '_prependTitle'])
            ->getMock();

        $this->originalSubject = $this
            ->objectManager
            ->getObject($this->className, $arguments);

        $this->email = $this
            ->getMockBuilder(Email::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
    }

    protected function getConstructorArguments()
    {
        $arguments = $this->objectManager->getConstructArguments($this->className);

        $this->pageFactory = $this->getMockBuilder(PageFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $arguments['resultPageFactory'] = $this->pageFactory;

        $this->page = $this->getMockBuilder(Page::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();

        $this->redirectFactory = $this->getMockBuilder(RedirectFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $arguments['redirectRedirectFactory'] = $this->redirectFactory;

        $this->redirect = $this->getMockBuilder(Redirect::class)
            ->setMethods(['setPath'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->coreRegistry = $this->getMockBuilder(Registry::class)
            ->setMethods(['register'])
            ->disableOriginalConstructor()
            ->getMock();
        $arguments['coreRegistry'] = $this->coreRegistry;

        $this->emailRepository = $this->getMockBuilder(EmailRepository::class)
            ->setMethods(['getById'])
            ->disableOriginalConstructor()
            ->getMock();
        $arguments['emailRepository'] = $this->emailRepository;

        $this->messageManager = $this->getMockBuilder(MessageManager::class)
            ->disableOriginalConstructor()
            ->setMethods(['addErrorMessage'])
            ->getMock();

        $this->context = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->setMethods(['getMessageManager', 'getResultRedirectFactory'])
            ->getMock();

        $this->context
            ->expects($this->any())
            ->method('getMessageManager')
            ->will($this->returnValue(($this->messageManager)));

        $this->context
            ->expects($this->any())
            ->method('getResultRedirectFactory')
            ->will($this->returnValue(($this->redirectFactory)));

        $arguments['context'] = $this->context;

        return $arguments;
    }

    public function testExecuteSuccess()
    {
        $emailId = 24344;

        $this
            ->subject
            ->expects($this->once())
            ->method('_getEmailIdFromRequest')
            ->willReturn($emailId);

        $this
            ->emailRepository
            ->expects($this->once())
            ->method('getById')
            ->with($emailId)
            ->willReturn($this->email);

        $this
            ->coreRegistry
            ->expects($this->once())
            ->method('register')
            ->with(RegistryConstants::CURRENT_EMAIL_ID, $emailId);

        $this
            ->pageFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn($this->page);

        $this
            ->subject
            ->expects($this->once())
            ->method('initPage')
            ->with($this->page)
            ->willReturn($this->subject);

        $this
            ->subject
            ->expects($this->once())
            ->method('_prependTitle')
            ->with($this->page, __('E-mail'))
            ->willReturn($this->subject);

        $this->assertEquals($this->subject->execute(), $this->page);
    }

    public function testExecuteException()
    {
        $emailId = 24344;

        $this
            ->subject
            ->expects($this->once())
            ->method('_getEmailIdFromRequest')
            ->willReturn($emailId);

        $this
            ->emailRepository
            ->expects($this->once())
            ->method('getById')
            ->with($emailId)
            ->willThrowException(new \Exception('Foo Bar'));

        $this
            ->coreRegistry
            ->expects($this->never())
            ->method('register');

        $this
            ->pageFactory
            ->expects($this->never())
            ->method('create');

        $this
            ->subject
            ->expects($this->never())
            ->method('initPage')
            ->with($this->page);

        $this
            ->subject
            ->expects($this->never())
            ->method('_prependTitle')
            ->with($this->page, __('E-mail'));

        $this
            ->messageManager
            ->expects($this->once())
            ->method('addErrorMessage');

        $this
            ->redirectFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn($this->redirect);

        $this
            ->redirect
            ->expects($this->once())
            ->method('setPath')
            ->with('transactionalemails/returns/index')
            ->willReturn($this->redirect);

        $this->assertEquals($this->subject->execute(), $this->redirect);
    }

}