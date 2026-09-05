<?php

namespace Tests\Filters;

use App\Filters\RateLimitFilter;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FilterTestTrait;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class RateLimitFilterTest extends CIUnitTestCase
{
    use FilterTestTrait;

    protected function setUp(): void
    {
        parent::setUp();
        // Clear cache for isolated testing
        Services::cache()->clean();
        \Config\Services::session()->set([]);
    }

    public function testRateLimitUnderLimitAllowsAccess()
    {
        $filter = new RateLimitFilter();
        $request = Services::request();
        
        // limit 5 requests per 60 seconds
        for ($i = 0; $i < 4; $i++) {
            $result = $filter->before($request, ['5:60']);
            $this->assertNull($result, "Request $i should be allowed");
        }
    }

    public function testRateLimitExceededBlocksAccess()
    {
        $filter = new RateLimitFilter();
        $request = Services::request();
        
        // limit 3 requests per 60 seconds
        $filter->before($request, ['3:60']); // 1
        $filter->before($request, ['3:60']); // 2
        $filter->before($request, ['3:60']); // 3
        
        // 4th request should block
        $result = $filter->before($request, ['3:60']);
        
        $this->assertInstanceOf(\CodeIgniter\HTTP\ResponseInterface::class, $result);
        $this->assertEquals(429, $result->getStatusCode());
        $this->assertStringContainsString('Too Many Requests', $result->getBody());
    }

    public function testRateLimitExceededAjaxReturnsJson429()
    {
        $filter = new RateLimitFilter();
        $request = Services::request();
        $request->setHeader('X-Requested-With', 'XMLHttpRequest');
        
        // limit 1 request per 60 seconds
        $filter->before($request, ['1:60']);
        
        // 2nd request should block
        $result = $filter->before($request, ['1:60']);
        
        $this->assertInstanceOf(\CodeIgniter\HTTP\ResponseInterface::class, $result);
        $this->assertEquals(429, $result->getStatusCode());
        $this->assertEquals('60', $result->getHeaderLine('Retry-After'));
        
        $body = json_decode($result->getBody(), true);
        $this->assertEquals('error', $body['status']);
        $this->assertStringContainsString('Terlalu banyak permintaan', $body['message']);
    }
}
