<?php

namespace Gabrielqs\TransactionalEmails\Test\Unit\Controller\Adminhtml\Order;

use Gabrielqs\TransactionalEmails\Controller\Adminhtml\RegistryConstants;
use \Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use \Magento\Framework\Registry;
use \Magento\Framework\View\Result\LayoutFactory;
use \Magento\Framework\View\Result\Layout;
use \Magento\Backend\App\Action\Context;
use \Magento\Framework\App\Request\Http as Request;
use \Gabrielqs\TransactionalEmails\Controller\Adminhtml\Order\Index as Subject;

/**
 * Unit Testcase
 */
class IndexTest extends \PHPUnit_Framework_TestCase
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
    protected $coreRegistry;

    /**
     * @var ObjectManager
     */
    protected $objectManager = null;

    /**
     * @var Subject
     */
    protected $originalSubject = null;

    /**
     * Request
     * @var Request
     */
    protected $request;

    /**
     * Result Layout
     * @var Layout
     */
    protected $resultLayout;

    /**
     * Result Layout Factory
     * @var LayoutFactory
     */
    protected $resultLayoutFactory;

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

        $this->originalSubject = $this
            ->objectManager
            ->getObject($this->className, $arguments);
    }

    protected function getConstructorArguments()
    {
        $arguments = $this->objectManager->getConstructArguments($this->className);

        $this->coreRegistry = $this->getMockBuilder(Registry::class)
            ->setMethods(['register'])
            ->disableOriginalConstructor()
            ->getMock();
        $arguments['registry'] = $this->coreRegistry;

        $this->resultLayoutFactory = $this->getMockBuilder(LayoutFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $arguments['resultLayoutFactory'] = $this->resultLayoutFactory;

        $this->resultLayout = $this->getMockBuilder(Layout::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();
        $this
            ->resultLayoutFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn($this->resultLayout);

        $this->request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->setMethods(['getParam'])
            ->getMock();
        $this->context = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->setMethods(['getRequest'])
            ->getMock();
        $this->context
            ->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue(($this->request)));
        $arguments['context'] = $this->context;

        return $arguments;
    }

    public function testExecute()
    {
        $id = 323;
        $this
            ->request
            ->expects($this->once())
            ->method('getParam')
            ->with('order_id', null)
            ->willReturn($id);
        $this
            ->coreRegistry
            ->expects($this->once())
            ->method('register')
            ->with(RegistryConstants::CURRENT_ORDER_ID, $id);
        $this
            ->resultLayoutFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn($this->resultLayout);
        $this->assertEquals($this->subject->execute(), $this->resultLayout);
    }

}