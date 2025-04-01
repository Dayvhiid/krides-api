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
        // Schema::table('trips', function (Blueprint $table) {
        //     // Drop the foreign key constraint
        //     $table->dropForeign(['user_id']);
        // });

        // Schema::table('trips', function (Blueprint $table) {
        //     // Drop the user_id column after the foreign key is removed
        //     $table->dropColumn('user_id');
        // });

        Schema::table('trips', function (Blueprint $table) {
            // Add the new foreign key column with the updated constraint
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