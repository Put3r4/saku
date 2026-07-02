<?php

use App\Models\Menu;
use App\Models\MenuEvaluation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Menu Management - Admin CRUD Operations', function () {
    beforeEach(function () {
        // Setup users
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->student = User::factory()->create(['role' => 'mahasiswa']);
    });

    describe('Admin Access Control', function () {
        it('allows admin to access menu index page', function () {
            $this->actingAs($this->admin)
                ->get(route('admin.menu.index'))
                ->assertOk()
                ->assertViewIs('admin.menu.index');
        });

        it('prevents non-admin from accessing menu management', function () {
            $this->actingAs($this->student)
                ->get(route('admin.menu.index'))
                ->assertForbidden();
        });

        it('redirects guest to login', function () {
            $this->get(route('admin.menu.index'))
                ->assertRedirect(route('login'));
        });
    });

    describe('Create Menu', function () {
        it('allows admin to create new menu', function () {
            $menuData = [
                'vendor_name' => 'Warung Pak Budi',
                'menu_name' => 'Nasi Goreng Spesial',
                'price' => 15000,
                'description' => 'Nasi goreng dengan telur mata sapi',
                'image_url' => 'https://example.com/image.jpg',
                'is_available' => true,
            ];

            $this->actingAs($this->admin)
                ->post(route('admin.menu.store'), $menuData)
                ->assertRedirect(route('admin.menu.index'))
                ->assertSessionHas('success', 'Menu baru berhasil ditambahkan ke sistem!');

            $this->assertDatabaseHas('menus', [
                'vendor_name' => 'Warung Pak Budi',
                'menu_name' => 'Nasi Goreng Spesial',
                'price' => 15000,
                'is_available' => true,
            ]);
        });

        it('prevents non-admin from creating menu', function () {
            $menuData = [
                'vendor_name' => 'Test Vendor',
                'menu_name' => 'Test Menu',
                'price' => 10000,
                'is_available' => true,
            ];

            $this->actingAs($this->student)
                ->post(route('admin.menu.store'), $menuData)
                ->assertForbidden();

            $this->assertDatabaseMissing('menus', ['menu_name' => 'Test Menu']);
        });

        it('validates required fields when creating menu', function () {
            $this->actingAs($this->admin)
                ->post(route('admin.menu.store'), [])
                ->assertSessionHasErrors(['vendor_name', 'menu_name', 'price']);
        });

        it('validates price must be numeric and non-negative', function () {
            $this->actingAs($this->admin)
                ->post(route('admin.menu.store'), [
                    'vendor_name' => 'Test Vendor',
                    'menu_name' => 'Test Menu',
                    'price' => -1000, // Negative price
                ])
                ->assertSessionHasErrors(['price']);

            $this->actingAs($this->admin)
                ->post(route('admin.menu.store'), [
                    'vendor_name' => 'Test Vendor',
                    'menu_name' => 'Test Menu',
                    'price' => 'not-a-number', // Invalid
                ])
                ->assertSessionHasErrors(['price']);
        });

        it('sets is_available to false when not provided', function () {
            $this->actingAs($this->admin)
                ->post(route('admin.menu.store'), [
                    'vendor_name' => 'Test Vendor',
                    'menu_name' => 'Test Menu',
                    'price' => 10000,
                    // is_available not provided
                ])
                ->assertRedirect(route('admin.menu.index'));

            $this->assertDatabaseHas('menus', [
                'menu_name' => 'Test Menu',
                'is_available' => false, // Default
            ]);
        });

        it('accepts nullable description and image_url', function () {
            $this->actingAs($this->admin)
                ->post(route('admin.menu.store'), [
                    'vendor_name' => 'Test Vendor',
                    'menu_name' => 'Simple Menu',
                    'price' => 5000,
                    'description' => null,
                    'image_url' => null,
                ])
                ->assertRedirect(route('admin.menu.index'))
                ->assertSessionHasNoErrors();

            $this->assertDatabaseHas('menus', [
                'menu_name' => 'Simple Menu',
                'description' => null,
                'image_url' => null,
            ]);
        });
    });

    describe('Read Menu', function () {
        it('displays list of menus sorted by vendor and menu name', function () {
            Menu::factory()->create(['vendor_name' => 'Warung B', 'menu_name' => 'Menu 2']);
            Menu::factory()->create(['vendor_name' => 'Warung A', 'menu_name' => 'Menu 1']);
            Menu::factory()->create(['vendor_name' => 'Warung A', 'menu_name' => 'Menu 3']);

            $response = $this->actingAs($this->admin)
                ->get(route('admin.menu.index'))
                ->assertOk()
                ->assertViewIs('admin.menu.index')
                ->assertViewHas('menus');

            $menus = $response->viewData('menus');

            // Should be sorted by vendor_name, then menu_name
            expect($menus[0]->vendor_name)->toBe('Warung A');
            expect($menus[0]->menu_name)->toBe('Menu 1');
            expect($menus[1]->vendor_name)->toBe('Warung A');
            expect($menus[1]->menu_name)->toBe('Menu 3');
            expect($menus[2]->vendor_name)->toBe('Warung B');
        });
    });

    describe('Update Menu', function () {
        it('allows admin to update existing menu', function () {
            $menu = Menu::factory()->create([
                'vendor_name' => 'Old Vendor',
                'menu_name' => 'Old Menu',
                'price' => 10000,
                'is_available' => false,
            ]);

            $this->actingAs($this->admin)
                ->put(route('admin.menu.update', $menu), [
                    'vendor_name' => 'New Vendor',
                    'menu_name' => 'New Menu',
                    'price' => 15000,
                    'description' => 'Updated description',
                    'is_available' => true,
                ])
                ->assertRedirect(route('admin.menu.index'))
                ->assertSessionHas('success', 'Menu berhasil diperbarui!');

            $this->assertDatabaseHas('menus', [
                'id' => $menu->id,
                'vendor_name' => 'New Vendor',
                'menu_name' => 'New Menu',
                'price' => 15000,
                'is_available' => true,
            ]);
        });

        it('prevents non-admin from updating menu', function () {
            $menu = Menu::factory()->create(['menu_name' => 'Original Name']);

            $this->actingAs($this->student)
                ->put(route('admin.menu.update', $menu), [
                    'vendor_name' => $menu->vendor_name,
                    'menu_name' => 'Hacked Name',
                    'price' => $menu->price,
                ])
                ->assertForbidden();

            // Data should remain unchanged
            $this->assertDatabaseHas('menus', [
                'id' => $menu->id,
                'menu_name' => 'Original Name',
            ]);
        });

        it('validates updated data', function () {
            $menu = Menu::factory()->create();

            $this->actingAs($this->admin)
                ->put(route('admin.menu.update', $menu), [
                    'vendor_name' => '',
                    'menu_name' => '',
                    'price' => -5000,
                ])
                ->assertSessionHasErrors(['vendor_name', 'menu_name', 'price']);
        });
    });

    describe('Delete Menu', function () {
        it('allows admin to delete menu', function () {
            $menu = Menu::factory()->create(['menu_name' => 'To Be Deleted']);

            $this->actingAs($this->admin)
                ->delete(route('admin.menu.destroy', $menu))
                ->assertRedirect(route('admin.menu.index'))
                ->assertSessionHas('success');

            $this->assertDatabaseMissing('menus', ['id' => $menu->id]);
        });

        it('prevents non-admin from deleting menu', function () {
            $menu = Menu::factory()->create();

            $this->actingAs($this->student)
                ->delete(route('admin.menu.destroy', $menu))
                ->assertForbidden();

            $this->assertDatabaseHas('menus', ['id' => $menu->id]);
        });

        it('shows appropriate message when deleting menu with evaluations', function () {
            $menu = Menu::factory()->create();
            MenuEvaluation::factory()->create(['menu_id' => $menu->id]);

            $this->actingAs($this->admin)
                ->delete(route('admin.menu.destroy', $menu))
                ->assertRedirect(route('admin.menu.index'))
                ->assertSessionHas('success', function ($message) {
                    return str_contains($message, 'Menu berhasil dihapus') &&
                           str_contains($message, 'Data evaluasi terkait menu ini juga telah dihapus');
                });

            $this->assertDatabaseMissing('menus', ['id' => $menu->id]);
            $this->assertDatabaseMissing('menu_evaluations', ['menu_id' => $menu->id]);
        });

        it('cascades delete to related menu evaluations', function () {
            $menu = Menu::factory()->create();
            $evaluation1 = MenuEvaluation::factory()->create(['menu_id' => $menu->id]);
            $evaluation2 = MenuEvaluation::factory()->create(['menu_id' => $menu->id]);

            $this->actingAs($this->admin)
                ->delete(route('admin.menu.destroy', $menu));

            // Menu and all evaluations should be deleted
            $this->assertDatabaseMissing('menus', ['id' => $menu->id]);
            $this->assertDatabaseMissing('menu_evaluations', ['id' => $evaluation1->id]);
            $this->assertDatabaseMissing('menu_evaluations', ['id' => $evaluation2->id]);
        });
    });

    describe('Menu Availability Toggle', function () {
        it('can mark menu as unavailable', function () {
            $menu = Menu::factory()->create(['is_available' => true]);

            $this->actingAs($this->admin)
                ->put(route('admin.menu.update', $menu), [
                    'vendor_name' => $menu->vendor_name,
                    'menu_name' => $menu->menu_name,
                    'price' => $menu->price,
                    'is_available' => false,
                ])
                ->assertRedirect(route('admin.menu.index'));

            $this->assertDatabaseHas('menus', [
                'id' => $menu->id,
                'is_available' => false,
            ]);
        });

        it('can mark menu as available', function () {
            $menu = Menu::factory()->create(['is_available' => false]);

            $this->actingAs($this->admin)
                ->put(route('admin.menu.update', $menu), [
                    'vendor_name' => $menu->vendor_name,
                    'menu_name' => $menu->menu_name,
                    'price' => $menu->price,
                    'is_available' => true,
                ])
                ->assertRedirect(route('admin.menu.index'));

            $this->assertDatabaseHas('menus', [
                'id' => $menu->id,
                'is_available' => true,
            ]);
        });
    });
});
