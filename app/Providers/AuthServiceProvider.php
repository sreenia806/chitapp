<?php

namespace App\Providers;

use App\Role;
use App\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        $user = \Auth::user();


        // Auth gates for: User management
        Gate::define('user_management_access', function ($user) {
            return in_array($user->role_id, [1]);
        });

        // Auth gates for: Member management
        Gate::define('member_management_access', function ($user) {
            return in_array($user->role_id, [1, 2, 3]);
        });

        // Auth gates for: Chit Scheme management
        Gate::define('scheme_management_access', function ($user) {
            return in_array($user->role_id, [1, 2]);
        });

        // Auth gates for: Roles
        Gate::define('role_access', function ($user) {
            return in_array($user->role_id, [1]);
        });
        Gate::define('role_create', function ($user) {
            return in_array($user->role_id, [1]);
        });
        Gate::define('role_edit', function ($user) {
            return in_array($user->role_id, [1]);
        });
        Gate::define('role_view', function ($user) {
            return in_array($user->role_id, [1]);
        });
        Gate::define('role_delete', function ($user) {
            return in_array($user->role_id, [1]);
        });

        // Auth gates for: Users
        Gate::define('user_access', function ($user) {
            return in_array($user->role_id, [1]);
        });
        Gate::define('user_create', function ($user) {
            return in_array($user->role_id, [1]);
        });
        Gate::define('user_edit', function ($user) {
            return in_array($user->role_id, [1]);
        });
        Gate::define('user_view', function ($user) {
            return in_array($user->role_id, [1]);
        });
        Gate::define('user_delete', function ($user) {
            return in_array($user->role_id, [1]);
        });


        // Auth gates for: Members
        Gate::define('member_access', function ($user) {
            return in_array($user->role_id, [1, 2]);
        });
        Gate::define('member_create', function ($user) {
            return in_array($user->role_id, [1, 2]);
        });
        Gate::define('member_edit', function ($user) {
            return in_array($user->role_id, [1, 2]);
        });
        Gate::define('member_view', function ($user) {
            return in_array($user->role_id, [1,2 ]);
        });
        Gate::define('member_delete', function ($user) {
            return in_array($user->role_id, [1, 2]);
        });


        // Auth gates for: Users
        Gate::define('scheme_access', function ($user) {
            return in_array($user->role_id, [1, 2]);
        });
        Gate::define('scheme_create', function ($user) {
            return in_array($user->role_id, [1, 2]);
        });
        Gate::define('scheme_edit', function ($user) {
            return in_array($user->role_id, [1, 2]);
        });
        Gate::define('scheme_view', function ($user) {
            return in_array($user->role_id, [1, 2]);
        });
        Gate::define('scheme_delete', function ($user) {
            return in_array($user->role_id, [1, 2]);
        });


        // Auth gates for: Payments
        Gate::define('payment_access', function ($user) {
            return in_array($user->role_id, [1, 2]);
        });
        Gate::define('payment_create', function ($user) {
            return in_array($user->role_id, [1, 2]);
        });
        Gate::define('payment_edit', function ($user) {
            return in_array($user->role_id, [1, 2]);
        });
        Gate::define('payment_view', function ($user) {
            return in_array($user->role_id, [1, 2]);
        });
        Gate::define('payment_delete', function ($user) {
            return in_array($user->role_id, [1, 2]);
        });


        // Auth gates for: Expense management
        Gate::define('ledger_management_access', function ($user) {
            return in_array($user->role_id, [1, 2]);
        });

        // Auth gates for: Expense category
        Gate::define('ledger_category_access', function ($user) {
            return in_array($user->role_id, [1, 2]);
        });
        Gate::define('ledger_category_create', function ($user) {
            return in_array($user->role_id, [1, 2]);
        });
        Gate::define('ledger_category_edit', function ($user) {
            return in_array($user->role_id, [1, 2]);
        });
        Gate::define('ledger_category_view', function ($user) {
            return in_array($user->role_id, [1, 2]);
        });
        Gate::define('ledger_category_delete', function ($user) {
            return in_array($user->role_id, [1]);
        });


        // Auth gates for: Expense
        Gate::define('ledger_entry_access', function ($user) {
            return in_array($user->role_id, [1, 2]);
        });
        Gate::define('ledger_entry_create', function ($user) {
            return in_array($user->role_id, [1, 2, 3]);
        });
        Gate::define('ledger_entry_edit', function ($user) {
            return in_array($user->role_id, [1]);
        });
        Gate::define('ledger_entry_view', function ($user) {
            return in_array($user->role_id, [1, 2, 3]);
        });
        Gate::define('ledger_entry_delete', function ($user) {
            return in_array($user->role_id, [1]);
        });

        // Auth gates for: Monthly report
        Gate::define('monthly_report_access', function ($user) {
            return in_array($user->role_id, [1, 2]);
        });

        // Auth gates for: Currency
        Gate::define('currency_access', function ($user) {
            return in_array($user->role_id, [1]);
        });
        Gate::define('currency_create', function ($user) {
            return in_array($user->role_id, [1]);
        });
        Gate::define('currency_edit', function ($user) {
            return in_array($user->role_id, [1]);
        });
        Gate::define('currency_view', function ($user) {
            return in_array($user->role_id, [1]);
        });
        Gate::define('currency_delete', function ($user) {
            return in_array($user->role_id, [1]);
        });

    }
}
