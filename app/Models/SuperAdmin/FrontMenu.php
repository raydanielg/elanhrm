<?php

namespace App\Models\SuperAdmin;

use App\Models\LanguageSetting;
use App\Models\BaseModel;

class FrontMenu extends BaseModel
{

    protected $guarded = ['id'];
    protected $table = 'front_menu_buttons';

    public function language()
    {
        return $this->belongsTo(LanguageSetting::class, 'language_setting_id');
    }

}
