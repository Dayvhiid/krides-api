<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up() {
    Schema::table('users', function (Blueprint $table) {
        $table->string('bank_name')->nullable();       // Human readable bank name (e.g. Access Bank)
        $table->string('bank_code')->nullable();       // Flutterwave bank code (e.g. "044")
        $table->string('account_number')->nullable();  // Driver’s bank account number
        $table->string('account_name')->nullable();    // Optional: verified account holder’s name
    });
}

public function down() {
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn(['bank_name', 'bank_code', 'account_number', 'account_name']);
    });
}

};
