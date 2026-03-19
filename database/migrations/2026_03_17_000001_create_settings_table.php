<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Seed default settings
        $defaults = [
            ['key' => 'store_name',       'value' => 'KasirKu'],
            ['key' => 'store_address',    'value' => ''],
            ['key' => 'store_phone',      'value' => ''],
            ['key' => 'receipt_message',  'value' => 'Terima kasih sudah berbelanja!'],
            ['key' => 'tax_enabled',      'value' => '1'],
            ['key' => 'tax_rate',         'value' => '10'],
            ['key' => 'theme',            'value' => 'dark'],
        ];

        foreach ($defaults as $row) {
            \Illuminate\Support\Facades\DB::table('settings')->insert(
                array_merge($row, ['created_at' => now(), 'updated_at' => now()])
            );
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
