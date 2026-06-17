<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kuda', function (Blueprint $table) {
            $table->enum('gender', ['jantan', 'betina'])
                ->default('jantan')
                ->after('jenis_kuda');
        });
    }

    public function down(): void
    {
        Schema::table('kuda', function (Blueprint $table) {
            $table->dropColumn('gender');
        });
    }
};
