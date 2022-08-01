<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migrate extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('migration');
    }

    /**
     * Run migration to latest version.
     */
    public function index()
    {
        $this->load->library('migration');

        if ($this->migration->latest() === FALSE) {
            show_error($this->migration->error_string());
        } else {
            echo 'Migration complete.' . PHP_EOL;
        }
    }

    /**
     * Run migration to specific version.
     * @param $target_version
     */
    public function to($target_version = null)
    {
        if(is_null($target_version)){
            echo 'Missing argument version migration.';
        } else {
            if ($this->migration->version($target_version) === FALSE) {
                show_error($this->migration->error_string());
            } else {
                echo 'Migration to version ' . $target_version . ' complete.' . PHP_EOL;
            }
        }
    }

    /**
     * Rollback migration version.
     */
    public function rollback()
    {
        if ($this->migration->version(0) === FALSE) {
            show_error($this->migration->error_string());
        } else {
            echo 'Rollback database complete.' . PHP_EOL;
        }
    }

    /**
     * Rollback and migrate database.
     */
    public function reset()
    {
        $this->rollback();
        $this->index();
    }
}