<?php

use App\Models\User;
use Livewire\Livewire;
use App\Models\Bahan;
use App\Filament\Resources\Bahans\Pages\CreateBahan;
use App\Filament\Resources\Bahans\Pages\ListBahans;


describe("as guest", function () {
    it('can redirects to login if not authenticated', function () {
        $this->get('/admin')
            ->assertRedirect('/admin/login');
    });
});

describe('as admin', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    });

    it('can add new bahan', function () {
        Livewire::test(CreateBahan::class)
            ->fillForm([
                'nama_bahan' => 'Botol',
                'satuan' => 'pcs',
                'min_stok'=> '10',
            ])   
            ->call('create')
            ->assertHasNoFormErrors();

        expect(Bahan::where('nama_bahan', 'Botol')->exists())->toBeTrue();
    });

    it('can show error message if user input is invalid', function () {
        Livewire::test(CreateBahan::class)
            ->fillForm([
                'nama_bahan' => '',
                'satuan' => 'pcs',
                'min_stok'=> 'tes',
            ])
            ->call('create')
            ->assertHasFormErrors(['nama_bahan' => 'required', 'min_stok' => 'numeric']);
    });

    it('can show list bahan', function () {
        Bahan::factory()->create();

        Livewire::test(ListBahans::class)
            ->assertSee('Botol');
    });

    it('can soft delete a bahan', function () {
        $bahan = Bahan::factory()->create();

        $bahan->delete();

        expect(Bahan::count())->toBe(0)->and($bahan->deleted_at)->not()->toBeNull();
    });

    it('cannot add bahan if name is duplicate', function () {
        Bahan::factory()->create();
        
        Livewire::test(CreateBahan::class)
            ->fillForm([
                'nama_bahan'=> 'Botol',
                'satuan' => 'pcs',
                'min_stok' => 10
            ])
            ->call('create')
            ->assertHasFormErrors(['nama_bahan' => 'unique']);
    });

    it('cannot input min stok to negative or zero', function () {
        Livewire::test(CreateBahan::class)
            ->fillForm([
                'nama_bahan'=> 'Botol',
                'satuan' => 'pcs',
                'min_stok' => '0'
            ])
            ->call('create')
            ->assertHasFormErrors(['min_stok']);
    });
});













