<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Pembeda admin dan pengguna biasa. Sebelumnya siapa pun yang mendaftar lewat
     * /api/register bisa langsung mengelola produk; sekarang hanya is_admin = true.
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_admin')->default(false)->after('password');
        });

        // Akun admin bawaan seeder dijadikan admin (aman dijalankan di database yang sudah berisi data)
        DB::table('users')->where('email', 'admin@example.com')->update(['is_admin' => true]);
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_admin');
        });
    }
};
