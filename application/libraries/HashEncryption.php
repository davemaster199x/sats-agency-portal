<?php
    
use Hashids\Hashids;

class HashEncryption extends Hashids {
    public function __construct($salt = '', $minLength = 0, $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890') {
        parent::__construct($salt, $minLength, $alphabet);
        log_message('Info', 'HashEncryption Class Initialized');
    }
    
    /**
     * Encode String
     * @param ...$numbers
     * @return string
     */
    public function encodeString(...$numbers): string {
        return $this->encode($numbers);
    }
    
    /**
     * Decode String
     * @param $hash
     * @return string
     */
    public function decodeString($hash): string {
        $decoded = $this->decode($hash);
        return $decoded ? $decoded[0] : '';
    }
}
