<?php

use App\Models\Company;
use App\Models\Module;
use App\Models\ModuleSetting;
use App\Models\Permission;
use App\Models\PermissionRole;
use App\Models\Role;
use App\Models\User;
use App\Models\UserPermission;
use App\Scopes\ActiveScope;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */

    public function up()
    {
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id')->unsigned()->nullable();
            $table->foreign('company_id')
                ->references('id')
                ->on('companies')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->string('type')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('account_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('account_type')->nullable();
            $table->integer('currency_id')->unsigned()->nullable();
            $table->foreign('currency_id')
                ->references('id')
                ->on('currencies')
                ->onDelete('set null')
                ->onUpdate('cascade');
            $table->string('contact_number')->nullable();
            $table->double('opening_balance', 15, 2)->nullable();
            $table->string('bank_logo')->nullable();
            $table->boolean('status')->nullable();
            $table->integer('added_by')->unsigned()->nullable();
            $table->foreign('added_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null')
                ->onUpdate('cascade');
            $table->integer('last_updated_by')->unsigned()->nullable();
            $table->foreign('last_updated_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null')
                ->onUpdate('cascade');
            $table->double('bank_balance', 16, 2)->nullable();
            $table->timestamps();
        });

        Schema::create('bank_transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id')->unsigned()->nullable();
            $table->foreign('company_id')
                ->references('id')
                ->on('companies')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->integer('bank_account_id')->unsigned()->nullable();
            $table->foreign('bank_account_id')
                ->references('id')
                ->on('bank_accounts')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->integer('payment_id')->unsigned()->nullable();
            $table->foreign('payment_id')
                ->references('id')
                ->on('payments')
                ->onDelete('set null')
                ->onUpdate('cascade');
            $table->integer('invoice_id')->unsigned()->nullable();
            $table->foreign('invoice_id')
                ->references('id')
                ->on('invoices')
                ->onDelete('set null')
                ->onUpdate('cascade');
            $table->integer('expense_id')->unsigned()->nullable();
            $table->foreign('expense_id')
                ->references('id')
                ->on('expenses')
                ->onDelete('set null')
                ->onUpdate('cascade');
            $table->double('amount', 15, 2)->nullable();
            $table->enum('type', ['Cr', 'Dr'])->default('Cr');
            $table->integer('added_by')->unsigned()->nullable();
            $table->foreign('added_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null')
                ->onUpdate('cascade');
            $table->integer('last_updated_by')->unsigned()->nullable();
            $table->foreign('last_updated_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null')
                ->onUpdate('cascade');
            $table->text('memo')->nullable();
            $table->string('transaction_relation')->nullable();
            $table->string('transaction_related_to')->nullable();
            $table->text('title')->nullable();
            $table->date('transaction_date')->nullable();
            $table->double('bank_balance', 16, 2)->nullable();
            $table->timestamps();
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->integer('bank_account_id')->unsigned()->nullable();
            $table->foreign('bank_account_id')
                ->references('id')
                ->on('bank_accounts')
                ->onDelete('set null')
                ->onUpdate('cascade');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->integer('bank_account_id')->unsigned()->nullable();
            $table->foreign('bank_account_id')
                ->references('id')
                ->on('bank_accounts')
                ->onDelete('set null')
                ->onUpdate('cascade');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->integer('bank_account_id')->unsigned()->nullable();
            $table->foreign('bank_account_id')
                ->references('id')
                ->on('bank_accounts')
                ->onDelete('set null')
                ->onUpdate('cascade');
        });


        $count = Company::withoutGlobalScope(ActiveScope::class)->count();

        // If bankaccount module is created right now and not previosly
        if ($count > 0) {

            $module = Module::firstOrCreate(['module_name' => 'bankaccount']);

            if ($module->wasRecentlyCreated) {
                $modulePayment = Module::where('module_name', 'payments')->first();
                $moduleExpense = Module::where('module_name', 'expenses')->first();
                $moduleInvoice = Module::where('module_name', 'invoices')->first();

                $permissions = [
                    [
                        'module_id' => $module->id,
                        'name' => 'add_bankaccount',
                        'allowed_permissions' => Permission::ALL_NONE,
                        'is_custom' => 0
                    ],
                    [
                        'module_id' => $module->id,
                        'name' => 'view_bankaccount',
                        'allowed_permissions' => Permission::ALL_4_ADDED_1_NONE_5,
                        'is_custom' => 0
                    ],
                    [
                        'module_id' => $module->id,
                        'name' => 'edit_bankaccount',
                        'allowed_permissions' => Permission::ALL_4_ADDED_1_NONE_5,
                        'is_custom' => 0
                    ],
                    [
                        'module_id' => $module->id,
                        'name' => 'delete_bankaccount',
                        'allowed_permissions' => Permission::ALL_4_ADDED_1_NONE_5,
                        'is_custom' => 0
                    ],
                    [
                        'module_id' => $module->id,
                        'name' => 'add_bank_transfer',
                        'allowed_permissions' => Permission::ALL_NONE,
                        'is_custom' => 1,
                    ],
                    [
                        'module_id' => $module->id,
                        'name' => 'add_bank_deposit',
                        'allowed_permissions' => Permission::ALL_NONE,
                        'is_custom' => 1,
                    ],
                    [
                        'module_id' => $module->id,
                        'name' => 'add_bank_withdraw',
                        'allowed_permissions' => Permission::ALL_NONE,
                        'is_custom' => 1,
                    ],
                    [
                        'module_id' => $modulePayment->id,
                        'name' => 'link_payment_bank_account',
                        'is_custom' => 1,
                        'allowed_permissions' => Permission::ALL_NONE
                    ],
                    [
                        'module_id' => $moduleExpense->id,
                        'name' => 'link_expense_bank_account',
                        'is_custom' => 1,
                        'allowed_permissions' => Permission::ALL_NONE
                    ],
                    [
                        'module_id' => $moduleInvoice->id,
                        'name' => 'link_invoice_bank_account',
                        'is_custom' => 1,
                        'allowed_permissions' => Permission::ALL_NONE
                    ],
                ];

                Permission::insert($permissions);
            }

            $companies = Company::select('id')->get();

            $typeArray = [];
            $types = ['admin', 'employee'];

            foreach ($companies as $company) {
                foreach ($types as $type) {
                    $typeArray[] = [
                        'company_id' => $company->id,
                        'module_name' => 'bankaccount',
                        'status' => 'active',
                        'type' => $type,
                    ];
                }

            }

            foreach (array_chunk($typeArray, 200) as $setting) {
                ModuleSetting::insert($setting);
            }

            $allBankPermissions = Permission::where('module_id', $module->id)->get();

            $permissionRole = [];
            $userPermission = [];

            foreach ($allBankPermissions as $permission) {

                foreach ($companies as $company) {

                    $role = Role::where('name', 'admin')->where('company_id', $company->id)->first();

                    $permissionRole[] = [
                        'permission_id' => $permission->id,
                        'role_id' => $role->id,
                        'permission_type_id' => 4,
                    ];

                    $admins = User::allAdmins($company->id);

                    foreach ($admins as $admin) {
                        $userPermission [] = [
                            'user_id' => $admin->id,
                            'permission_id' => $permission->id,
                            'permission_type_id' => 4,
                        ];
                    }
                }
            }

            foreach (array_chunk($permissionRole, 200) as $permissionRoleChunk) {
                PermissionRole::insert($permissionRoleChunk);
            }

            foreach (array_chunk($userPermission, 200) as $userPermissionChunk) {
                UserPermission::insert($userPermissionChunk);
            }


        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }

};
