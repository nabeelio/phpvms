<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJournalTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('journal_transactions', function (Blueprint $table) {
            $table->char('id', 36)->unique();
            $table->char('transaction_group', 36)->nullable();
            $table->integer('journal_id');
            $table->unsignedBigInteger('credit')->nullable();
            $table->unsignedBigInteger('debit')->nullable();
            $table->char('currency', 5);
            $table->text('memo')->nullable();
            $table->char('ref_class', 32)->nullable();
            $table->text('ref_class_id')->nullable();
            $table->timestamps();
            $table->dateTime('post_date');
            $table->softDeletes();

            $table->index('journal_id');
            $table->index('transaction_group');
            $table->index(['ref_class', 'ref_class_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('journal_transactions');
    }
}
