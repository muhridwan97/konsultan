<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class PlanResourceReferenceModel
 */
class PlanResourceReferenceModel extends MY_Model
{
    protected $table = 'plan_resource_references';

    /**
     * Get base query of table.
     *
     * @param null $branchId
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($branchId = null)
    {
        return parent::getBaseQuery($branchId)
            ->select([
                'plan_resources.resource',
            ])
            ->join('plan_resources', 'plan_resources.id = plan_resource_references.id_plan_resource', 'left');
    }

}
