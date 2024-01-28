<?php

use Clickbar\Magellan\Schema\MagellanSchema;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        MagellanSchema::enablePostgisIfNotExists($this->connection);
    }

    public function down(): void
    {
        // leaving like this for the moment as this will fail on php artisan db:wipe
        // because the extension is enabled for the test DB as well.
        //        MagellanSchema::disablePostgisIfExists($this->connection);
    }
};
