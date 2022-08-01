<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class SecurityCheckPhotoTypeModel extends MY_Model
{
    protected $table = 'security_check_photo_types';

    const CATEGORY_INBOUND = 'INBOUND';
    const CATEGORY_OUTBOUND = 'OUTBOUND';
    const CATEGORY_EMPTY_CONTAINER = 'EMPTY CONTAINER';

    const TYPE_START = 'START';
    const TYPE_STOP = 'STOP';

    /**
     * Security group by category.
     *
     * @param $category
     * @return array|null
     */
    public function getSecurityCheckCategories($category)
    {
        $baseQuery = $this->db
            ->select([
                'category',
                '(
                    SELECT COUNT(*) FROM security_check_photo_types AS sc_starts
                    WHERE sc_starts.type = "START"
                        AND sc_starts.category = security_check_photo_types.category
                ) AS total_start',
                '(
                    SELECT COUNT(*) FROM security_check_photo_types AS sc_starts
                    WHERE sc_starts.type = "STOP"
                        AND sc_starts.category = security_check_photo_types.category
                ) AS total_stop',
            ])
            ->from($this->table)
            ->where('category', $category)
            ->group_by('category');

        return $baseQuery->get()->row_array();
    }
}