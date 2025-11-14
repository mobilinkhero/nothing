<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\BotFlow;
use Corbital\ModuleManager\Facades\ModuleManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class BotFlowController extends Controller
{
    public function edit($subdomain, $id)
    {

        $flow = BotFlow::where('tenant_id', tenant_id())->findOrFail($id);
        $isAiAssistantModuleEnabled = ModuleManager::isActive('AiAssistant');

        return view('tenant.bot-flows.edit', compact('flow', 'isAiAssistantModuleEnabled'));
    }

    public function upload(Request $request)
    {
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'file' => 'required|file',
                'type' => 'required|string|in:image,video,audio,document',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Get the file
            $file = $request->file('file');
            $extension = strtolower($file->getClientOriginalExtension());

            // Define allowed extensions for each media type
            $allowedExtensions = get_meta_allowed_extension();

            // Check if extension is allowed for this media type
            if (! isset($allowedExtensions[$request->type]['extension']) || ! in_array('.'.$extension, explode(', ', $allowedExtensions[$request->type]['extension']))) {
                return response()->json([
                    'message' => "Invalid file type. Allowed types for {$request->type} are: ".$allowedExtensions[$request->type]['extension'],
                ], 422);
            }

            // Generate a unique filename
            $filename = Str::uuid().'.'.$extension;

            // Define the storage path based on media type
            $path = "bot_flow/{$request->type}s"; // e.g., images, videos, audios, documents

            // Store the file in the public disk
            $storedPath = $file->storeAs($path, $filename, 'public');

            // Debug logging
            \Log::info('BotFlow Upload Debug', [
                'path' => $path,
                'filename' => $filename,
                'stored_path' => $storedPath,
                'file_exists_after_store' => Storage::disk('public')->exists($storedPath ?? ''),
                'storage_path' => Storage::disk('public')->path($storedPath ?? ''),
                'disk_config' => config('filesystems.disks.public')
            ]);

            // Verify the file was stored successfully
            if (! $storedPath) {
                \Log::error('BotFlow Upload Failed - No stored path', [
                    'original_filename' => $file->getClientOriginalName(),
                    'path' => $path,
                    'filename' => $filename
                ]);
                return response()->json([
                    'message' => 'File upload failed - storage returned null',
                ], 500);
            }

            // Double check file exists
            if (! Storage::disk('public')->exists($storedPath)) {
                \Log::error('BotFlow Upload Failed - File not found after storage', [
                    'stored_path' => $storedPath,
                    'storage_path' => Storage::disk('public')->path($storedPath)
                ]);
                return response()->json([
                    'message' => 'File upload failed - file not accessible after storage',
                ], 500);
            }

            // Generate the public URL for the file
            $url = Storage::disk('public')->url($storedPath);

            // Log success to home directory  
            $this->logToHomeDirectory('BotFlow Upload SUCCESS', [
                'timestamp' => now()->format('Y-m-d H:i:s'),
                'original_filename' => $file->getClientOriginalName(),
                'stored_path' => $storedPath,
                'generated_url' => $url,
                'file_size' => $file->getSize(),
                'media_type' => $request->type
            ]);

            // Return the URL to the frontend
            return response()->json([
                'url' => $url,
                'fileName' => $file->getClientOriginalName(),
            ]);
        } catch (\Exception $e) {
            // Return a detailed error response
            return response()->json([
                'message' => 'An error occurred during file upload',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function save(Request $request, $subdomain)
    {
        // Debug logging to home directory
        $this->logToHomeDirectory('BotFlow Save Request', [
            'timestamp' => now()->format('Y-m-d H:i:s'),
            'tenant_id' => tenant_id(),
            'request_data' => $request->all(),
            'has_flow_data' => $request->has('flow_data'),
            'has_name' => $request->has('name'),
            'has_id' => $request->has('id'),
            'id_value' => $request->id
        ]);

        \Log::info('BotFlow Save Request', [
            'tenant_id' => tenant_id(),
            'request_data' => $request->all(),
            'has_flow_data' => $request->has('flow_data'),
            'has_name' => $request->has('name'),
            'has_id' => $request->has('id'),
            'id_value' => $request->id
        ]);

        // Check if this is a flow data save (only id and flow_data) vs name/description save
        $isFlowDataSave = $request->has('flow_data') && ! $request->has('name');

        // Different validation rules based on what's being saved
        if ($isFlowDataSave) {
            $validator = Validator::make($request->all(), [
                'flow_data' => 'required|json',
                'id' => 'required|exists:bot_flows,id',
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'flow_data' => 'nullable|json',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
            ]);
        }

        if ($validator->fails()) {
            $this->logToHomeDirectory('BotFlow Save Validation FAILED', [
                'timestamp' => now()->format('Y-m-d H:i:s'),
                'errors' => $validator->errors()->toArray(),
                'request_data' => $request->all(),
                'validation_type' => $isFlowDataSave ? 'Flow Data Save' : 'Name/Description Save'
            ]);

            \Log::error('BotFlow Save Validation Failed', [
                'errors' => $validator->errors()->toArray(),
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        if ($request->id) {
            $flow = BotFlow::where('tenant_id', tenant_id())->findOrFail($request->id);

            if ($isFlowDataSave) {
                // Only update flow_data
                $flow->update(['flow_data' => $request->flow_data]);
            } else {
                // Prepare data for name/description update
                $flowData = [];

                if ($request->has('name')) {
                    $flowData['name'] = $request->name;
                }

                if ($request->has('description')) {
                    $flowData['description'] = $request->description;
                }

                if ($request->has('is_active')) {
                    $flowData['is_active'] = $request->has('is_active') ? 1 : 0;
                }

                // Only update flow_data if provided (to avoid overwriting existing data)
                if ($request->has('flow_data') && ! is_null($request->flow_data)) {
                    $flowData['flow_data'] = $request->flow_data;
                }

                $flow->update($flowData);
            }

            $message = t('flow_updated_successfully');
        } else {
            // For new flows, both name and flow_data are required
            if (! $request->has('flow_data') || ! $request->has('name')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Name and flow data are required for new flows',
                ], 422);
            }

            $flowData = [
                'tenant_id' => tenant_id(),
                'name' => $request->name,
                'flow_data' => $request->flow_data,
                'is_active' => $request->has('is_active') ? 1 : 0,
            ];

            if ($request->has('description')) {
                $flowData['description'] = $request->description;
            }

            $flow = BotFlow::create($flowData);
            $message = t('flow_created_successfully');
        }

        $this->logToHomeDirectory('BotFlow Save SUCCESS', [
            'timestamp' => now()->format('Y-m-d H:i:s'),
            'flow_id' => $flow->id,
            'tenant_id' => $flow->tenant_id,
            'message' => $message,
            'flow_name' => $flow->name ?? 'N/A',
            'is_active' => $flow->is_active
        ]);

        \Log::info('BotFlow Save Success', [
            'flow_id' => $flow->id,
            'tenant_id' => $flow->tenant_id,
            'message' => $message
        ]);

        return response()->json([
            'success' => true,
            'message' => $message,
            'flow_id' => $flow->id,
        ]);
    }

    public function get($subdomain, $id)
    {
        $flow = BotFlow::where('tenant_id', tenant_id())->findOrFail($id);

        // Debug logging for flow loading
        $this->logToHomeDirectory('BotFlow Load Request', [
            'timestamp' => now()->format('Y-m-d H:i:s'),
            'flow_id' => $id,
            'tenant_id' => tenant_id(),
            'flow_name' => $flow->name,
            'flow_data_length' => strlen($flow->flow_data ?? ''),
            'flow_data_preview' => substr($flow->flow_data ?? '', 0, 200) . '...',
            'has_flow_data' => !empty($flow->flow_data)
        ]);

        return response()->json([
            'success' => true,
            'flow' => json_decode($flow->flow_data),
        ]);
    }

    public function delete($id)
    {
        if (! checkPermission(['bot_flows.delete'])) {
            return response()->json([
                'success' => false,
                'message' => t('access_denied'),
            ], 403);
        }

        $flow = BotFlow::where('tenant_id', tenant_id())->findOrFail($id);
        $flow->delete();

        return response()->json([
            'success' => true,
            'message' => t('flow_deleted_successfully'),
        ]);
    }

    /**
     * Log debugging information to home directory
     */
    private function logToHomeDirectory($title, $data = [])
    {
        try {
            $logFile = base_path('botflow_save_debug.log');
            $timestamp = now()->format('Y-m-d H:i:s');
            
            $logEntry = "================================================================================\n";
            $logEntry .= "[{$timestamp}] {$title}\n";
            $logEntry .= "================================================================================\n";
            
            foreach ($data as $key => $value) {
                if (is_array($value) || is_object($value)) {
                    $logEntry .= strtoupper($key) . ":\n";
                    $logEntry .= json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n\n";
                } else {
                    $logEntry .= strtoupper($key) . ": " . $value . "\n";
                }
            }
            
            $logEntry .= "================================================================================\n\n";
            
            file_put_contents($logFile, $logEntry, FILE_APPEND);
            
        } catch (\Exception $e) {
            // Silently fail if logging fails
            \Log::error('Home directory logging failed', ['error' => $e->getMessage()]);
        }
    }
}
