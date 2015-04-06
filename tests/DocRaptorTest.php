<?php

use DocRaptor\ApiWrapper;
use DocRaptor\HttpClientMock;

class DocRaptorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var ApiWrapper;
     */
    protected $docRaptor;

    protected function setUp()
    {
        $httpClientMock = new HttpClientMock();
        $this->docRaptor = new ApiWrapper(null, $httpClientMock);
    }

    public function testCanSetApiKeyViaConstructor()
    {
        $httpClientMock = new HttpClientMock();
        $docRaptor = new ApiWrapper('my-key', $httpClientMock);
        $this->assertEquals('my-key', $docRaptor->getApiKey());
    }

    public function testCanConstructWithoutApiKey()
    {
        $this->assertNull($this->docRaptor->getApiKey());
    }

    public function testCanSetApiKeyViaSetter()
    {
        $this->docRaptor->setApiKey('my-key');
        $this->assertEquals('my-key', $this->docRaptor->getApiKey());
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
        $this->docRaptor->setDocumentType('xLS');
        $this->assertEquals('xls', $this->docRaptor->getDocumentType());
    }

    public function testAllowOnlyStrictBoolToToggleTestFlag()
    {
        $this->docRaptor->setTest('true');
        $this->assertEquals(false, $this->docRaptor->getTest());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testNoInvalidValidationTypesAllowed()
    {
        $this->docRaptor->setStrict(false);
    }

    public function testAllowOnlyStrictBoolToToggleHelpFlag()
    {
        $this->docRaptor->setHelp('true');
        $this->assertEquals(false, $this->docRaptor->getHelp());
    }

    /**
     * @expectedException \DocRaptor\Exception\MissingAPIKeyException
     */
    public function testCallingFetchDocumentWhenApiKeyIsMissingThrowsException()
    {
        $this->docRaptor->fetchDocument();
    }

    /**
     * @expectedException \DocRaptor\Exception\MissingContentException
     */
    public function testCallingFetchDocumentWithoutContentThrowsException()
    {
        $this->docRaptor->setApiKey('my-key');
        $this->docRaptor->fetchDocument();
    }

    public function testFetchDocumentReturnsResultWhenContentIsGiven()
    {
        $this->docRaptor->setApiKey('my-key');
        $this->docRaptor->setDocumentContent('test');
        $result = $this->docRaptor->fetchDocument();
        $this->assertEquals('success', $result);
    }

    public function testFetchDocumentReturnsResultWhenUrlIsGiven()
    {
        $this->docRaptor->setApiKey('my-key');
        $this->docRaptor->setDocumentUrl('test');
        $result = $this->docRaptor->fetchDocument();
        $this->assertEquals('success', $result);
    }

    public function testFileIsWrittenWhenFilenameIsPassed()
    {
        $this->docRaptor->setApiKey('my-key');
        $this->docRaptor->setDocumentContent('test');
        $result = $this->docRaptor->fetchDocument('test.txt');
        $this->assertFileExists('test.txt');
        $this->assertEquals(true, $result);
        $this->assertStringEqualsFile('test.txt', 'success');
    }
}