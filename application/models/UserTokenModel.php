<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UserTokenModel extends MY_Model
{
    protected $table = 'prv_user_tokens';

    public static $TOKEN_REMEMBER = 'REMEMBER';
    public static $TOKEN_PASSWORD = 'PASSWORD';
    public static $TOKEN_REGISTRATION = 'REGISTRATION';
    public static $TOKEN_CONFIRMATION = 'CONFIRMATION';
    public static $TOKEN_RATING = 'RATING';

    public function __construct()
    {
        if ($this->config->item('sso_enable')) {
            $this->table = env('DB_SSO_DATABASE') . '.prv_user_tokens';
        }
    }

    /**
     * Generate token for one authenticate of credential of several
     * actions such as registration or reset password.
     * @param $email
     * @param string $tokenType
     * @param int $length
     * @param int $maxActivation
     * @param null $expired_at
     * @param bool $tokenUpdate
     * @return bool|string
     */
    public function createToken($email, $tokenType = 'REGISTRATION', $length = 32, $maxActivation = 1, $expired_at = null, $tokenUpdate = true)
    {

        $this->load->helper('string');
        $token = random_string('alnum', $length);

        $isTokenEmailExist = $this->db->get_where($this->table, [
            'email' => $email,
            'type' => $tokenType
        ])->num_rows();

        if ($isTokenEmailExist && $tokenUpdate) {
            $result = $this->db->update($this->table, [
                'token' => $token,
                'max_activation' => $maxActivation,
                'expired_at' => $expired_at,
            ], [
                'email' => $email,
                'type' => $tokenType
            ]);
        } else {
            $result = $this->db->insert($this->table, [
                'email' => $email,
                'token' => $token,
                'type' => $tokenType,
                'max_activation' => $maxActivation,
                'expired_at' => $expired_at,
            ]);
        }

        if ($result) {
            return $token;
        }
        return false;
    }

    /**
     * Check if given token is valid.
     * @param string $token
     * @param string $tokenType
     * @return bool|string
     */
    public function verifyToken($token, $tokenType)
    {
        $token = $this->db->get_where($this->table, [
            'token' => $token,
            'type' => $tokenType
        ]);

        if ($token->num_rows()) {
            $tokenData = $token->row_array();
            return $tokenData['email'];
        }

        return false;
    }

    /**
     * @param $token
     * @param bool $checkActivation
     * @param bool $checkExpiredDate
     * @return array
     */
    public function getUserTokenByTokenKey($token, $checkActivation = false, $checkExpiredDate = false)
    {
        $userToken = $this->db->from($this->table)
            ->where('token', $token);

        if ($checkActivation) {
            $userToken->where('max_activation >', 0);
        }

        if ($checkExpiredDate) {
            $userToken->where('expired_at >= DATE(NOW())');
        }

        return $userToken->get()->row_array();
    }

    /**
     * Get user token by email.
     * @param $email
     * @return mixed
     */
    public function getUserTokenByEmail($email)
    {
        return $this->db->from($this->table)
            ->where('email', $email)
            ->get()->result_array();
    }

    /**
     * Activate token use, decrease token max activation value.
     * @param $token
     * @return mixed
     */
    public function activateToken($token)
    {
        $tokenData = $this->getUserTokenByTokenKey($token, true);

        if (empty($tokenData)) {
            return false;
        }

        $result = $this->db->update($this->table, [
            'max_activation' => $tokenData['max_activation'] - 1,
        ], [
            'token' => $token
        ]);

        return $result;
    }

    /**
     * Delete token by its token.
     *
     * @param $token
     * @return bool
     */
    public function deleteToken($token)
    {
        return $this->delete(['token' => $token]);
    }

}