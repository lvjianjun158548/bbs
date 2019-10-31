<?php

namespace app\home\model;

use think\Model;

class SpecValue extends Model
{
    //
    public function specBind(){
        return $this->belongsto('Spec','spec_id','id')->bind('spec_name');
    }
}
