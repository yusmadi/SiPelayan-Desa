<?php

namespace Tests\Filters;

use App\Filters\RoleFilter;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FilterTestTrait;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class RoleFilterTest extends CIUnitTestCase
{
    use FilterTestTrait;

    protected function setUp(): void
    {
        parent::setUp();
        \Config\Services::session()->set([]);
    }

    public function testEmptyArgumentsAllowsAccess()
    {
        session()->set('role_slug', 'warga');
        
        $filter = new RoleFilter();
        $request = Services::request(null, false);
        
        // If no roles specified in route, allow everyone
        $result = $filter->before($request, []);
        $this->assertNull($result);
    }

    public function testAuthorizedRoleAllowsAccess()
    {
        session()->set('role_slug', 'admin_desa');
        
        $filter = new RoleFilter();
        $request = Services::request(null, false);
        
        // Allowed roles: admin_desa, super_admin
        $result = $filter->before($request, ['admin_desa,super_admin']);
        $this->assertNull($result);
    }

    public function testUnauthorizedRoleRedirectsToHome()
    {
        session()->set([
            'user_id' => 10,
            'role_slug' => 'warga'
        ]);
        
        $filter = new RoleFilter();
        $request = Services::request(null, false);
        
        // Route only allows admin_desa or super_admin
        $result = $filter->before($request, ['admin_desa,super_admin']);
        
        if (! ($result instanceof \CodeIgniter\HTTP\RedirectResponse)) {
            var_dump(is_object($result) ? get_class($result) : gettype($result));
        }
        
        $this->assertInstanceOf(\CodeIgniter\HTTP\RedirectResponse::class, $result);
        $this->assertEquals(site_url('/'), $result->getHeaderLine('Location'));
    }

    public function testUnauthorizedRoleAjaxReturnsJson403()
    {
        session()->set([
            'user_id' => 10,
            'role_slug' => 'warga'
        ]);
        
        $filter = new RoleFilter();
        // Simulate AJAX request
        $request = Services::request();
        $request->setHeader('X-Requested-With', 'XMLHttpRequest');
        
        $result = $filter->before($request, ['admin_desa']);
        
        $this->assertInstanceOf(\CodeIgniter\HTTP\ResponseInterface::class, $result);
        $this->assertEquals(403, $result->getStatusCode());
        
        $body = json_decode($result->getBody(), true);
        $this->assertEquals('error', $body['status']);
        $this->assertStringContainsString('Akses ditolak', $body['message']);
        
        // Remove header for next tests
        $request->removeHeader('X-Requested-With');
    }
}
