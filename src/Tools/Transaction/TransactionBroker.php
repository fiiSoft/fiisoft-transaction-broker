<?php

namespace FiiSoft\Tools\Transaction;

use RuntimeException;

interface TransactionBroker
{
    /**
     * @param TransactionParticipant $participant
     * @return void
     */
    public function registerParticipant(TransactionParticipant $participant);
    
    /**
     * @return bool
     */
    public function isTransactionActive();
    
    /**
     * @param TransactionParticipant $activist
     * @throws RuntimeException on error
     * @return void
     */
    public function beginTransaction(TransactionParticipant $activist);
    
    /**
     * @param TransactionParticipant $activist
     * @throws RuntimeException on error
     * @return void
     */
    public function commitTransaction(TransactionParticipant $activist);
    
    /**
     * @param TransactionParticipant $activist
     * @throws RuntimeException on error
     * @return void
     */
    public function rollbackTransaction(TransactionParticipant $activist);
}