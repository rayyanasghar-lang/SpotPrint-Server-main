<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class DashboardController extends Controller
{
    public function getSystemStats()
    {
        // Users [exclude Root role] (total / active / inactive / guests) 
        $userQuery = User::withoutRole('Root');
        $userCounts = $userQuery
            ->selectRaw("COUNT(*) as total, SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active, SUM(CASE WHEN status = 'inActive' THEN 1 ELSE 0 END) as inactive")
            ->first();
        $stats['users']['total'] = $userCounts->total;
        $stats['users']['active'] = $userCounts->active;
        $stats['users']['inactive'] = $userCounts->inactive;

        // get users count for each role
        $stats['users']['by_role'] = DB::table('model_has_roles')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->join('users', 'model_has_roles.model_id', '=', 'users.id')
            ->where('roles.name', '!=', 'Root')
            ->select('roles.name as role', DB::raw('count(users.id) as count'))
            ->groupBy('roles.name')
            ->get();


        // Categories (total / active / inactive)
        $stats['categories']['total'] = DB::table('categories')->count();
        $stats['categories']['active'] = DB::table('categories')->where('status', 'active')->count();
        $stats['categories']['inactive'] = DB::table('categories')->where('status', 'inActive')->count();


        // Products (total / active / inactive)
        $stats['products']['total'] = DB::table('products')->count();
        $stats['products']['active'] = DB::table('products')->where('is_active', true)->count();
        $stats['products']['inactive'] = DB::table('products')->where('is_active', false)->count();


        // Orders (total / completed / pending)
        $stats['orders']['total'] = DB::table('orders')->count();
        $stats['orders']['completed'] = DB::table('orders')->where('order_status', 'Completed')->count();
        $stats['orders']['pending'] = DB::table('orders')->where('order_status', 'Pending')->count();


        // Sales Amount (total / chart data for last 30 days)
        $stats['sales']['total'] = DB::table('orders')
            ->where('payment_status', 'Paid')
            ->where('created_at', '>=', now()->subDays(30))
            ->sum('total');
        $stats['sales']['chart'] = DB::table('orders')
            ->where('payment_status', 'Paid')
            ->where('created_at', '>=', now()->subDays(30))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total) as total'))
            ->groupBy('created_at')
            ->orderBy('created_at')
            ->pluck('total', 'date');

        


        // Total number of guests
        //$stats['users']['guests'] = DB::table('orders')->whereNull('user_id')->count();

        // Stock (total / out of stock)
        //$stats['stock']['total'] = DB::table('products')->sum('stock');
        //$stats['stock']['out_of_stock'] = DB::table('products')->where('stock', 0)->count();


        // Total number of orders by payment status
        $stats['paymentStatusCounts'] = DB::table('orders')->select('payment_status', DB::raw('count(*) as count'))->groupBy('payment_status')->get();

        // Total number of orders by order status
        $stats['orderStatusCounts'] = DB::table('orders')->select('order_status', DB::raw('count(*) as count'))->groupBy('order_status')->get();


        return $this->successResponse($stats, 'System stats retrieved successfully');
    }
}