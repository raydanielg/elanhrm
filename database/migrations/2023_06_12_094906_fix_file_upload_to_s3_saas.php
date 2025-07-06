<?php

use App\Helper\Files;
use App\Models\GlobalSetting;
use App\Models\SuperAdmin\Feature;
use App\Models\SuperAdmin\SeoDetail;
use App\Models\SuperAdmin\FrontDetail;
use App\Models\SuperAdmin\TrFrontDetail;
use App\Models\SuperAdmin\OfflinePlanChange;
use Illuminate\Database\Migrations\Migration;
use App\Models\SuperAdmin\GlobalInvoiceSetting;

return new class extends Migration
{

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $files = [
            [
                'model' => Feature::class,
                'columns' => [
                    [
                        'name' => 'image',
                        'path' => 'front/feature',
                    ]
                ],
            ],
            [
                'model' => TrFrontDetail::class,
                'columns' => [
                    [
                        'name' => 'image',
                        'path' => 'front',
                    ]
                ],
            ],
            [
                'model' => SeoDetail::class,
                'columns' => [
                    [
                        'name' => 'og_image',
                        'path' => 'front/seo-detail',
                    ]
                ],
            ],
            [
                'model' => FrontDetail::class,
                'columns' => [
                    [
                        'name' => 'background_image',
                        'path' => 'front/homepage-background',
                    ]
                ],
            ],
            [
                'model' => GlobalInvoiceSetting::class,
                'columns' => [
                    [
                        'name' => 'logo',
                        'path' => 'app-logo',
                    ],
                    [
                        'name' => 'authorised_signatory_signature',
                        'path' => 'app-logo',
                    ],
                ],
            ],
            [
                'model' => GlobalSetting::class,
                'columns' => [
                    [
                        'name' => 'logo',
                        'path' => 'app-logo',
                    ],
                    [
                        'name' => 'logo_front',
                        'path' => 'app-logo',
                    ],
                    [
                        'name' => 'light_logo',
                        'path' => 'app-logo',
                    ],
                    [
                        'name' => 'login_background',
                        'path' => 'login-background',
                    ],
                    [
                        'name' => 'favicon',
                        'path' => 'favicon',
                    ],
                ],
            ],
            [
                'model' => OfflinePlanChange::class,
                'columns' => [
                    [
                        'name' => 'file_name',
                        'path' => OfflinePlanChange::FILE_PATH,
                    ],
                ],
            ],
        ];

        foreach ($files as $file) {
            $model = $file['model'];
            $columns = $file['columns'];

            Files::fixLocalUploadFiles($model, $columns);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }

};
