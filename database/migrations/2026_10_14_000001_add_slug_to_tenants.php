<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', fn (Blueprint $table) => $table->string('slug', 160)->nullable());
        DB::table('tenants')->orderBy('id')->chunkById(100, function ($tenants) {
            foreach ($tenants as $tenant) {
                $base = rtrim(Str::limit(Str::slug($tenant->name), 120, ''), '-') ?: 'store';
                $slug = $base;
                if (DB::table('tenants')->where('slug', $slug)->exists()) {
                    $slug = $base.'-'.$tenant->id;
                }
                while (DB::table('tenants')->where('slug', $slug)->exists()) {
                    $slug .= '-'.Str::lower(Str::random(4));
                }
                DB::table('tenants')->where('id', $tenant->id)->update(['slug' => $slug]);
            }
        });
        Schema::table('tenants', fn (Blueprint $table) => $table->unique('slug'));
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn('slug');
        });
    }
};
