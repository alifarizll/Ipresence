<?php

namespace App\Utilities;

class Pbkdf2Hash
{
    const DEF_ALGO = 'sha256';
    const DEF_ITER = 27500;
    const DEF_PASS_LEN = 8;

    protected $pass;
    protected $algo;
    protected $iter;
    protected $salt;
    protected $hash;

    public function __construct($pass = false, $algo = self::DEF_ALGO, $iter = self::DEF_ITER) {
        $this->pass = $pass === false ? self::createPass() : $pass;
        $hash = self::hash($this->pass, null, $algo, $iter);
        $this->algo = $hash[0];
        $this->iter = $hash[1];
        $this->salt = $hash[2];
        $this->hash = $hash[3];
    }

    static public function randStr($len, $chars = 'A-Za-z0-9\s')
    {
        $chars = str_replace(
            array('A-Z', 'a-z', '0-9', '\s', 'a-f'),
            array('ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz', '0123456789', '!"#$%&\'()*+,-./:;<=>?@[\]^_`{|}~', 'abcdef'),
            $chars
        );
        $charsLen = strlen($chars) - 1;
        $str = "";
        for ($i = 0; $i < $len; $i++) $str .= $chars[mt_rand(0, $charsLen)];
        return $str;
    }

    static public function createPass($len = 8, $chars = 'A-Za-z0-9\s')
    {
        return self::randStr($len, $chars);
    }

    static public function hash($pass, $salt=null, $algo = self::DEF_ALGO, $iter = self::DEF_ITER)
    {
        if (strlen($pass) < self::DEF_PASS_LEN) {
            trigger_error('PBKDF2 ERROR: Password parameter at least 8 character.', E_USER_ERROR);
        }
        return array(
            $algo,
            $iter,
            $salt = isset($salt) ? $salt : self::randStr(32, '0-9a-f'),
            $hash = self::pbkdf2($pass, $salt, $algo, $iter)
        );
    }

    static public function pbkdf2($pass, $salt, $algo = self::DEF_ALGO, $iter = self::DEF_ITER) {
        if (!function_exists("hash_pbkdf2")) {
            trigger_error('PHP ERROR: PBKDF2 hashing does not supported.', E_USER_ERROR);
        }
        if (!in_array($algo, hash_algos(), true)) {
            trigger_error('PBKDF2 ERROR: Invalid hash algorithm. ('.$algo.')', E_USER_ERROR);
        }
        if ($iter <= 0) {
            trigger_error('PBKDF2 ERROR: Invalid iteration parameter.', E_USER_ERROR);
        }
        return base64_encode(hash_pbkdf2($algo, $pass, $salt, $iter, 0, true));
    }

    static public function check($pass, $hash, $salt, $algo = self::DEF_ALGO, $iter = self::DEF_ITER)
    {
        $old_pass = base64_decode($hash);
        $new_pass = base64_decode(self::pbkdf2($pass, $salt, $algo, $iter));
        return $old_pass === $new_pass;
    }

    // Get the password in plain text
    public function getPass()
    {
        return $this->pass;
    }

    // Get the algorithm used to generate the hash
    public function getAlgo()
    {
        return $this->algo;
    }

    // Get the 2^n power for the number of iteratinos
    public function getIter()
    {
        return $this->iter;
    }

    // Get the salt used to generate the hash
    public function getSalt()
    {
        return $this->salt;
    }

    // Get the hash
    public function getHash()
    {
        return $this->hash;
    }

}
