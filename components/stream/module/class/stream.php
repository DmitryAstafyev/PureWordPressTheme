<?php
namespace Pure\Components\Stream\Module{
    class Provider{
        private $stream_option = 'pure_stream_storage';
        public function get_users_IDs_in_stream($owner_id){
            $stream = array();
            if ((int)$owner_id > 0){
                $stream = get_user_meta((int)$owner_id, $this->stream_option, true);
                return (is_array($stream) !== false ? $stream : false);
            }
        }
        public function is_in_stream($owner_id, $target_id){
            if ((int)$owner_id > 0 && (int)$target_id > 0){
                $stream = $this->get_users_IDs_in_stream($owner_id);
                if ($stream !== false){
                    return in_array((int)$target_id, $stream);
                }
            }
            return false;
        }
        private function save($owner_id, $stream){
            if ((int)$owner_id > 0 && is_array($stream) !== false){
                return update_user_meta( (int)$owner_id, $this->stream_option, $stream );
            }
            return false;
        }
        public function add($owner_id, $target_id){
            if ((int)$owner_id > 0 && (int)$target_id > 0){
                $stream = $this->get_users_IDs_in_stream($owner_id);
                $stream = ($stream === false ? array() : $stream);
                $stream[] = (int)$target_id;
                return $this->save((int)$owner_id, $stream);
            }
            return false;
        }
        public function remove($owner_id, $target_id){
            if ((int)$owner_id > 0 && (int)$target_id > 0){
                $stream = $this->get_users_IDs_in_stream($owner_id);
                if ($stream !== false){
                    if (in_array((int)$target_id, $stream) !== false){
                        $index = array_keys($stream, (int)$target_id);
                        if (count($index) > 0){
                            array_splice($stream, $index[0], 1);
                            return $this->save((int)$owner_id, $stream);
                        }
                    }
                }
            }
            return false;
        }
        public function toggle($owner_id, $target_id){
            if ((int)$owner_id > 0 && (int)$target_id > 0){
                $stream = $this->get_users_IDs_in_stream($owner_id);
                $stream = ($stream === false ? array() : $stream);
                if (in_array((int)$target_id, $stream) !== false){
                    $this->remove($owner_id, $target_id);
                }else{
                    $this->add($owner_id, $target_id);
                }
                return true;
            }
            return false;
        }
    }
}