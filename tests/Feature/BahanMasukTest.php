<?php

use App\Filament\Resources\BahanMasuks\BahanMasukResource;
use App\Filament\Resources\BahanMasuks\Pages\CreateBahanMasuk;
use App\Filament\Resources\BahanMasuks\Pages\ListBahanMasuks;
use App\Models\Bahan;
use App\Models\BahanMasuk;
use App\Models\BahanMasukItem;
use App\Models\User;
use Livewire\Livewire;  


describe("as guest", function () {
    it("can redirect to login page if user not authenticated", function () {
        $response = $this->get(BahanMasukResource::getUrl('index'));

        $response->assertRedirect('/admin/login');
    });
});

describe('as admin', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    });

    it('can add new bahan masuk', function () {
        $bahan = Bahan::factory()->create();

        $test = Livewire::test(CreateBahanMasuk::class)
            ->set('data.tanggal', '2026-02-26')
            ->set('data.nama_supplier', 'test')
            ->set('data.keterangan', 'oke')
            ->set('data.items', [
                [
                    'bahan_id' => $bahan->id,
                    'jumlah' => 10,
                ],
            ]);

        $test->call('create');

        $this->assertDatabaseHas('bahan_masuk', ['tanggal' => '2026-02-26', 'user_id' => $this->user->id]);

        $this->assertDatabaseHas('bahan_masuk_items', ['bahan_id' => $bahan->id, 'jumlah' => 10]);
    });

    it('can show list bahan masuk', function () {
        $this->bahan = BahanMasuk::factory()->create();

        $bahanMasuk = BahanMasuk::factory()->has(BahanMasukItem::factory()->count(50)->state(['jumlah' => 10]), 'items')->create();

        $response = $this->get(BahanMasukResource::getUrl('index'));

        $response->assertStatus(200);

        Livewire::test(ListBahanMasuks::class)
            ->assertSee('500');
    });

    it('can show validation error if input invalid', function () {
        $bahan = Bahan::factory()->create();

        $test = Livewire::test(CreateBahanMasuk::class)
            ->set('data.tanggal', '')
            ->set('data.nama_supplier', '')
            ->set('data.keterangan', '')
            ->set('data.items', [
                [
                    'bahan_id' => null,
                    'jumlah' => 'hai',
                ],
            ]);

        $test->call('create')->assertHasFormErrors(['tanggal' => 'required', 'nama_supplier' => 'required', 'keterangan' => 'required', 'items.0.bahan_id' => 'required', 'items.0.jumlah' => 'numeric']);
    });

    it('can shows latest date transaction', function () {
        BahanMasuk::factory()->count(5)->create();

        $new = BahanMasuk::latest('tanggal')->first();

        Livewire::test(ListBahanMasuks::class)
            ->assertTableColumnStateSet('nama_supplier', $new->nama_supplier, record: $new);
    });

    it('cannot input jumlah to negative or zero', function () {
        $bahan = Bahan::factory()->create();

        $test = Livewire::test(CreateBahanMasuk::class)
            ->set('data.tanggal', '2026-02-20')
            ->set('data.nama_supplier', 'cihuy')
            ->set('data.keterangan', 'test')
            ->set('data.items', [
                [
                    'bahan_id' => $bahan->id,
                    'jumlah' => '-10',
                ],
            ]);

        $test->call('create')
            ->assertHasFormErrors(['items.0.jumlah']);
    });

    it('cannot input past date', function () {
        $bahan = Bahan::factory()->create();

        $test = Livewire::test(CreateBahanMasuk::class)
            ->set('data.tanggal', '2026-03-09')
            ->set('data.nama_supplier', 'cihuy')
            ->set('data.keterangan', 'test')
            ->set('data.items', [
                [
                    'bahan_id' => $bahan->id,
                    'jumlah' => '10',
                ],
            ]);

        $test->call('create')
            ->assertHasFormErrors(['tanggal']);
    });

});