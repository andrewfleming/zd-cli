<?php

namespace App\Server;

class HTTP {
    private const RESPONSE_OK = "HTTP/1.0 200 OK\r\n" . "Content-Type: text/plain\r\n" . "\r\n" . "Ok. You may close this tab and return to the shell.\r\n";
    private const RESPONSE_ERROR = "HTTP/1.0 400 Bad Request\r\n" . "Content-Type: text/plain\r\n" . "\r\n" . "Bad Request\r\n";

    public function __construct() {
        ini_set( 'default_socket_timeout', 60 * 5 );
    }

    public function server( $socketStr ) {
        $server = stream_socket_server( $socketStr, $errno, $errstr );

        if ( ! $server ) {
            error_log( 'Error starting HTTP server' );

            return false;
        }
        return $server;
    }

    public function accept( $server ) {
        do {
            $sock = stream_socket_accept( $server );

            if ( ! $sock ) {
                exit( 1 );
            }

            $headers        = [];
            $body           = null;
            $content_length = 0;
            //read request headers
            while ( false !== ( $line = trim( fgets( $sock ) ) ) ) {

                if ( '' === $line ) {
                    break;
                }

                $regex = '#^Content-Length:\s*([[:digit:]]+)\s*$#i';

                if ( preg_match( $regex, $line, $matches ) ) {
                    $content_length = (int) $matches[1];
                }

                $headers[] = $line;
            }
            // read content/body
            if ( $content_length > 0 ) {
                $body = fread( $sock, $content_length );
            }
            // send response
            list( $method, $url, $httpver ) = explode( ' ', $headers[0] );

            if ( $method == 'GET' ) {
                $parts = parse_url( $url );

                if ( isset( $parts['path'] ) && $parts['path'] == '/authorization-code/callback' && isset( $parts['query'] ) ) {
                    parse_str( $parts['query'], $query );

                    if ( isset( $query['code'] ) && isset( $query['state'] ) ) {
                        fwrite( $sock, self::RESPONSE_OK );
                        fclose( $sock );

                        return $query;
                    }
                }
            }

            fwrite( $sock, self::RESPONSE_ERROR );
            fclose( $sock );
        } while ( true );
    }
}
