<?php
/**
 * Created by PhpStorm.
 * User: angga
 * Date: 22/11/18
 * Time: 1:39
 */

/**
 * Class MustAuthenticated
 * @property UserTokenModel $userToken;
 * @property UserModel $user;
 * @property CI_Session $session;
 * @property CI_Loader $load;
 */
class MustAuthenticated
{
    private $loginPage = 'auth/login';

    private $mustLogin = ['*'];

    private $allowGuest = [
		Account::class, Client_area::class, Cycle_count::class, Opname::class, Help::class, Cargo_manifest::class,
        Migrate::class, Automate::class, Response::class, Cash_bond::class, Monitoring::class, Inbound_progress::class, Booking_rating_public::class,
        Payment_check::class, Webhook::class, Discrepancy_handover_confirmation::class
    ];

    public function __construct()
    {
        $CI = get_instance();
        if ($CI->config->item('sso_enable')) {
            $this->loginPage = sso_url('auth/login');
        } else {
            array_push($this->allowGuest, Welcome::class);
            $this->loginPage = site_url('welcome');
        }
    }

    /**
     * Check if user is authenticated.
     */
    public function checkAuth()
    {
        if ($this->needAuthenticated()) {
            if (!UserModel::isLoggedIn()) {
                $rememberToken = get_cookie('remember_token');

                if (empty($rememberToken)) {
                    $redirectTo = '?redirect=' . urlencode(current_url());

                    redirect($this->loginPage . $redirectTo, false);
                }
                $this->loginWithCookie($rememberToken, $this->loginPage);
            } else {
                $CI = get_instance();
                if ($CI->config->item('sso_enable')) {
                    if (!AuthorizationModel::hasApplicationAccess()) {
                        redirect(sso_url('app'), false);
                    }
                }
            }
        }
    }

    /**
     * Login with remember token.
     *
     * @param $rememberToken
     * @param $loginUrl
     */
    private function loginWithCookie($rememberToken, $loginUrl)
    {
        $CI = get_instance();
        $CI->load->model('UserTokenModel', 'userToken');
        $CI->load->model('UserModel', 'user');

        $email = $CI->userToken->verifyToken($rememberToken, UserTokenModel::$TOKEN_REMEMBER);
        if ($email === false) {
            $user = null;
        } else {
            $user = $CI->user->getUserByEmail($email);
        }
        if (empty($user)) {
            redirect($loginUrl);
        } else {
            $CI->session->set_userdata([
                'auth.id' => $user['id'],
                'auth.is_logged_in' => true,
                'auth.remember_me' => true,
                'auth.remember_token' => $rememberToken
            ]);
        }
    }

    /**
     * Find out if we need to check user session or not.
     *
     * @return bool
     */
    private function needAuthenticated()
    {
        $controller = get_class(get_instance());

        $mustAuthenticated = $this->mustLogin[0] == '*';

        if ($mustAuthenticated) {
            // all controller need to be authenticated,
            // but exclude guest's controller (the blacklist controller)
            foreach ($this->allowGuest as $guestController) {
                if ($controller == $guestController) {
                    $mustAuthenticated = false;
                    break;
                }
            }
        } else {
            // we specify whitelist of which controllers that we need to check
            // if user need to be authenticated
            foreach ($this->mustLogin as $restrictedController) {
                if ($controller == $restrictedController) {
                    $mustAuthenticated = true;
                    break;
                }
            }
        }

        return $mustAuthenticated;
    }
}
