<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Table::truncate();
        $roles = [
            ['guard_name' => 'sanctum', 'name' => 'Admin'],
            ['guard_name' => 'sanctum', 'name' => 'Employee']
        ];

        $modules = [
            'Employees' => [
                ['name' => 'employees_access', 'guard_name' => 'sanctum'],
                ['name' => 'employees_create', 'guard_name' => 'sanctum'],
                ['name' => 'employees_show', 'guard_name' => 'sanctum'],
                ['name' => 'employees_edit', 'guard_name' => 'sanctum'],
                ['name' => 'employees_delete', 'guard_name' => 'sanctum'],
            ],
            'Designations' => [
                ['name' => 'designations_access', 'guard_name' => 'sanctum'],
                ['name' => 'designations_create', 'guard_name' => 'sanctum'],
                ['name' => 'designations_show', 'guard_name' => 'sanctum'],
                ['name' => 'designations_edit', 'guard_name' => 'sanctum'],
                ['name' => 'designations_delete', 'guard_name' => 'sanctum'],
            ],

            'Roles' => [
                ['name' => 'roles_access', 'guard_name' => 'sanctum'],
                ['name' => 'roles_create', 'guard_name' => 'sanctum'],
                ['name' => 'roles_show', 'guard_name' => 'sanctum'],
                ['name' => 'roles_edit', 'guard_name' => 'sanctum'],
                ['name' => 'roles_delete', 'guard_name' => 'sanctum'],
            ],

            'Companies' => [
                ['name' => 'companies_access', 'guard_name' => 'sanctum'],
                ['name' => 'companies_create', 'guard_name' => 'sanctum'],
                ['name' => 'companies_show', 'guard_name' => 'sanctum'],
                ['name' => 'companies_edit', 'guard_name' => 'sanctum'],
                ['name' => 'companies_delete', 'guard_name' => 'sanctum'],

            ],

            'Company Users'=>[
                ['name' => 'companies_users_access', 'guard_name' => 'sanctum'],
                ['name' => 'companies_users_create', 'guard_name' => 'sanctum'],
                ['name' => 'companies_users_show', 'guard_name' => 'sanctum'],
                ['name' => 'companies_users_edit', 'guard_name' => 'sanctum'],
                ['name' => 'companies_users_delete', 'guard_name' => 'sanctum'],
            ],

            'Company Contracts'=>[
                ['name' => 'companies_contracts_access', 'guard_name' => 'sanctum'],

            ],
            'Company Machines'=>[
                ['name' => 'companies_machines_access', 'guard_name' => 'sanctum'],
                ['name' => 'companies_machines_add', 'guard_name' => 'sanctum'],
                ['name' => 'companies_machines_dettach', 'guard_name' => 'sanctum'],
            ],
            'Contracts' => [
                ['name' => 'contracts_access', 'guard_name' => 'sanctum'],
                ['name' => 'contracts_create', 'guard_name' => 'sanctum'],
                ['name' => 'contracts_show', 'guard_name' => 'sanctum'],
                ['name' => 'contracts_edit', 'guard_name' => 'sanctum'],
                ['name' => 'contracts_delete', 'guard_name' => 'sanctum'],
                ['name' => 'contracts_machine_model_access', 'guard_name' => 'sanctum'],
            ],
            'Warehouses' => [

                ['name' => 'warehouses_access', 'guard_name' => 'sanctum'],
                ['name' => 'warehouses_create', 'guard_name' => 'sanctum'],
                ['name' => 'warehouses_show', 'guard_name' => 'sanctum'],
                ['name' => 'warehouses_edit', 'guard_name' => 'sanctum'],
                ['name' => 'warehouses_delete', 'guard_name' => 'sanctum'],
                ['name' => 'warehouses_parts_access', 'guard_name' => 'sanctum'],
            ],
            'Box Heading;' => [

                ['name' => 'box_heading_access', 'guard_name' => 'sanctum'],
                ['name' => 'box_heading_create', 'guard_name' => 'sanctum'],
                ['name' => 'box_heading_show', 'guard_name' => 'sanctum'],
                ['name' => 'box_heading_edit', 'guard_name' => 'sanctum'],
                ['name' => 'box_heading_delete', 'guard_name' => 'sanctum'],
                ['name' => 'box_heading_parts_access', 'guard_name' => 'sanctum'],
            ],
            'Machines' => [
                ['name' => 'machines_access', 'guard_name' => 'sanctum'],
                ['name' => 'machines_create', 'guard_name' => 'sanctum'],
                ['name' => 'machines_show', 'guard_name' => 'sanctum'],
                ['name' => 'machines_edit', 'guard_name' => 'sanctum'],
                ['name' => 'machines_delete', 'guard_name' => 'sanctum'],

                ['name' => 'machines_model_part_headings_access', 'guard_name' => 'sanctum'],
            ],
            'Machines Model'=>[
                ['name' => 'machines_model_access', 'guard_name' => 'sanctum'],
                ['name' => 'machines_model_add', 'guard_name' => 'sanctum'],
                ['name' => 'machines_model_show', 'guard_name' => 'sanctum'],
                ['name' => 'machines_model_edit', 'guard_name' => 'sanctum'],
                ['name' => 'machines_model_delete', 'guard_name' => 'sanctum'],
            ],
            'Machines Part Headings'=>[
                ['name' => 'machines_part_headings_access', 'guard_name' => 'sanctum'],
                ['name' => 'machines_part_headings_add', 'guard_name' => 'sanctum'],
                ['name' => 'machines_part_headings_edit', 'guard_name' => 'sanctum'],
                ['name' => 'machines_part_headings_delete', 'guard_name' => 'sanctum'],
            ],
            'Parts' => [
                ['name' => 'parts_access', 'guard_name' => 'sanctum'],
                ['name' => 'parts_create', 'guard_name' => 'sanctum'],
                ['name' => 'parts_show', 'guard_name' => 'sanctum'],
                ['name' => 'parts_edit', 'guard_name' => 'sanctum'],
                ['name' => 'parts_delete', 'guard_name' => 'sanctum'],
                ['name' => 'parts_barcode', 'guard_name' => 'sanctum'],
            ],
            'Parts Stocks'=>[
                ['name' => 'parts_stocks_access', 'guard_name' => 'sanctum'],
                ['name' => 'parts_stocks_add', 'guard_name' => 'sanctum'],
                ['name' => 'parts_stocks_show', 'guard_name' => 'sanctum'],
                ['name' => 'parts_stocks_edit', 'guard_name' => 'sanctum'],
                ['name' => 'parts_stocks_delete', 'guard_name' => 'sanctum'],
            ],
            'Parts Aliases'=>[
                ['name' => 'parts_aliases_access', 'guard_name' => 'sanctum'],
                ['name' => 'parts_aliases_add', 'guard_name' => 'sanctum'],
                ['name' => 'parts_aliases_show', 'guard_name' => 'sanctum'],
                ['name' => 'parts_aliases_edit', 'guard_name' => 'sanctum'],
                ['name' => 'parts_aliases_delete', 'guard_name' => 'sanctum'],
            ],
            'Requisitions' => [

                ['name' => 'requisitions_access', 'guard_name' => 'sanctum'],
                ['name' => 'requisitions_create', 'guard_name' => 'sanctum'],
                ['name' => 'requisitions_show', 'guard_name' => 'sanctum'],
                ['name' => 'requisitions_generate_quotation', 'guard_name' => 'sanctum'],
                ['name' => 'requisitions_print', 'guard_name' => 'sanctum'],
                ['name' => 'requisitions_approve', 'guard_name' => 'sanctum'],
            ],
            'Quotations' => [
                ['name' => 'quotations_access', 'guard_name' => 'sanctum'],
                ['name' => 'quotations_create', 'guard_name' => 'sanctum'],
                ['name' => 'quotations_show', 'guard_name' => 'sanctum'],
                ['name' => 'quotations_partItems_update', 'guard_name' => 'sanctum'],
                ['name' => 'quotations_lock', 'guard_name' => 'sanctum'],
                ['name' => 'quotations_generate_invoice', 'guard_name' => 'sanctum'],
                ['name' => 'quotations_approve', 'guard_name' => 'sanctum'],
            ],

            'Invoices' => [
                ['name' => 'invoices_access', 'guard_name' => 'sanctum'],
                ['name' => 'invoices_create', 'guard_name' => 'sanctum'],
                ['name' => 'invoices_show', 'guard_name' => 'sanctum'],
                ['name' => 'invoices_print', 'guard_name' => 'sanctum'],
                ['name' => 'invoices_generate_delivery_note', 'guard_name' => 'sanctum'],
                ['name' => 'invoices_payment', 'guard_name' => 'sanctum'],
                ['name' => 'invoices_delete_access', 'guard_name' => 'sanctum'],
            ],
            'Delivery Notes' => [
                ['name' => 'deliverynotes_access', 'guard_name' => 'sanctum'],
                ['name' => 'deliverynotes_create', 'guard_name' => 'sanctum'],
                ['name' => 'deliverynotes_show', 'guard_name' => 'sanctum'],
                ['name' => 'deliverynotes_print', 'guard_name' => 'sanctum'],
            ],
            'Gate Pass' => [
                ['name' => 'gate_pass_access', 'guard_name' => 'sanctum'],
            ],
            'Sales Report' => [
                ['name' => 'sales_report_access', 'guard_name' => 'sanctum'],
                ['name' => 'sales_report_export', 'guard_name' => 'sanctum'],
                ['name' => 'sales_report_filter', 'guard_name' => 'sanctum'],
            ],
            'Stock Report' => [
                ['name' => 'stock_report_access', 'guard_name' => 'sanctum'],
                ['name' => 'stock_report_export', 'guard_name' => 'sanctum'],
                ['name' => 'stock_report_view', 'guard_name' => 'sanctum'],
            ],
            'Settings' => [
                ['name' => 'settings_access', 'guard_name' => 'sanctum'],
                ['name' => 'settings_create', 'guard_name' => 'sanctum'],
                ['name' => 'settings_show', 'guard_name' => 'sanctum'],
                ['name' => 'settings_edit', 'guard_name' => 'sanctum'],
                ['name' => 'settings_delete', 'guard_name' => 'sanctum']
            ],
            'Company Parts' => [
                ['name' => 'company_part_access', 'guard_name' => 'sanctum'],
            ],
            'Claim Request' => [
                ['name' => 'claim_request_access', 'guard_name' => 'sanctum'],
            ],
            'Claim Requisition' => [
                ['name' => 'claim_requisition_access', 'guard_name' => 'sanctum'],
            ],
            'Transaction Summery' => [
                ['name' => 'transaction_summery_access', 'guard_name' => 'sanctum'],
            ],
            'Required Requisition' => [
                ['name' => 'required_requisition_access', 'guard_name' => 'sanctum'],
            ],
            'Foc Summery' => [
                ['name' => 'foc_summery_access', 'guard_name' => 'sanctum'],
            ],

        ];

        foreach ($modules as $key => $permissions) {
            $module = Module::create(['name' => $key]);
            foreach ($permissions as $permission)
                Permission::create(['name' => $permission['name'], 'guard_name' => $permission['guard_name'], 'module_id' => $module->id]);
        }

        $permissions = [];

        Role::insert($roles);
        // Permission::insert($permissions);
    }
}
