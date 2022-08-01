<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class SettingModel extends CI_Model
{
    private $table = 'ref_settings';
    public static $settings = [];

    /**
     * SettingModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Retrieve all configuration keys.
     */
    public function getAllSettings()
    {
        if(empty(self::$settings)) {
            $settings = $this->db->get($this->table)->result_array();
            $dataSettings = [];
            foreach ($settings as $data) {
                $dataSettings[$data['key']] = $data['value'];
            }
            self::$settings = $dataSettings;
        }
        return self::$settings;
    }

    /**
     * Get single setting key
     * @param string $key
     * @param string $default
     * @return string
     */
    public function getSetting($key, $default = '')
    {
        $settings = $this->getAllSettings();
        if (key_exists($key, $settings)) {
            return $settings[$key];
        }
        return $default;
    }

    /**
     * Update single setting data.
     * @param string $key
     * @param string $value
     */
    public function updateSetting($key, $value)
    {
        $this->db->update($this->table, ['value' => $value, 'created_by' => UserModel::authenticatedUserData('id')], ['key' => $key]);
    }

    /**
     * Update all settings at once.
     * @param $settings
     * @return bool
     */
    public function updateSettings($settings)
    {
        $this->db->trans_start();
        foreach ($settings as $key => $value) {
            $this->updateSetting($key, $value);
        }
        $this->db->trans_complete();
        return $this->db->trans_status();
    }

}