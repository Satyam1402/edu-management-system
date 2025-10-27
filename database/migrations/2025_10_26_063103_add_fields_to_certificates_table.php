<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('certificates', function (Blueprint $table) {
        $table->string('title')->nullable()->after('student_id');
        $table->text('description')->nullable()->after('title');
        $table->date('issued_date')->nullable()->after('description');
    });
}
public function down()
{
    Schema::table('certificates', function (Blueprint $table) {
        $table->dropColumn(['title', 'description', 'issued_date']);
    });
}

};
