<?php

namespace App\Services;

class EncryptionService
{
    protected $password; // Kita ganti nama variabel dari 'kunci' menjadi 'password' agar lebih jelas
    protected $data;
    protected $method;

    public function __construct($password = null, $bit = '128')
    {
        if ($password) {
            $this->set_password($password);
        }
        $this->set_bit($bit);
    }

    public function set_password($password)
    {
        $this->password = $password;
    }

    public function set_bit($bit)
    {
        if ($bit == '128') {
            $this->method = 'aes-128-cbc';
        } else {
            $this->method = 'aes-256-cbc';
        }
    }

    public function setData($data)
    {
        $this->data = $data;
    }

    public function encrypt()
    {
        // 1. Buat Salt acak yang unik (16 bytes)
        $salt = random_bytes(16);

        // 2. Buat kunci turunan (derived key) dari password dan salt menggunakan PBKDF2
        $keyLength = ($this->method == 'aes-128-cbc') ? 16 : 32;
        $derivedKey = hash_pbkdf2('sha256', $this->password, $salt, 100000, $keyLength, true);

        // 3. Buat IV acak (16 bytes)
        $iv = random_bytes(16);

        // 4. Enkripsi data menggunakan kunci turunan (derivedKey)
        $encryptedData = openssl_encrypt($this->data, $this->method, $derivedKey, OPENSSL_RAW_DATA, $iv);

        // 5. Gabungkan: salt + IV + ciphertext. Lalu encode dengan Base64.
        return base64_encode($salt . $iv . $encryptedData);
    }

    public function decrypt()
    {
        $data = base64_decode($this->data);

        // 1. Ekstrak Salt dari 16 byte pertama
        $salt = substr($data, 0, 16);

        // 2. Ekstrak IV dari 16 byte berikutnya
        $iv = substr($data, 16, 16);

        // 3. Ekstrak data terenkripsi (sisa dari data)
        $encryptedData = substr($data, 32);

        // 4. Buat kembali kunci turunan yang sama persis menggunakan password dan salt yang diekstrak
        $keyLength = ($this->method == 'aes-128-cbc') ? 16 : 32;
        $derivedKey = hash_pbkdf2('sha256', $this->password, $salt, 100000, $keyLength, true);

        // 5. Dekripsi data menggunakan kunci turunan dan IV
        return openssl_decrypt($encryptedData, $this->method, $derivedKey, OPENSSL_RAW_DATA, $iv);
    }
}
