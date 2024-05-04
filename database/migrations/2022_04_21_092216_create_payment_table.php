<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            
            $table->bigInteger("payment_item_id")->unsigned();
            $table->bigInteger("member_id")->unsigned()->nullable();
            $table->string('document_number')->nullable();
            $table->date('document_date');
            $table->date("year")->nullable();
            $table->float('quantity')->default(1);
            $table->float('value')->default(0);
            $table->float('total')->default(0);
            $table->boolean('bank_id')->unsigned()->nullable();
            $table->string('currency')->default('EUR');
            $table->string('remarks')->nullable();
            $table->string('status')->default('draft');
            $table->timestamps();
            $table->bigInteger('created_by')->unsigned()->nullable();
            $table->bigInteger('updated_by')->unsigned()->nullable();
        });
        Schema::table('payments', function($table) {
            $table->index(['member_id']);
            $table->foreign('member_id')->references('id')->on('members');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment');
    }
}
