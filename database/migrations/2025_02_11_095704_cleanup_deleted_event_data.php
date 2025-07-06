<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\File;
use App\Models\Event;
use App\Models\EventFile;
use App\Helper\Files;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $eventIds = Event::pluck('id')->toArray();
        $folder = EventFile::FILE_PATH;
        $folderPath = public_path(Files::UPLOAD_FOLDER . '/' . $folder);

        if (File::exists($folderPath)) {

            $eventFolders = File::directories($folderPath);
            foreach ($eventFolders as $eventFolder) {

                $eventId = basename($eventFolder);

                if (!in_array($eventId, $eventIds)) {

                    File::deleteDirectory($eventFolder);

                }
            }
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
