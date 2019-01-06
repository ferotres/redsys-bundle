<?php

namespace Ferotres\RedsysBundle\Redsys\Helper;

/**
 * Class RedsysHelper
 * @package CoreBiz\Redsys\Helper
 */
final class RedsysHelper
{
    /**
     * @param $data
     * @param $key
     * @return string
     */
    private function encrypt_3DES_SSL(string $data, string $key)
    {
        $iv = "\0\0\0\0\0\0\0\0";
        $data_padded = $data;
        if (strlen($data_padded) % 8) {
            $data_padded = str_pad($data_padded, strlen($data_padded) + 8 - strlen($data_padded) % 8, "\0");
        }
        $ciphertext = openssl_encrypt($data_padded, "DES-EDE3-CBC", $key, OPENSSL_RAW_DATA | OPENSSL_NO_PADDING, $iv);
        return $ciphertext;
    }

    /**
     * @return string
     */
    public function arrayToJson($data)
    {
        $json = json_encode($data);
        return $json;
    }

    /**
     * @return string
     */
    public function createMerchantParameters(array $operation)
    {
        // Se transforma el array de datos en un objeto Json
        $json = $this->arrayToJson($operation);
        // Se codifican los datos Base64
        return $this->encodeBase64($json);
    }

    /**
     * @param $input
     * @return string
     */
    public function base64_url_encode($input)
    {
        return strtr(base64_encode($input), '+/', '-_');
    }

    /**
     * @param $data
     * @return string
     */
    public function encodeBase64($data)
    {
        $data = base64_encode($data);
        return $data;
    }

    /**
     * @param $input
     * @return bool|string
     */
    public function base64_url_decode($input)
    {
        return base64_decode(strtr($input, '-_', '+/'));
    }

    /**
     * @param $data
     * @return bool|string
     */
    public function decodeBase64($data)
    {
        $data = base64_decode($data);
        return $data;
    }

    /**
     * @param $ent
     * @param $key
     * @return string
     */
    public function mac256($ent,$key)
    {
        $res = hash_hmac('sha256', $ent, $key, true);
        return $res;
    }

    /**
     * @param string $secret
     * @param string $data
     * @return string
     */
    function createSignature(string $secret, string $data, string $order)
    {
        $key = $this->decodeBase64($secret);
        $key = $this->encrypt_3DES_SSL($order, $key);
        $res = $this->mac256($data, $key);
        return $this->encodeBase64($res);
    }

}