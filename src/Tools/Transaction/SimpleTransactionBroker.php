<?php

namespace FiiSoft\Tools\Transaction;

use RuntimeException;
use SplObjectStorage;

/**
 * Instances of this class can be created by operator new and by method getInstance,
 * in which case the singleton is returned.
 */
final class SimpleTransactionBroker implements TransactionBroker
{
    /** @var TransactionBroker */
    private static $instance;
    
    /** @var SplObjectStorage|TransactionParticipant[] */
    private $participants;
    
    /**
     * The participant which started the transaction and is only able to commit or rollback it.
     *
     * @var TransactionParticipant
     */
    private $activist;
    
    /**
     * @return TransactionBroker singleton
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        
        return self::$instance;
    }
    
    public function __construct()
    {
        $this->participants = new SplObjectStorage();
    }
    
    /**
     * @param TransactionParticipant $participant
     * @return void
     */
    public function registerParticipant(TransactionParticipant $participant)
    {
        if (!$this->participants->contains($participant)) {
            $this->participants->attach($participant);
            if ($this->activist !== null) {
                $participant->transactionStarted();
            }
        }
    }
    
    /**
     * @return bool
     */
    public function isTransactionActive()
    {
        return $this->activist !== null;
    }
    
    /**
     * @param TransactionParticipant $activist
     * @throws RuntimeException
     * @return void
     */
    public function beginTransaction(TransactionParticipant $activist)
    {
        if ($this->activist !== null) {
            throw new RuntimeException('Transaction has already started and cannot be started again');
        }
        
        $this->activist = $activist;
        $this->activist->transactionStarted();
        
        foreach ($this->participants as $participant) {
            if ($participant !== $activist) {
                $participant->transactionStarted();
            }
        }
    }
    
    /**
     * @param TransactionParticipant $activist
     * @throws RuntimeException
     * @return void
     */
    public function commitTransaction(TransactionParticipant $activist)
    {
        if ($this->activist === null) {
            return;
        }
        
        if ($this->activist !== $activist) {
            throw new RuntimeException('Provided object has not begun the transaction and cannot commit it');
        }
        
        $this->activist->transactionConfirmed();
        
        foreach ($this->participants as $participant) {
            if ($participant !== $activist) {
                $participant->transactionConfirmed();
            }
        }
        
        $this->activist = null;
    }
    
    /**
     * @param TransactionParticipant $activist
     * @throws RuntimeException
     * @return void
     */
    public function rollbackTransaction(TransactionParticipant $activist)
    {
        if ($this->activist === null) {
            return;
        }
        
        if ($this->activist !== $activist) {
            throw new RuntimeException('Provided object has not begun the transaction and cannot rollback it');
        }
        
        $this->activist->transactionCanceled();
        
        foreach ($this->participants as $participant) {
            if ($participant !== $activist) {
                $participant->transactionCanceled();
            }
        }
        
        $this->activist = null;
    }
}