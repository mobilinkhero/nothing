<?php

namespace App\Console\Commands;

use App\Models\Tenant\SalesBot;
use App\Models\Tenant\Tenant;
use Illuminate\Console\Command;
use Spatie\Multitenancy\Models\Tenant as SpatieMultitenancyTenant;

class DebugSalesBot extends Command
{
    protected $signature = 'sales-bot:debug {tenant_id?}';
    protected $description = 'Debug SalesBot models and tenant relationships';

    public function handle()
    {
        $tenantId = $this->argument('tenant_id');
        
        $this->info('🔍 Sales Bot Debug Information');
        $this->info('================================');
        
        // If tenant ID provided, focus on that tenant
        if ($tenantId) {
            $this->debugSpecificTenant($tenantId);
        } else {
            $this->debugAllTenants();
        }
        
        // Debug current tenant context
        $this->info("\n🎯 Current Context:");
        $currentTenant = SpatieMultitenancyTenant::current();
        if ($currentTenant) {
            $this->info("Current Tenant ID: {$currentTenant->id}");
            $this->info("Current Tenant Key: {$currentTenant->tenant_key}");
        } else {
            $this->warn("No current tenant set");
        }
    }
    
    private function debugSpecificTenant($tenantId)
    {
        $this->info("📋 Tenant ID: {$tenantId}");
        
        // Find tenant
        $tenant = SpatieMultitenancyTenant::find($tenantId);
        if (!$tenant) {
            $this->error("❌ Tenant {$tenantId} not found!");
            return;
        }
        
        $this->info("✅ Tenant found: " . ($tenant->tenant_key ?? $tenant->subdomain ?? "ID: {$tenant->id}"));
        
        // Find SalesBots for this tenant
        $salesBots = SalesBot::where('tenant_id', $tenantId)->get();
        
        $this->info("\n📦 SalesBots for Tenant {$tenantId}:");
        if ($salesBots->count() > 0) {
            foreach ($salesBots as $bot) {
                $this->info("  • ID: {$bot->id} | Name: {$bot->name} | Active: " . ($bot->is_active ? 'Yes' : 'No'));
                $this->info("    Google Sheet: {$bot->google_sheet_id}");
                $this->info("    Products: {$bot->products()->count()} | Orders: {$bot->orders()->count()}");
                $this->info("    Created: {$bot->created_at}");
                $this->info("");
            }
        } else {
            $this->warn("❌ No SalesBots found for tenant {$tenantId}");
            $tenantKey = $tenant->tenant_key ?? $tenant->subdomain ?? $tenantId;
            $this->info("💡 Create a SalesBot first at: /{$tenantKey}/sales-bot/create");
        }
    }
    
    private function debugAllTenants()
    {
        $this->info("📋 All Tenants and their SalesBots:");
        
        $tenants = SpatieMultitenancyTenant::all();
        
        foreach ($tenants as $tenant) {
            $tenantName = $tenant->tenant_key ?? $tenant->subdomain ?? "ID: {$tenant->id}";
            $this->info("\n🏢 Tenant: {$tenantName} (ID: {$tenant->id})");
            
            $salesBots = SalesBot::where('tenant_id', $tenant->id)->get();
            
            if ($salesBots->count() > 0) {
                foreach ($salesBots as $bot) {
                    $this->info("  • SalesBot ID: {$bot->id} | Name: {$bot->name}");
                }
            } else {
                $this->warn("  • No SalesBots");
            }
        }
        
        // Check for orphaned SalesBots
        $this->info("\n🔍 Checking for orphaned SalesBots...");
        $allSalesBots = SalesBot::all();
        $validTenantIds = $tenants->pluck('id')->toArray();
        
        $orphaned = $allSalesBots->reject(function ($bot) use ($validTenantIds) {
            return in_array($bot->tenant_id, $validTenantIds);
        });
        
        if ($orphaned->count() > 0) {
            $this->error("⚠️  Found orphaned SalesBots:");
            foreach ($orphaned as $bot) {
                $this->error("  • ID: {$bot->id} | Name: {$bot->name} | Tenant ID: {$bot->tenant_id} (invalid)");
            }
        } else {
            $this->info("✅ No orphaned SalesBots found");
        }
    }
}
