<?php

namespace FiiSoft\Tools\Transaction;

interface TransactionParticipant
{
    /**
     * @return void
     */
    public function transactionStarted();
    
    /**
     * @return void
     */
    public function transactionConfirmed();
    
    /**
     * @return void
     */
    public function transactionCanceled();
}