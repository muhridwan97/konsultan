<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ComplainCategoryModel extends MY_Model
{
    protected $table = 'ref_complain_categories';

    const TYPE_MAJOR = 'MAJOR';
    const TYPE_MINOR = 'MINOR';

    const CATEGORY_COMPLAIN = 'COMPLAIN';
    const CATEGORY_CONCLUSION = 'CONCLUSION';

  
}