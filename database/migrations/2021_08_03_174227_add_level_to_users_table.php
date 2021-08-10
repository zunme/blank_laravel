<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLevelToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedSmallInteger('user_level')->default(100)->comment('권한')->after('email');
            $table->string('tel',20)->comment('전화번호')->after('user_level');
            $table->string('national',50)->comment('국가')->after('tel');
            $table->string('rcmnd_code',50)->comment('추천코드')->after('national');
            $table->unsignedBigInteger('parent_id')->comment('추천인')->after('rcmnd_code');
            $table->unsignedDecimal('points', $precision = 10, $scale = 2)->comment('points')->after('parent_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
            $table->dropColumn('user_level');
            $table->dropColumn('tel');
            $table->dropColumn('national');
            $table->dropColumn('rcmnd_code');
            $table->dropColumn('parent_id');
            $table->dropColumn('points');
        });
    }
}
