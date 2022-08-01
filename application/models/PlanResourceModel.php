<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class PlanResourceModel
 * @property ReportPlanRealizationModel $reportPlanRealization
 */
class PlanResourceModel extends MY_Model
{
    protected $table = 'plan_resources';

    const TYPE_ALL = 'ALL';
    const TYPE_INBOUND = 'INBOUND';
    const TYPE_OUTBOUND = 'OUTBOUND';

    const RESOURCE_LABOR = 'Labor';
    const RESOURCE_FORKLIFT = 'Forklift';
    const RESOURCE_CRANE = 'Crane';

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
                'IFNULL((plan_resources.plan - plan_resources.realization), 0) AS outstanding',
            ]);
    }
    /**
     * Get data by custom condition.
     *
     * @param $conditions
     * @param bool $resultRow
     * @param bool $withTrashed
     * @return array|int
     */
    public function getBy($conditions, $resultRow = false, $withTrashed = false)
    {
        $baseQuery = $this->getBaseQuery();

        if (key_exists('compare', $conditions)) {
            $referenceType = self::TYPE_ALL;
            if (key_exists('type', $conditions)) {
                $referenceType = $conditions['type'];
            }
            if (key_exists('plan_resources.type', $conditions)) {
                $referenceType = $conditions['plan_resources.type'];
            }

            $baseQuery
                ->select([
                    'IFNULL(resource_realizations.total_realization, 0) - IFNULL(plan_resources.realization, 0) AS current_realization',
                ]);

            if ($referenceType == self::TYPE_ALL) {
                $baseQuery->join("(
                    SELECT 
                        id_plan_resource, 
                        IFNULL(SUM(IF(guest.checkin IS NOT NULL, 1, 0)), 0) AS total_realization 
                    FROM plan_resource_references
                    INNER JOIN plan_resources ON plan_resources.id = plan_resource_references.id_plan_resource
                    INNER JOIN " . env('DB_VMS_DATABASE') . ".guest ON guest.id = plan_resource_references.id_reference
                    WHERE plan_resources.resource = 'Labor'
                    GROUP BY id_plan_resource
                    
                    UNION ALL
                    
                    SELECT
                        id_plan_resource,
                        SUM(IFNULL(realizations.total, 0) + IFNULL(realization_internals.total, 0)) AS total_realization
                    FROM plan_resource_references
                    INNER JOIN plan_resources ON plan_resources.id = plan_resource_references.id_plan_resource
                    LEFT JOIN (
                        SELECT id_requisition, SUM(IF(total_used_in_work_order > 0, 1, 0)) AS total
                        FROM (
                            SELECT
                              id_requisition,
                              heavy_equipment_entry_permits.id AS id_heep,
                              COUNT(work_order_component_heeps.id) AS total_used_in_work_order
                            FROM heavy_equipment_entry_permits
                            LEFT JOIN work_order_component_heeps ON work_order_component_heeps.id_heep = heavy_equipment_entry_permits.id
                            GROUP BY id_requisition, heavy_equipment_entry_permits.id
                            HAVING total_used_in_work_order > 0
                        ) AS heep GROUP BY id_requisition
                    ) AS realizations ON realizations.id_requisition = plan_resource_references.id_reference 
                        AND plan_resource_references.type = 'EXTERNAL'
                    LEFT JOIN (
                        SELECT
                          ref_heavy_equipments.id,
                          IF(COUNT(work_order_component_heavy_equipments.id) > 0, 1, 0) AS total
                        FROM ref_heavy_equipments
                        LEFT JOIN work_order_component_heavy_equipments ON work_order_component_heavy_equipments.id_heavy_equipment = ref_heavy_equipments.id
                        WHERE DATE(work_order_component_heavy_equipments.created_at) = '" . format_date($conditions['compare_date']) . "'
                        GROUP BY ref_heavy_equipments.id
                    ) AS realization_internals ON realization_internals.id = plan_resource_references.id_reference 
                        AND plan_resource_references.type = 'INTERNAL'
                    WHERE plan_resources.resource IN('Forklift', 'Crane')
                    GROUP BY id_plan_resource
                ) AS resource_realizations", "resource_realizations.id_plan_resource = plan_resources.id", 'left');
            } else if ($referenceType == self::TYPE_INBOUND) {
                $baseQuery
                    ->join('plan_inbounds', 'plan_inbounds.id = plan_resources.id_reference', 'left')
                    ->join("(
                        SELECT 
                            bookings.id AS id_booking, 
                            'Labor' AS resource,
                            IFNULL(SUM(CAST(work_order_components.quantity AS UNSIGNED)), 0) AS total_realization 
                        FROM bookings
                        INNER JOIN handlings ON handlings.id_booking = bookings.id
                        INNER JOIN ref_handling_types ON ref_handling_types.id = handlings.id_handling_type
                        INNER JOIN work_orders ON work_orders.id_handling = handlings.id
                        INNER JOIN work_order_components ON work_order_components.id_work_order = work_orders.id
                        INNER JOIN ref_components ON ref_components.id = work_order_components.id_component
                        WHERE ref_components.handling_component IN('Labours', 'Labors')
                            AND handling_type IN('UNLOAD', 'STRIPING')
                            AND handlings.is_deleted = FALSE
                            AND handlings.status != 'REJECTED'
                            AND work_orders.is_deleted = FALSE
                        GROUP BY bookings.id
                        
                        UNION ALL
                        
                        SELECT 
                            id_booking,
                            resource,
                            SUM(total_realization) AS total_realization
                        FROM (
                            SELECT
                                handlings.id_booking, 
                                UPPER(ref_item_categories.item_name) AS resource,
                                IFNULL(COUNT(DISTINCT work_order_component_heeps.id_heep), 0) AS total_realization
                            FROM handlings
                            INNER JOIN ref_handling_types ON ref_handling_types.id = handlings.id_handling_type
                            INNER JOIN work_orders ON work_orders.id_handling = handlings.id
                            INNER JOIN work_order_components ON work_order_components.id_work_order = work_orders.id
                            INNER JOIN work_order_component_heeps ON work_order_component_heeps.id_work_order_component = work_order_components.id
                            INNER JOIN heavy_equipment_entry_permits ON heavy_equipment_entry_permits.id = work_order_component_heeps.id_heep
                            INNER JOIN " . env('DB_PURCHASE_DATABASE') . ".requisitions ON requisitions.id = heavy_equipment_entry_permits.id_requisition
                            INNER JOIN " . env('DB_PURCHASE_DATABASE') . ".ref_item_categories ON ref_item_categories.id = requisitions.id_item_category
                            WHERE ref_item_categories.item_name IN('Forklift', 'Crane')
                                AND handling_type IN('UNLOAD', 'STRIPING')
                                AND handlings.is_deleted = FALSE
                                AND handlings.status = 'APPROVED'
                                AND work_orders.status = 'COMPLETED'
                                AND work_orders.is_deleted = FALSE
                                AND DATE(handlings.handling_date) = '" . format_date($conditions['compare_date']) . "'
                            GROUP BY handlings.id_booking, UPPER(ref_item_categories.item_name)
                        
                            UNION ALL
                        
                            SELECT
                                handlings.id_booking, 
                                UPPER(ref_heavy_equipments.type) AS resource,
                                IFNULL(COUNT(DISTINCT ref_heavy_equipments.id), 0) AS total_realization
                            FROM handlings
                            INNER JOIN ref_handling_types ON ref_handling_types.id = handlings.id_handling_type
                            INNER JOIN work_orders ON work_orders.id_handling = handlings.id
                            INNER JOIN work_order_components ON work_order_components.id_work_order = work_orders.id
                            INNER JOIN work_order_component_heavy_equipments ON work_order_component_heavy_equipments.id_work_order_component = work_order_components.id
                            INNER JOIN ref_heavy_equipments ON ref_heavy_equipments.id = work_order_component_heavy_equipments.id_heavy_equipment
                            WHERE ref_heavy_equipments.type IN('FORKLIFT', 'CRANE')
                                AND handling_type IN('UNLOAD', 'STRIPING')
                                AND handlings.is_deleted = FALSE
                                AND handlings.status = 'APPROVED'
                                AND work_orders.status = 'COMPLETED'
                                AND work_orders.is_deleted = FALSE
                                AND DATE(work_orders.completed_at) = '" . format_date($conditions['compare_date']) . "'
                            GROUP BY handlings.id_booking, UPPER(ref_heavy_equipments.type)
                        ) AS heavy_equipments
                        GROUP BY id_booking, resource
                    ) AS resource_realizations", "resource_realizations.id_booking = plan_inbounds.id_booking 
                        AND resource_realizations.resource = plan_resources.resource", 'left');
            } else if ($referenceType == self::TYPE_OUTBOUND) {
                $baseQuery
                    ->join('plan_outbounds', 'plan_outbounds.id = plan_resources.id_reference', 'left')
                    ->join("(
                        SELECT 
                            bookings.id AS id_booking, 
                            'Labor' AS resource,
                            IFNULL(SUM(CAST(work_order_components.quantity AS UNSIGNED)), 0) AS total_realization 
                        FROM bookings
                        INNER JOIN handlings ON handlings.id_booking = bookings.id
                        INNER JOIN ref_handling_types ON ref_handling_types.id = handlings.id_handling_type
                        INNER JOIN work_orders ON work_orders.id_handling = handlings.id
                        INNER JOIN work_order_components ON work_order_components.id_work_order = work_orders.id
                        INNER JOIN ref_components ON ref_components.id = work_order_components.id_component
                        WHERE ref_components.handling_component IN('Labours', 'Labors')
                            AND handling_type IN('LOAD')
                            AND handlings.is_deleted = FALSE
                            AND handlings.status = 'APPROVED'
                            AND work_orders.is_deleted = FALSE
                        GROUP BY bookings.id
                        
                        UNION ALL
                        
                        SELECT 
                            id_booking,
                            resource,
                            SUM(total_realization) AS total_realization
                        FROM (
                            SELECT
                                handlings.id_booking, 
                                UPPER(ref_item_categories.item_name) AS resource,
                                IFNULL(COUNT(DISTINCT work_order_component_heeps.id_heep), 0) AS total_realization
                            FROM handlings
                            INNER JOIN ref_handling_types ON ref_handling_types.id = handlings.id_handling_type
                            INNER JOIN work_orders ON work_orders.id_handling = handlings.id
                            INNER JOIN work_order_components ON work_order_components.id_work_order = work_orders.id
                            INNER JOIN work_order_component_heeps ON work_order_component_heeps.id_work_order_component = work_order_components.id
                            INNER JOIN heavy_equipment_entry_permits ON heavy_equipment_entry_permits.id = work_order_component_heeps.id_heep
                            INNER JOIN " . env('DB_PURCHASE_DATABASE') . ".requisitions ON requisitions.id = heavy_equipment_entry_permits.id_requisition
                            INNER JOIN " . env('DB_PURCHASE_DATABASE') . ".ref_item_categories ON ref_item_categories.id = requisitions.id_item_category
                            WHERE ref_item_categories.item_name IN('Forklift', 'Crane')
                                AND handling_type IN('LOAD')
                                AND handlings.is_deleted = FALSE
                                AND handlings.status = 'APPROVED'
                                AND work_orders.status = 'COMPLETED'
                                AND work_orders.is_deleted = FALSE
                                AND DATE(work_orders.completed_at) = '" . format_date($conditions['compare_date']) . "'
                            GROUP BY handlings.id_booking, UPPER(ref_item_categories.item_name)
                        
                            UNION ALL
                        
                            SELECT
                                handlings.id_booking, 
                                UPPER(ref_heavy_equipments.type) AS resource,
                                IFNULL(COUNT(DISTINCT ref_heavy_equipments.id), 0) AS total_realization
                            FROM handlings
                            INNER JOIN ref_handling_types ON ref_handling_types.id = handlings.id_handling_type
                            INNER JOIN work_orders ON work_orders.id_handling = handlings.id
                            INNER JOIN work_order_components ON work_order_components.id_work_order = work_orders.id
                            INNER JOIN work_order_component_heavy_equipments ON work_order_component_heavy_equipments.id_work_order_component = work_order_components.id
                            INNER JOIN ref_heavy_equipments ON ref_heavy_equipments.id = work_order_component_heavy_equipments.id_heavy_equipment
                            WHERE ref_heavy_equipments.type IN('FORKLIFT', 'CRANE')
                                AND handling_type IN('LOAD')
                                AND handlings.is_deleted = FALSE
                                AND handlings.status = 'APPROVED'
                                AND work_orders.status = 'COMPLETED'
                                AND work_orders.is_deleted = FALSE
                                AND DATE(work_orders.completed_at) = '" . format_date($conditions['compare_date']) . "'
                            GROUP BY handlings.id_booking, UPPER(ref_heavy_equipments.type)
                        ) AS heavy_equipments
                        GROUP BY id_booking, resource
                    ) AS resource_realizations", "resource_realizations.id_booking = plan_outbounds.id_booking 
                        AND resource_realizations.resource = plan_resources.resource", 'left');
            }

            unset($conditions['compare']);
            unset($conditions['compare_date']);
        }

        foreach ($conditions as $key => $condition) {
            if (is_array($condition)) {
                if (!empty($condition)) {
                    $baseQuery->where_in($key, $condition);
                }
            } else {
                $baseQuery->where($key, $condition);
            }
        }

        if (!$withTrashed && $this->db->field_exists('is_deleted', $this->table)) {
            $baseQuery->where($this->table . '.is_deleted', false);
        }

        if ($resultRow === 'COUNT') {
            return $baseQuery->count_all_results();
        } else if ($resultRow) {
            return $baseQuery->get()->row_array();
        }

        $data = $baseQuery->get()->result_array();

        return $data;
    }
}
