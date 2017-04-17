<?php

namespace Gabrielqs\TransactionalEmails\Test\Unit\Model;

use \Magento\Customer\Api\Data\CustomerInterface;
use \Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use \Magento\Framework\Api\SearchCriteriaBuilder;
use \Magento\Framework\Api\SearchCriteria;
use \Magento\Sales\Model\Order as SalesOrder;
use \Magento\Sales\Model\OrderRepository as SalesOrderRepository;
use \Magento\Customer\Model\Customer;
use \Magento\Customer\Model\ResourceModel\CustomerRepository;
use \Magento\Framework\DataObject;
use \Gabrielqs\TransactionalEmails\Model\Email as Subject;

/**
 * Unit Testcase
 */
class EmailTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var String
     */
    protected $className = null;

    /**
     * @var Customer
     */
    protected $customer = null;

    /**
     * @var CustomerRepository
     */
    protected $customerRepository = null;

    /**
     * @var ObjectManager
     */
    protected $objectManager = null;

    /**
     * @var Subject
     */
    protected $originalSubject = null;

    /**
     * @var SalesOrder
     */
    protected $salesOrder = null;

    /**
     * @var SalesOrderRepository
     */
    protected $salesOrderRepository = null;

    /**
     * @var SearchCriteria
     */
    protected $searchCriteria = null;

    /**
     * Search Criteria Builder
     * @var SearchCriteriaBuilder|null
     */
    protected $searchCriteriaBuilder = null;

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
            ->setMethods(['getData', 'setData'])
            ->getMock();

        $this->originalSubject = $this
            ->objectManager
            ->getObject($this->className, $arguments);



        $this->customer = $this->getMockBuilder(Customer::class)
            ->disableOriginalConstructor()
            ->setMethods(['getId'])
            ->getMock();

        $this->salesOrder = $this->getMockBuilder(SalesOrder::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $this->searchCriteria = $this->getMockBuilder(SearchCriteria::class)
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();
    }

    protected function getConstructorArguments()
    {
        $arguments = $this->objectManager->getConstructArguments($this->className);

        $this->salesOrderRepository = $this->getMockBuilder(SalesOrderRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['get', 'getList'])
            ->getMock();
        $arguments['salesOrderRepository'] = $this->salesOrderRepository;

        $this->customerRepository = $this->getMockBuilder(CustomerRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['getById', 'getList'])
            ->getMock();
        $arguments['customerRepository'] = $this->customerRepository;

        $this->searchCriteriaBuilder = $this->getMockBuilder(SearchCriteriaBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(['addFilter', 'create'])
            ->getMock();
        $arguments['searchCriteriaBuilder'] = $this->searchCriteriaBuilder;

        return $arguments;
    }



    public function testGetIdGetsTheRightKey()
    {
        $this
            ->subject
            ->expects($this->once())
            ->method('getData')
            ->with(Subject::EMAIL_ID)
            ->willReturn(234);
        $this->assertEquals(234, $this->subject->getId());
    }

    public function testSetIdSetsTheRightKey()
    {
        $this
            ->subject
            ->expects($this->once())
            ->method('setData')
            ->with(Subject::EMAIL_ID, 'foobar');
        $this->subject->setId('foobar');
    }

    public function testGetFromGetsTheRightKey()
    {
        $this
            ->subject
            ->expects($this->once())
            ->method('getData')
            ->with(Subject::FROM)
            ->willReturn('john@example.com');
        $this->assertEquals('john@example.com', $this->subject->getFrom());
    }

    public function testSetFromSetsTheRightKey()
    {
        $this
            ->subject
            ->expects($this->once())
            ->method('setData')
            ->with(Subject::FROM, 'foobar');
        $this->subject->setFrom('foobar');
    }

    public function testGetToGetsTheRightKey()
    {
        $this
            ->subject
            ->expects($this->once())
            ->method('getData')
            ->with(Subject::TO)
            ->willReturn('john@example.com');
        $this->assertEquals('john@example.com', $this->subject->getTo());
    }

    public function testSetToSetsTheRightKey()
    {
        $this
            ->subject
            ->expects($this->once())
            ->method('setData')
            ->with(Subject::TO, 'foobar');
        $this->subject->setTo('foobar');
    }

    public function testGetBccGetsTheRightKey()
    {
        $this
            ->subject
            ->expects($this->once())
            ->method('getData')
            ->with(Subject::BCC)
            ->willReturn('john@example.com');
        $this->assertEquals('john@example.com', $this->subject->getBcc());
    }

    public function testSetBccSetsTheRightKey()
    {
        $this
            ->subject
            ->expects($this->once())
            ->method('setData')
            ->with(Subject::BCC, 'foobar');
        $this->subject->setBcc('foobar');
    }

    public function testGetStatusGetsTheRightKey()
    {
        $this
            ->subject
            ->expects($this->once())
            ->method('getData')
            ->with(Subject::STATUS)
            ->willReturn(Subject::STATUS_SUCCESS);
        $this->assertEquals(Subject::STATUS_SUCCESS, $this->subject->getStatus());
    }

    public function testSetStatusSetsTheRightKey()
    {
        $this
            ->subject
            ->expects($this->once())
            ->method('setData')
            ->with(Subject::STATUS, 'foobar');
        $this->subject->setStatus('foobar');
    }

    public function testGetSubjectGetsTheRightKey()
    {
        $this
            ->subject
            ->expects($this->once())
            ->method('getData')
            ->with(Subject::SUBJECT)
            ->willReturn('Foo Baz');
        $this->assertEquals('Foo Baz', $this->subject->getSubject());
    }

    public function testSetSubjectSetsTheRightKey()
    {
        $this
            ->subject
            ->expects($this->once())
            ->method('setData')
            ->with(Subject::SUBJECT, 'foobar');
        $this->subject->setSubject('foobar');
    }

    public function testGetBodyGetsTheRightKey()
    {
        $this
            ->subject
            ->expects($this->once())
            ->method('getData')
            ->with(Subject::BODY)
            ->willReturn('Hello!');
        $this->assertEquals('Hello!', $this->subject->getBody());
    }

    public function testSetBodySetsTheRightKey()
    {
        $this
            ->subject
            ->expects($this->once())
            ->method('setData')
            ->with(Subject::BODY, 'foobar');
        $this->subject->setBody('foobar');
    }

    public function testGetRequestGetsTheRightKey()
    {
        $this
            ->subject
            ->expects($this->once())
            ->method('getData')
            ->with(Subject::REQUEST)
            ->willReturn('{sendTo: john@doe.com}');
        $this->assertEquals('{sendTo: john@doe.com}', $this->subject->getRequest());
    }

    public function testSetRequestSetsTheRightKey()
    {
        $this
            ->subject
            ->expects($this->once())
            ->method('setData')
            ->with(Subject::REQUEST, 'foobar');
        $this->subject->setRequest('foobar');
    }

    public function testGetResponseGetsTheRightKey()
    {
        $this
            ->subject
            ->expects($this->once())
            ->method('getData')
            ->with(Subject::RESPONSE)
            ->willReturn('{success: true}');
        $this->assertEquals('{success: true}', $this->subject->getResponse());
    }

    public function testSetResponseSetsTheRightKey()
    {
        $this
            ->subject
            ->expects($this->once())
            ->method('setData')
            ->with(Subject::RESPONSE, 'foobar');
        $this->subject->setResponse('foobar');
    }

    public function testGetOrderIdGetsTheRightKey()
    {
        $this
            ->subject
            ->expects($this->once())
            ->method('getData')
            ->with(Subject::ORDER_ID)
            ->willReturn(323);
        $this->assertEquals(323, $this->subject->getOrderId());
    }

    public function testSetOrderIdSetsTheRightKey()
    {
        $this
            ->subject
            ->expects($this->once())
            ->method('setData')
            ->with(Subject::ORDER_ID, 'foobar');
        $this->subject->setOrderId('foobar');
    }

    public function testGetCustomerIdGetsTheRightKey()
    {
        $this
            ->subject
            ->expects($this->once())
            ->method('getData')
            ->with(Subject::CUSTOMER_ID)
            ->willReturn(989);
        $this->assertEquals(989, $this->subject->getCustomerId());
    }

    public function testSetCustomerIdSetsTheRightKey()
    {
        $this
            ->subject
            ->expects($this->once())
            ->method('setData')
            ->with(Subject::CUSTOMER_ID, 'foobar');
        $this->subject->setCustomerId('foobar');
    }

    public function testGetCreationTimeGetsTheRightKey()
    {
        $this
            ->subject
            ->expects($this->once())
            ->method('getData')
            ->with(Subject::CREATION_TIME)
            ->willReturn('2016-08-09 00:12:12');
        $this->assertEquals('2016-08-09 00:12:12', $this->subject->getCreationTime());
    }

    public function testSetCreationTimeSetsTheRightKey()
    {
        $this
            ->subject
            ->expects($this->once())
            ->method('setData')
            ->with(Subject::CREATION_TIME, 'foobar');
        $this->subject->setCreationTime('foobar');
    }

    public function testGetIdentitesReturnsExpectedValue()
    {
        $this
            ->subject
            ->expects($this->once())
            ->method('getData')
            ->with(Subject::EMAIL_ID)
            ->willReturn(234);
        $this->assertEquals([Subject::CACHE_TAG . '_234'], $this->subject->getIdentities());
    }


    public function testFindAndSetCustomerIdFromEmailUsesRepositoryAndSetsIdToRightAttribute()
    {
        $customerEmail = 'customer@email.com';
        $customerId = 3432;

        $this
            ->subject
            ->expects($this->any())
            ->method('getData')
            ->with(Subject::TO)
            ->will($this->returnValue($customerEmail));

        $this
            ->searchCriteriaBuilder
            ->expects($this->once())
            ->method('addFilter')
            ->with(CustomerInterface::EMAIL, $customerEmail, 'eq')
            ->will($this->returnValue($this->searchCriteriaBuilder));

        $this
            ->searchCriteriaBuilder
            ->expects($this->once())
            ->method('create')
            ->will($this->returnValue($this->searchCriteria));

        $collection = $this->getMockBuilder(DataObject::class)
            ->setMethods(['getTotalCount', 'getItems'])
            ->disableOriginalConstructor()
            ->getMock();

        $collection
            ->expects($this->once())
            ->method('getTotalCount')
            ->willReturn(1);

        $collection
            ->expects($this->once())
            ->method('getItems')
            ->willReturn([$this->customer]);

        $this
            ->customerRepository
            ->expects($this->once())
            ->method('getList')
            ->with($this->searchCriteria)
            ->will($this->returnValue($collection));

        $this
            ->customer
            ->expects($this->once())
            ->method('getId')
            ->willReturn($customerId);

        $this
            ->subject
            ->expects($this->once())
            ->method('setData')
            ->with(Subject::CUSTOMER_ID, $customerId);

        $this->assertEquals($this->subject, $this->subject->findAndSetCustomerIdFromEmail());

    }


    public function testFindAndSetCustomerIdFromEmailUsesRepositoryAndSetsIdToNullWhenNoCustomerFound()
    {
        $customerEmail = 'customer@email.com';

        $this
            ->subject
            ->expects($this->any())
            ->method('getData')
            ->with(Subject::TO)
            ->will($this->returnValue($customerEmail));

        $this
            ->searchCriteriaBuilder
            ->expects($this->once())
            ->method('addFilter')
            ->with(CustomerInterface::EMAIL, $customerEmail, 'eq')
            ->will($this->returnValue($this->searchCriteriaBuilder));

        $this
            ->searchCriteriaBuilder
            ->expects($this->once())
            ->method('create')
            ->will($this->returnValue($this->searchCriteria));

        $collection = $this->getMockBuilder(DataObject::class)
            ->setMethods(['getTotalCount', 'getItems'])
            ->disableOriginalConstructor()
            ->getMock();

        $collection
            ->expects($this->once())
            ->method('getTotalCount')
            ->willReturn(0);

        $this
            ->customerRepository
            ->expects($this->once())
            ->method('getList')
            ->with($this->searchCriteria)
            ->will($this->returnValue($collection));

        $this
            ->subject
            ->expects($this->once())
            ->method('setData')
            ->with(Subject::CUSTOMER_ID, null);

        $this->assertEquals($this->subject, $this->subject->findAndSetCustomerIdFromEmail());
    }

    public function testGetCustomerGetsItFromRepositoryOnlyOnceAndReturnsIt()
    {
        $customerId = 9383;
        $this
            ->subject
            ->expects($this->once())
            ->method('getData')
            ->with(Subject::CUSTOMER_ID)
            ->willReturn($customerId);

        $this
            ->customerRepository
            ->expects($this->once())
            ->method('getById')
            ->with($customerId)
            ->willReturn($this->customer);

        $this->assertEquals($this->customer, $this->subject->getCustomer());
        $this->assertEquals($this->customer, $this->subject->getCustomer());
    }

    public function testGetsalesOrderGetsItFromRepositoryOnlyOnceAndReturnsIt()
    {
        $salesOrderId = 343423;
        $this
            ->subject
            ->expects($this->once())
            ->method('getData')
            ->with(Subject::ORDER_ID)
            ->willReturn($salesOrderId);

        $this
            ->salesOrderRepository
            ->expects($this->once())
            ->method('get')
            ->with($salesOrderId)
            ->willReturn($this->salesOrder);

        $this->assertEquals($this->salesOrder, $this->subject->getOrder());
        $this->assertEquals($this->salesOrder, $this->subject->getOrder());
    }

}