<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class LoanApplicationTableUpdate extends Migration
{
    public function up()
    {
        Schema::table('loan_applications', function (Blueprint $table){
            $table->enum('overdue', ['yes', 'no'])->default('no');
            $table->decimal('penalty', 10, 2)->nullable();
        });
    }

}
