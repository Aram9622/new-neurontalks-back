<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Request;

class DatabaseController extends Controller
{
    /**
     * Run the database migrations.
     */
    public function migrate()
    {
        try {
            // Run migrations with --force because it's usually a production environment
            Artisan::call('migrate', ['--force' => true]);
            $output = Artisan::output();

            return response()->json([
                'status' => 'success',
                'message' => 'Migrations completed successfully.',
                'output' => $output
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Migration failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Run the database seeders.
     */
    public function seed()
    {
        try {
            Artisan::call('db:seed', ['--force' => true]);
            $output = Artisan::output();

            return response()->json([
                'status' => 'success',
                'message' => 'Seeding completed successfully.',
                'output' => $output
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Seeding failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
