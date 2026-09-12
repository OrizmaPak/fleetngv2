<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class FrontendMigrationTest extends TestCase
{
    use RefreshDatabase;

    private function account($role, $active = 1)
    {
        return User::create(['first_name' => 'Test', 'last_name' => 'Operator', 'email' => 'role' . $role . $active . '@example.test', 'password' => Hash::make('local-test-password'), 'user_type' => $role, 'is_active' => $active]);
    }

    public function test_all_registered_controller_handlers_exist()
    {
        foreach (Route::getRoutes() as $route) {
            if (strpos($route->getActionName(), '@') === false) continue;
            [$class, $method] = explode('@', $route->getActionName(), 2);
            $this->assertTrue(class_exists($class) && method_exists($class, $method), $route->uri() . ': ' . $route->getActionName());
        }
    }

    public function test_staff_login_uses_role_and_active_status_without_remote_http()
    {
        $user = $this->account(3);
        $this->post('/admin', ['email' => $user->email, 'password' => 'local-test-password'])->assertSessionHas('error_message');
        $this->assertGuest();
        $this->post('/user', ['email' => $user->email, 'password' => 'local-test-password', 'remember_me' => 'remember'])->assertRedirect('/analytics')->assertCookieExpired('user_cookies');
        $this->assertAuthenticatedAs($user);
        $this->assertEquals(3, session('user_role'));
        $this->get('/logout')->assertRedirect('/user');
        $this->assertGuest();
        $this->assertNull(session('user_role'));
        $inactive = $this->account(3, 0);
        $this->post('/user', ['email' => $inactive->email, 'password' => 'local-test-password'])->assertSessionHas('error_message');
        $this->assertGuest();
    }

    public function test_failed_login_does_not_flash_password()
    {
        $this->post('/user', ['email' => 'nobody@example.test', 'password' => 'private-value'])->assertSessionHas('error_message');
        $this->assertNull(session('_old_input.password'));
    }

    public function test_merchant_actions_are_role_and_entity_scoped()
    {
        $admin = $this->account(2);
        $super = $this->account(1);
        $merchant = $this->account(4);
        $this->actingAs($admin)->post('/superadmin/merchant/status', ['id' => $merchant->id, 'status' => 0])->assertForbidden();
        $this->actingAs($super)->post('/superadmin/merchant/status', ['id' => $admin->id, 'status' => 0])->assertNotFound();
        $this->actingAs($super)->post('/superadmin/merchant/status', ['id' => $merchant->id, 'status' => 0])->assertOk();
        $this->assertEquals(0, $merchant->fresh()->is_active);
        $this->actingAs($super)->post('/superadmin/merchant/delete', ['user_id' => $merchant->id])->assertRedirect('/superadmin/merchant-list');
        $this->assertSoftDeleted('users', ['id' => $merchant->id]);
    }

    public function test_company_user_cannot_read_another_users_trip_history()
    {
        $user = $this->account(3);
        $this->actingAs($user)->get('/user-trip-list/' . ($user->id + 1))->assertForbidden();
        $this->actingAs($user)->getJson('/user-trip-list-detail/' . ($user->id + 1))->assertForbidden();
    }

    public function test_modern_login_renders_isolated_controls()
    {
        app('view')->getFinder()->prependLocation(resource_path('views/themes/fleetng-modern'));
        view()->share('uiTheme', 'fleetng-modern');
        $this->get('/user')->assertOk()->assertSee('fleetng-password-toggle')->assertSee('operations.css')->assertSee('data-fleetng-theme="dark"', false);
    }
}
