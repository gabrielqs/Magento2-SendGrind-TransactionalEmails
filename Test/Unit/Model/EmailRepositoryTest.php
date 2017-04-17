<?php

namespace Gabrielqs\TransactionalEmails\Test\Unit\Unit\Model;

use \Magento\Framework\Exception\CouldNotDeleteException;
use \Magento\Framework\Exception\CouldNotSaveException;
use \Magento\Framework\Exception\NoSuchEntityException;
use \Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use \Magento\Framework\Api\SortOrder;
use \Gabrielqs\TransactionalEmails\Model\EmailRepository as Subject;
use \Gabrielqs\TransactionalEmails\Model\ResourceModel\Email as EmailResource;
use \Gabrielqs\TransactionalEmails\Model\EmailFactory as EmailFactory;
use \Gabrielqs\TransactionalEmails\Model\ResourceModel\Email\Collection as EmailCollection;
use \Gabrielqs\TransactionalEmails\Model\Email as Email;
use \Gabrielqs\TransactionalEmails\Api\Data\EmailSearchResultsInterfaceFactory;



/**
 * Unit Testcase
 */
class FileRepositoryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var String
     */
    protected $className = null;

    /**
     * @var ObjectManager
     */
    protected $objectManager = null;

    /**
     * @var Subject
     */
    protected $originalSubject = null;

    /**
     * Email Resource
     * @var EmailResource
     */
    protected $resource;

    /**
     * File Factory
     * @var EmailFactory
     */
    protected $emailFactory;

    /**
     * File Collection Factory
     * @var EmailSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

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

        $this->resource = $this->getMockBuilder(EmailResource::class)
            ->setMethods(['save', 'load', 'delete'])
            ->disableOriginalConstructor()
            ->getMock();
        $arguments['resource'] = $this->resource;

        $this->emailFactory = $this->getMockBuilder(EmailFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $arguments['emailFactory'] = $this->emailFactory;

        $this->searchResultsFactory = $this->getMockBuilder(EmailSearchResultsInterfaceFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $arguments['searchResultsFactory'] = $this->searchResultsFactory;

        return $arguments;
    }

    public function testSaveReturnsCouldNotSaveExceptionOnException()
    {
        $email = $this->getMockBuilder(Email::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this
            ->resource
            ->expects($this->once())
            ->method('save')
            ->will($this->throwException(new \Exception()));

        $this->setExpectedException(CouldNotSaveException::class);
        $this->subject->save($email);
    }

    public function testSaveReturnsSuccessfullySavesEntityToResourceAndReturnsIt()
    {
        $email = $this->getMockBuilder(Email::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this
            ->resource
            ->expects($this->once())
            ->method('save')
            ->will($this->returnValue($email));

        $return = $this->subject->save($email);

        $this->assertEquals($return, $email);
    }

    public function testGetByIdThrowsNoSuchEntityExceptionWhenEntityNotFound()
    {
        $email = $this->getMockBuilder(Email::class)
            ->disableOriginalConstructor()
            ->setMethods(['getId'])
            ->getMock();

        $email
            ->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(null));

        $this
            ->emailFactory
            ->expects($this->once())
            ->method('create')
            ->will($this->returnValue($email));

        $this
            ->resource
            ->expects($this->once())
            ->method('load')
            ->with($email, 324)
            ->will($this->returnValue($email));

        $this->setExpectedException(NoSuchEntityException::class);
        $this->subject->getById(324);

    }

    public function testGetByIdReturnsLoadedEntityWhenEntityFound()
    {
        $email = $this->getMockBuilder(Email::class)
            ->disableOriginalConstructor()
            ->setMethods(['getId'])
            ->getMock();

        $email
            ->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(324));

        $this
            ->emailFactory
            ->expects($this->once())
            ->method('create')
            ->will($this->returnValue($email));

        $this
            ->resource
            ->expects($this->once())
            ->method('load')
            ->with($email, 324)
            ->will($this->returnValue($email));

        $return = $this->subject->getById(324);

        $this->assertEquals($email, $return);

    }
    
    public function testDeleteByIdUsesResourceToDeleteAndReturnsTrue()
    {
        $emailId = '123';

        $email = $this->getMockBuilder(Email::class)
            ->disableOriginalConstructor()
            ->setMethods(['getId'])
            ->getMock();

        $email->expects($this->any())
            ->method('getId')
            ->willReturn($emailId);

        $this
            ->emailFactory
            ->expects($this->once())
            ->method('create')
            ->will($this->returnValue($email));

        $this
            ->resource
            ->expects($this->once())
            ->method('load')
            ->with($email, $emailId)
            ->willReturn($email);

        $this
            ->resource
            ->expects($this->once())
            ->method('delete')
            ->with($email)
            ->willReturnSelf();

        $this->assertTrue($this->subject->deleteById($emailId));
    }

    public function testDeleteByIdThrowsExceptionWhenEntityNotFound()
    {
        $emailId = '123';

        $email = $this->getMockBuilder(Email::class)
            ->disableOriginalConstructor()
            ->setMethods(['getId'])
            ->getMock();

        $email->expects($this->any())
            ->method('getId')
            ->willReturn(false);

        $this
            ->emailFactory
            ->expects($this->once())
            ->method('create')
            ->will($this->returnValue($email));

        $this
            ->resource
            ->expects($this->once())
            ->method('load')
            ->with($email, $emailId)
            ->will($this->throwException(new NoSuchEntityException()));

        $this
            ->resource
            ->expects($this->never())
            ->method('delete')
            ->willReturnSelf();

        $this->setExpectedException(NoSuchEntityException::class);

        $this->assertTrue($this->subject->deleteById($emailId));
    }

    public function testDeleteByIdThrowsExceptionWhenCouldNotDelete()
    {
        $emailId = '123';

        $email = $this->getMockBuilder(Email::class)
            ->disableOriginalConstructor()
            ->setMethods(['getId'])
            ->getMock();

        $email
            ->expects($this->any())
            ->method('getId')
            ->willReturn($emailId);

        $this
            ->emailFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn($email);

        $this
            ->resource
            ->expects($this->once())
            ->method('load')
            ->with($email, $emailId)
            ->willReturn($email);

        $this
            ->resource
            ->expects($this->once())
            ->method('delete')
            ->will($this->throwException(new \Exception()));

        $this->setExpectedException(CouldNotDeleteException::class);

        $this->assertTrue($this->subject->deleteById($emailId));
    }

    public function testGetList()
    {
        $field = 'name';
        $value = 'magento';
        $condition = 'eq';
        $currentPage = 3;
        $pageSize = 2;
        $sortField = 'id';

        $criteria = $this->getMockBuilder('Magento\Framework\Api\SearchCriteriaInterface')->getMock();
        $filterGroup = $this->getMockBuilder('Magento\Framework\Api\Search\FilterGroup')->getMock();
        $filter = $this->getMockBuilder('Magento\Framework\Api\Filter')->getMock();
        $storeFilter = $this->getMockBuilder('Magento\Framework\Api\Filter')->getMock();
        $sortOrder = $this->getMockBuilder('Magento\Framework\Api\SortOrder')->getMock();

        $criteria->expects($this->once())->method('getFilterGroups')->willReturn([$filterGroup]);
        $criteria->expects($this->once())->method('getSortOrders')->willReturn([$sortOrder]);
        $criteria->expects($this->once())->method('getCurrentPage')->willReturn($currentPage);
        $criteria->expects($this->once())->method('getPageSize')->willReturn($pageSize);
        $filterGroup->expects($this->once())->method('getFilters')->willReturn([$storeFilter, $filter]);
        $filter->expects($this->any())->method('getConditionType')->willReturn($condition);
        $filter->expects($this->any())->method('getField')->willReturn($field);
        $filter->expects($this->once())->method('getValue')->willReturn($value);
        $storeFilter->expects($this->any())->method('getField')->willReturn('store_id');
        $storeFilter->expects($this->once())->method('getValue')->willReturn(1);
        $sortOrder->expects($this->once())->method('getField')->willReturn($sortField);
        $sortOrder->expects($this->once())->method('getDirection')->willReturn(SortOrder::SORT_DESC);

        $emailCollection = $this->getMockBuilder(EmailCollection::class)
            ->disableOriginalConstructor()
            ->setMethods([
                'addOrder',
                'setSearchCriteria',
                'setCurPage',
                'setPageSize',
                'addFieldToFilter'
                ])
            ->getMock();

        $this
            ->searchResultsFactory
            ->expects($this->once())
            ->method('create')
            ->will($this->returnValue($emailCollection));

        $emailCollection
            ->expects($this->once())
            ->method('addFieldToFilter')
            ->with([ 'store_id', 'name' ],
            [
                [ 'eq' => 1 ],
                [ 'eq' => 'magento' ]
            ])
            ->willReturnSelf();

        $emailCollection->expects($this->once())->method('setCurPage')->with($currentPage)->willReturnSelf();
        $emailCollection->expects($this->once())->method('setPageSize')->with($pageSize)->willReturnSelf();
        $emailCollection->expects($this->once())->method('addOrder')->with($sortField, 'DESC')->willReturnSelf();

        $this->assertEquals($emailCollection, $this->subject->getList($criteria));
    }

    public function testGetListNoSortOrdersCreatesArray()
    {
        $field = 'name';
        $value = 'magento';
        $condition = 'eq';
        $currentPage = 3;
        $pageSize = 2;
        $sortField = 'id';

        $criteria = $this->getMockBuilder('Magento\Framework\Api\SearchCriteriaInterface')->getMock();
        $filterGroup = $this->getMockBuilder('Magento\Framework\Api\Search\FilterGroup')->getMock();
        $filter = $this->getMockBuilder('Magento\Framework\Api\Filter')->getMock();
        $storeFilter = $this->getMockBuilder('Magento\Framework\Api\Filter')->getMock();
        $sortOrder = $this->getMockBuilder('Magento\Framework\Api\SortOrder')->getMock();

        $criteria->expects($this->once())->method('getFilterGroups')->willReturn([$filterGroup]);
        $criteria->expects($this->once())->method('getSortOrders')->willReturn(null);
        $criteria->expects($this->once())->method('getCurrentPage')->willReturn($currentPage);
        $criteria->expects($this->once())->method('getPageSize')->willReturn($pageSize);
        $filterGroup->expects($this->once())->method('getFilters')->willReturn([$storeFilter, $filter]);
        $filter->expects($this->any())->method('getConditionType')->willReturn($condition);
        $filter->expects($this->any())->method('getField')->willReturn($field);
        $filter->expects($this->once())->method('getValue')->willReturn($value);
        $storeFilter->expects($this->any())->method('getField')->willReturn('store_id');
        $storeFilter->expects($this->once())->method('getValue')->willReturn(1);

        $emailCollection = $this->getMockBuilder(EmailCollection::class)
            ->disableOriginalConstructor()
            ->setMethods([
                'setSearchCriteria',
                'setCurPage',
                'setPageSize',
                'addFieldToFilter'
                ])
            ->getMock();

        $this
            ->searchResultsFactory
            ->expects($this->once())
            ->method('create')
            ->will($this->returnValue($emailCollection));

        $emailCollection
            ->expects($this->once())
            ->method('addFieldToFilter')
            ->with([ 'store_id', 'name' ],
            [
                [ 'eq' => 1 ],
                [ 'eq' => 'magento' ]
            ])
            ->willReturnSelf();

        $emailCollection->expects($this->once())->method('setCurPage')->with($currentPage)->willReturnSelf();
        $emailCollection->expects($this->once())->method('setPageSize')->with($pageSize)->willReturnSelf();

        $this->assertEquals($emailCollection, $this->subject->getList($criteria));
    }

}