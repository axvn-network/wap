<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EditApiTypeToTransferLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transfer_logs', function (Blueprint $table) {
            $table->string('platform_type', 50)->nullable()->comment('Nền tảng game');
            $table->integer('addtime')->default(0)->comment('Thời gian thêm');
            $table->integer('settlementsday')->default(0)->comment('Ngày quyết toán');
            $table->string('betid', 50)->nullable()->comment('Mã cược');
            $table->string('remark', 100)->nullable()->comment('Ghi chú');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transfer_logs', function (Blueprint $table) {
            $table->dropColumn(['platform_type', 'addtime', 'settlementsday', 'betid', 'remark']);
        });
    }
}
