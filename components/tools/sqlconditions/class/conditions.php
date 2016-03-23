<?php
namespace Pure\Components\Tools\SQLConditions{
    class Conditions{
        public function WHERE($field_name, $from_date, $number_days, $include_borders = true){
            $right      = new \DateTime((string)$from_date);
            $left       = new \DateTime((string)$from_date);
            $left->modify('-'.$number_days.' days');
            if ($include_borders === true){
                $right  ->modify('+1 days');
                $left   ->modify('-1 days');
            }
            $where      = "(".$field_name." BETWEEN '" . $left->format('Y-m-d') . "'" . " AND "."'". $right->format('Y-m-d') . "')";
            return $where;
        }
    }
}
?>