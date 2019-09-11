<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/25 0025
 * Time: 下午 1:16
 */

namespace system;


use system\config\config;

class encrypt
{
    //此处填写前后端共同约定的秘钥

    /**
     * 加密
     * @param string $str    要加密的数据
     * @return bool|string   加密后的数据
     */
     public static function aes_encrypt($str) {

        $data = openssl_encrypt($str, 'AES-128-ECB', config::encrypt()["aes_key"], OPENSSL_RAW_DATA);
        $data = base64_encode($data);

        return $data;
    }
     public static function aes_decrypt($str) {
        $decrypted = openssl_decrypt(base64_decode($str), 'AES-128-ECB', config::encrypt()["aes_key"], OPENSSL_RAW_DATA);
        return $decrypted;
    }
    public static function rsa_encrypt($str) {
        $encrypted = '';
        $pu_key = openssl_pkey_get_public(config::encrypt()["rsa_public"]);
        openssl_public_encrypt($str, $encrypted, $pu_key);//公钥加密

        $encrypted = base64_encode($encrypted);

        return $encrypted;
    }

    /**
     * 解密
     * @param string $str 要解密的数据
     * @param string $private_key 私钥
     * @return string
     */
    static public function ras_decrypt($str) {
        $decrypted = '';
        $pi_key =  openssl_pkey_get_private(config::encrypt()["rsa_private"]);
        openssl_private_decrypt(base64_decode($str), $decrypted, $pi_key);//私钥解密
        return $decrypted;
    }
}