<?php
namespace Pure\Components\Tools\Dates{
    class Dates {
        public function fromNow($date_str){
            $date           = date_create($date_str);
            $date_cuttent   = new \DateTime("now");
            $interval       = date_diff($date, $date_cuttent);
            $result         =   ($interval->y > 0 ? $interval->y.' years '  : '');
            $result         .=  ($interval->m > 0 ? $interval->m.' months ' : '');
            $result         .=  ($interval->d > 0 ? $interval->d.' days '   : '');
            if ($result === ''){
                $result         =   ($interval->h > 0 ? $interval->h.' hours '      : '');
                $result         .=  ($interval->i > 0 ? $interval->i.' minutes '    : '');
            }
            return $result;
        }
        public function between($_date_from, $_date_to){
            $date_from      = date_create($_date_from);
            $date_to        = date_create($_date_to);
            $interval       = date_diff($date_from, $date_to);
            $result         =   ($interval->y > 0 ? $interval->y.' years '  : '');
            $result         .=  ($interval->m > 0 ? $interval->m.' months ' : '');
            $result         .=  ($interval->d > 0 ? $interval->d.' days '   : '');
            if ($result === ''){
                $result         =   ($interval->h > 0 ? $interval->h.' hours '      : '');
                $result         .=  ($interval->i > 0 ? $interval->i.' minutes '    : '');
            }
            return $result;
        }
    }
}
?>