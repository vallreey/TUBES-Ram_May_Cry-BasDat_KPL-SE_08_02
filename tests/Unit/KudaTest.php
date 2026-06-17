<?php

namespace Tests\Unit;

use App\Models\Kuda;
use Tests\TestCase;

class KudaTest extends TestCase
{
    public function test_status_tersedia_constant()
    {
        $this->assertEquals(
            'tersedia',
            Kuda::STATUS_TERSEDIA
        );
    }

    public function test_status_terjual_constant()
    {
        $this->assertEquals(
            'terjual',
            Kuda::STATUS_TERJUAL
        );
    }

    public function test_status_breeding_constant()
    {
        $this->assertEquals(
            'breeding',
            Kuda::STATUS_BREEDING
        );
    }

    public function test_gender_jantan_constant()
    {
        $this->assertEquals(
            'jantan',
            Kuda::GENDER_JANTAN
        );
    }

    public function test_gender_betina_constant()
    {
        $this->assertEquals(
            'betina',
            Kuda::GENDER_BETINA
        );
    }

    public function test_fillable_contains_nama_kuda()
    {
        $kuda = new Kuda();

        $this->assertContains(
            'nama_kuda',
            $kuda->getFillable()
        );
    }
}