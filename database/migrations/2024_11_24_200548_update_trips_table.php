<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        Schema::table('trips', function (Blueprint $table) {
            // Drop the existing foreign key constraint and user_id column
            // $table->dropForeign(['user_id']); // Drop foreign key constraint
            $table->dropColumn('user_id'); // Drop user_id column
        });
        Schema::table('trips', function (Blueprint $table) {
            // Drop the old columns and add foreign keys
            $table->foreignId('user_id')->nullable()->constrained('users', 'id')->onDelete('set null')->after('distance');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            // Drop the foreign key and the column
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
