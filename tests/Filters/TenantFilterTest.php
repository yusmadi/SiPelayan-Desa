<?php

namespace Tests\Filters;

use App\Filters\TenantFilter;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FilterTestTrait;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class TenantFilterTest extends CIUnitTestCase
{
    use FilterTestTrait;

    protected function setUp(): void
    {
        parent::setUp();
        \Config\Services::session()->set([]);
    }

    public function testSuperAdminBypassTenantFilter()
    {
        session()->set('role_slug', 'super_admin');
        
        $filter = new TenantFilter();
        $request = Services::request();
        
        // Before method should return null for super_admin (bypassed)
        $result = $filter->before($request);
        $this->assertNull($result);
    }

    public function testNoSessionVillageIdRedirectsToLogin()
    {
        session()->set('role_slug', 'warga');
        // Intentionally not setting village_id
        
        $filter = new TenantFilter();
        $request = Services::request();
        
        $result = $filter->before($request);
        
        $this->assertInstanceOf(\CodeIgniter\HTTP\RedirectResponse::class, $result);
        $this->assertEquals(site_url('/auth/login'), $result->getHeaderLine('Location'));
    }

    public function testValidTenantAccessIsAllowed()
    {
        session()->set([
            'role_slug' => 'warga',
            'village_id' => 12
        ]);
        
        $filter = new TenantFilter();
        $request = Services::request(null, false);
        // Simulate URL /admin-desa/12
        $uri = new \CodeIgniter\HTTP\URI('http://localhost/admin-desa/12');
        $request = $request->withUri($uri);
        
        $result = $filter->before($request);
        
        // Allowed access should return null
        $this->assertNull($result);
        unset($_GET['village_id']);
    }

    public function testCrossTenantAccessIsBlocked()
    {
        session()->set([
            'user_id' => 99,
            'role_slug' => 'admin_desa',
            'village_id' => 5 // User belongs to village 5
        ]);
        
        $filter = new TenantFilter();
        $request = Services::request(null, false);
        // Simulate URL /admin-desa/10
        $uri = new \CodeIgniter\HTTP\URI('http://localhost/admin-desa/10');
        $request = $request->withUri($uri);
        
        $result = $filter->before($request);
        
        // Blocked access should return a redirect to dashboard
        $this->assertInstanceOf(\CodeIgniter\HTTP\RedirectResponse::class, $result);
        $this->assertEquals(site_url('/'), $result->getHeaderLine('Location'));
        
        unset($_GET['village_id']);
    }
}
