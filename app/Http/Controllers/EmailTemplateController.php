<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class EmailTemplateController extends Controller
{
    protected $templatesPath;

    public function __construct()
    {
        $this->templatesPath = resource_path('views/emails');
    }

    public function index()
    {
        $templates = [];
        $files = File::glob($this->templatesPath . '/*.blade.php');

        foreach ($files as $file) {
            $filename = basename($file);
            $name = Str::replace('.blade.php', '', $filename);
            
            $templates[] = [
                'name' => $name,
                'filename' => $filename,
                'title' => Str::title(Str::replace('-', ' ', $name)),
                'path' => $file,
            ];
        }

        return response()->json(['templates' => $templates]);
    }

    public function show($template)
    {
        $filename = $template . '.blade.php';
        $filepath = $this->templatesPath . '/' . $filename;

        if (!File::exists($filepath)) {
            return response()->json(['error' => 'Template not found'], 404);
        }

        $content = File::get($filepath);

        return response()->json([
            'name' => $template,
            'filename' => $filename,
            'content' => $content,
        ]);
    }

    public function update(Request $request, $template)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $filename = $template . '.blade.php';
        $filepath = $this->templatesPath . '/' . $filename;

        if (!File::exists($filepath)) {
            return response()->json(['error' => 'Template not found'], 404);
        }

        // Backup original file
        $backupPath = $this->templatesPath . '/backups';
        if (!File::exists($backupPath)) {
            File::makeDirectory($backupPath, 0755, true);
        }
        
        $backupFile = $backupPath . '/' . $filename . '.' . now()->format('Y-m-d_H-i-s') . '.backup';
        File::copy($filepath, $backupFile);

        // Update template
        File::put($filepath, $request->content);

        return response()->json([
            'success' => true,
            'message' => 'Template updated successfully',
            'backup' => basename($backupFile),
        ]);
    }

    /**
     * Preview template with sample data
     */
    public function preview(Request $request, $template)
    {
        $request->validate([
            'content' => 'required|string'
        ]);

        try {
            // Get sample data based on template name
            $data = $this->getSampleData($template);
            
            // Create a temporary view
            $tempPath = storage_path('tmp');
            if (!File::exists($tempPath)) {
                File::makeDirectory($tempPath, 0755, true);
            }
            
            $tempFile = $tempPath . '/' . $template . '-' . uniqid() . '.blade.php';
            File::put($tempFile, $request->content);
            
            // Render the template
            $html = view()->file($tempFile, $data)->render();
            
            // Clean up temp file
            File::delete($tempFile);
            
            return response()->json(['html' => $html]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error rendering template: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * Get sample data for template preview
     */
    private function getSampleData($template)
    {
        switch ($template) {
            case 'subscription-adjusted':
                return [
                    'member' => (object)[
                        'first_name' => 'John',
                        'last_name' => 'Doe',
                        'full_name' => 'John Doe',
                        'email' => 'john.doe@example.com',
                    ],
                    'subscription' => (object)[
                        'id' => 1,
                        'type' => 'Wedding Loan Payment',
                        'frequency' => 'Monthly',
                        'amount' => 350,
                        'billing_frequency' => 'monthly',
                        'gateway_name' => 'Cardknox',
                    ],
                    'oldAmount' => 250,
                    'newAmount' => 350,
                ];
            
            case 'note-notification':
                return [
                    'member' => (object)[
                        'first_name' => 'John',
                        'last_name' => 'Doe',
                        'full_name' => 'John Doe',
                        'email' => 'john.doe@example.com',
                    ],
                    'note' => (object)[
                        'note' => 'This is a sample note message. Please contact us if you have any questions.',
                    ],
                    'userName' => 'Admin User',
                ];
            
            default:
                return [
                    'member' => (object)[
                        'first_name' => 'Sample',
                        'last_name' => 'User',
                        'full_name' => 'Sample User',
                        'email' => 'sample@example.com',
                    ],
                ];
        }
    }
}