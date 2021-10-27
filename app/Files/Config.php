<?php

namespace App\Files;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Encryption\Encrypter;

class Config {
    public const CONFIG_FILE_NAME = '.zd-cli';
    private $encrypter;

    public function __construct( Encrypter $encrypter ) {
        $this->encrypter = $encrypter;
    }

    public function read_file() {
        $string = file_get_contents( $this->file_path() );

        try {
            $decrypted_string = $this->encrypter->decryptString( $string );
        } catch ( DecryptException $e ) {
            return $e;
        }

        return json_decode( $decrypted_string, true );
    }

    public function write_file( $config ) {
        $string = json_encode( $config );
        $encypted_string = $this->encrypter->encryptString( $string );
        return file_put_contents( $this->file_path(), $encypted_string );
    }

    private function get_home_dir() {
        return $_SERVER['HOME'];
    }

    private function file_path() {
        return $this->get_home_dir() . DIRECTORY_SEPARATOR . self::CONFIG_FILE_NAME;
    }

    private function validate_config( $vars ) {
        $args = array(
            'subdomain' => FILTER_VALIDATE_DOMAIN,
            'client_id',
            'client_secret'
        );
    }
}
