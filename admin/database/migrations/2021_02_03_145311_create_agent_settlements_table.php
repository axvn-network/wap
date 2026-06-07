<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgentSettlementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agent_settlements', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->comment('Tên kế hoạch');
            $table->tinyInteger('type')->default(1)->comment('Loại quyết toán 1: Hoàn trả, 2: Hoa hồng');
            $table->decimal('realperson', 6, 2)->default(0.00)->comment('Người thật');
            $table->decimal('electron', 6, 2)->default(0.00)->comment('Điện tử');
            $table->decimal('joker', 6, 2)->default(0.00)->comment('Bài');
            $table->decimal('sport', 6, 2)->default(0.00)->comment('Thể thao');
            $table->decimal('fish', 6, 2)->default(0.00)->comment('Bắn cá');
            $table->decimal('lottery', 6, 2)->default(0.00)->comment('Xổ số');
            $table->decimal('e_sport', 6, 2)->default(0.00)->comment('Thể thao điện tử');
            $table->decimal('member_fs', 8, 2)->default(0.00)->comment('Hoàn trả thành viên');
            $table->tinyInteger('state')->default(1)->comment('Trạng thái');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('agent_settlements');
    }
}
