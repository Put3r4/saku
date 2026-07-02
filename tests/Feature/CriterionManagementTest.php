<?php

use App\Models\Criterion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Criterion Management - Admin CRUD Operations', function () {
    beforeEach(function () {
        // Setup users
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->student = User::factory()->create(['role' => 'mahasiswa']);
    });

    describe('Admin Access Control', function () {
        it('allows admin to access criteria index page', function () {
            $this->actingAs($this->admin)
                ->get(route('admin.criteria.index'))
                ->assertOk()
                ->assertViewIs('admin.criteria.index');
        });

        it('prevents non-admin from accessing criteria management', function () {
            $this->actingAs($this->student)
                ->get(route('admin.criteria.index'))
                ->assertForbidden(); // 403
        });

        it('prevents guest from accessing criteria management', function () {
            $this->get(route('admin.criteria.index'))
                ->assertRedirect(route('login'));
        });
    });

    describe('Create Criterion', function () {
        it('allows admin to create new criterion', function () {
            $criterionData = [
                'kode' => 'C1',
                'nama_kriteria' => 'Protein',
                'tipe' => 'benefit',
                'bobot' => 0.4,
            ];

            $this->actingAs($this->admin)
                ->post(route('admin.criteria.store'), $criterionData)
                ->assertRedirect(route('admin.criteria.index'))
                ->assertSessionHas('success', 'Kriteria baru berhasil ditambahkan ke sistem!');

            $this->assertDatabaseHas('criteria', [
                'kode' => 'C1',
                'nama_kriteria' => 'Protein',
                'tipe' => 'benefit',
                'bobot' => 0.4,
            ]);
        });

        it('prevents non-admin from creating criterion', function () {
            $criterionData = [
                'kode' => 'C1',
                'nama_kriteria' => 'Test',
                'tipe' => 'benefit',
                'bobot' => 0.5,
            ];

            $this->actingAs($this->student)
                ->post(route('admin.criteria.store'), $criterionData)
                ->assertForbidden();

            $this->assertDatabaseMissing('criteria', ['kode' => 'C1']);
        });

        it('validates required fields when creating criterion', function () {
            $this->actingAs($this->admin)
                ->post(route('admin.criteria.store'), [])
                ->assertSessionHasErrors(['kode', 'nama_kriteria', 'tipe', 'bobot']);
        });

        it('validates unique kode constraint', function () {
            // Create existing criterion
            Criterion::factory()->create(['kode' => 'C1']);

            // Try to create duplicate
            $this->actingAs($this->admin)
                ->post(route('admin.criteria.store'), [
                    'kode' => 'C1', // Duplicate
                    'nama_kriteria' => 'Test',
                    'tipe' => 'benefit',
                    'bobot' => 0.3,
                ])
                ->assertSessionHasErrors(['kode']);
        });

        it('validates tipe must be benefit or cost', function () {
            $this->actingAs($this->admin)
                ->post(route('admin.criteria.store'), [
                    'kode' => 'C1',
                    'nama_kriteria' => 'Test',
                    'tipe' => 'invalid', // Invalid type
                    'bobot' => 0.5,
                ])
                ->assertSessionHasErrors(['tipe']);
        });
    });

    describe('Update Criterion', function () {
        it('allows admin to update existing criterion', function () {
            $criterion = Criterion::factory()->create([
                'kode' => 'C1',
                'nama_kriteria' => 'Old Name',
                'tipe' => 'benefit',
                'bobot' => 0.3,
            ]);

            $this->actingAs($this->admin)
                ->put(route('admin.criteria.update', $criterion), [
                    'kode' => 'C1',
                    'nama_kriteria' => 'New Name',
                    'tipe' => 'cost',
                    'bobot' => 0.4,
                ])
                ->assertRedirect(route('admin.criteria.index'))
                ->assertSessionHas('success', 'Kriteria berhasil diperbarui!');

            $this->assertDatabaseHas('criteria', [
                'id' => $criterion->id,
                'kode' => 'C1',
                'nama_kriteria' => 'New Name',
                'tipe' => 'cost',
                'bobot' => 0.4,
            ]);
        });

        it('prevents non-admin from updating criterion', function () {
            $criterion = Criterion::factory()->create(['nama_kriteria' => 'Original']);

            $this->actingAs($this->student)
                ->put(route('admin.criteria.update', $criterion), [
                    'kode' => $criterion->kode,
                    'nama_kriteria' => 'Hacked',
                    'tipe' => $criterion->tipe,
                    'bobot' => $criterion->bobot,
                ])
                ->assertForbidden();

            // Data should remain unchanged
            $this->assertDatabaseHas('criteria', [
                'id' => $criterion->id,
                'nama_kriteria' => 'Original',
            ]);
        });
    });

    describe('Delete Criterion', function () {
        it('allows admin to delete criterion', function () {
            $criterion = Criterion::factory()->create();

            $this->actingAs($this->admin)
                ->delete(route('admin.criteria.destroy', $criterion))
                ->assertRedirect(route('admin.criteria.index'))
                ->assertSessionHas('success', 'Kriteria berhasil dihapus dari sistem!');

            $this->assertDatabaseMissing('criteria', ['id' => $criterion->id]);
        });

        it('prevents non-admin from deleting criterion', function () {
            $criterion = Criterion::factory()->create();

            $this->actingAs($this->student)
                ->delete(route('admin.criteria.destroy', $criterion))
                ->assertForbidden();

            $this->assertDatabaseHas('criteria', ['id' => $criterion->id]);
        });
    });

    describe('Weight Validation - Total Bobot Cannot Exceed 1.00', function () {
        it('rejects creation when total weight would exceed 1.00', function () {
            // Create existing criteria with total weight 0.8
            Criterion::factory()->create(['kode' => 'C1', 'bobot' => 0.5]);
            Criterion::factory()->create(['kode' => 'C2', 'bobot' => 0.3]);
            // Total existing = 0.8

            // Try to add criterion with weight 0.3 (would make total 1.1)
            $this->actingAs($this->admin)
                ->post(route('admin.criteria.store'), [
                    'kode' => 'C3',
                    'nama_kriteria' => 'Test',
                    'tipe' => 'benefit',
                    'bobot' => 0.3, // 0.8 + 0.3 = 1.1 > 1.0 ❌
                ])
                ->assertSessionHasErrors(['bobot']);

            // Criterion should not be created
            $this->assertDatabaseMissing('criteria', ['kode' => 'C3']);
        });

        it('allows creation when total weight equals exactly 1.00', function () {
            // Create existing criteria with total weight 0.7
            Criterion::factory()->create(['kode' => 'C1', 'bobot' => 0.7]);

            // Add criterion with weight 0.3 (total = 1.0) ✅
            $this->actingAs($this->admin)
                ->post(route('admin.criteria.store'), [
                    'kode' => 'C2',
                    'nama_kriteria' => 'Test',
                    'tipe' => 'benefit',
                    'bobot' => 0.3,
                ])
                ->assertRedirect(route('admin.criteria.index'))
                ->assertSessionHasNoErrors();

            $this->assertDatabaseHas('criteria', ['kode' => 'C2']);
        });

        it('rejects update when total weight would exceed 1.00', function () {
            // Create criteria
            $criterion1 = Criterion::factory()->create(['kode' => 'C1', 'bobot' => 0.5]);
            $criterion2 = Criterion::factory()->create(['kode' => 'C2', 'bobot' => 0.3]);
            // Total = 0.8

            // Try to update C2 to 0.6 (would make total 1.1)
            $this->actingAs($this->admin)
                ->put(route('admin.criteria.update', $criterion2), [
                    'kode' => 'C2',
                    'nama_kriteria' => $criterion2->nama_kriteria,
                    'tipe' => $criterion2->tipe,
                    'bobot' => 0.6, // 0.5 + 0.6 = 1.1 > 1.0 ❌
                ])
                ->assertSessionHasErrors(['bobot']);

            // Weight should remain unchanged
            $this->assertDatabaseHas('criteria', [
                'id' => $criterion2->id,
                'bobot' => 0.3, // Original value
            ]);
        });

        it('allows update when total weight remains valid', function () {
            $criterion1 = Criterion::factory()->create(['kode' => 'C1', 'bobot' => 0.6]);
            $criterion2 = Criterion::factory()->create(['kode' => 'C2', 'bobot' => 0.2]);
            // Total = 0.8

            // Update C2 to 0.4 (total = 1.0) ✅
            $this->actingAs($this->admin)
                ->put(route('admin.criteria.update', $criterion2), [
                    'kode' => 'C2',
                    'nama_kriteria' => $criterion2->nama_kriteria,
                    'tipe' => $criterion2->tipe,
                    'bobot' => 0.4,
                ])
                ->assertRedirect(route('admin.criteria.index'))
                ->assertSessionHasNoErrors();

            $this->assertDatabaseHas('criteria', [
                'id' => $criterion2->id,
                'bobot' => 0.4,
            ]);
        });
    });

    describe('View Criterion Details', function () {
        it('displays criteria list with correct total weight', function () {
            Criterion::factory()->create(['kode' => 'C1', 'nama_kriteria' => 'Protein', 'bobot' => 0.4]);
            Criterion::factory()->create(['kode' => 'C2', 'nama_kriteria' => 'Kalori', 'bobot' => 0.3]);
            Criterion::factory()->create(['kode' => 'C3', 'nama_kriteria' => 'Jarak', 'bobot' => 0.3]);

            $response = $this->actingAs($this->admin)
                ->get(route('admin.criteria.index'))
                ->assertOk()
                ->assertViewIs('admin.criteria.index')
                ->assertViewHas('criteria')
                ->assertViewHas('totalBobot');

            // Check total weight
            $totalBobot = $response->viewData('totalBobot');
            expect($totalBobot)->toBe(1.0);
        });
    });
});
