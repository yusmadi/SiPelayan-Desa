<?php

namespace Tests\Filters;

use App\Filters\AuthFilter;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FilterTestTrait;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class AuthFilterTest extends CIUnitTestCase
{
    use FilterTestTrait;

    protected function setUp(): void
    {
        parent::setUp();
        // Clear session properly for testing
        \Config\Services::session()->set([]);
    }

    public function testNotLoggedInRedirectsToLogin()
    {
        $filter = new AuthFilter();
        $request = Services::request();
        
        $result = $filter->before($request);
        
        $this->assertInstanceOf(\CodeIgniter\HTTP\RedirectResponse::class, $result);
        $this->assertEquals(site_url('/auth/login'), $result->getHeaderLine('Location'));
    }

    public function testLoggedInAllowsAccess()
    {
        session()->set([
            'user_id' => 1,
            'role_slug' => 'warga'
        ]);
        
        $filter = new AuthFilter();
        $request = Services::request();
        
        // Note: For this to fully pass, UserModel must either not be instantiated
        // or properly mocked. Since our AuthFilter skips DB check if UserModel isn't 
        // fully implemented, it should return null here.
        
        $result = $filter->before($request);
        $this->assertNull($result);
    }
}
