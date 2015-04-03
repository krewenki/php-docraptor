<?php

use DocRaptor\ApiWrapper;

class DocRaptorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var ApiWrapper;
     */
    protected $docRaptor;

    protected function setUp()
    {
        $this->docRaptor = new ApiWrapper();
    }

    public function testCanSetApiKeyViaConstructor()
    {
        $docRaptor = new ApiWrapper('my-key');
        $this->assertEquals('my-key', $docRaptor->getApiKey());
    }

    public function testCanConstructWithoutApiKey()
    {
        $this->assertNull($this->docRaptor->getApiKey());
    }

    public function testDefaultDocumentTypeIsPdf()
    {
        $this->assertEquals('pdf', $this->docRaptor->getDocumentType());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testNoInvalidDocumentTypesAllowed()
    {
        $this->docRaptor->setDocumentType('wrong');
    }

    public function testCaseOfDocTypeDoesNotMatter()
    {
        $this->docRaptor->setDocumentType('XLS');
        $this->assertEquals('xls', $this->docRaptor->getDocumentType());
    }
}