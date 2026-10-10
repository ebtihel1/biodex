<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\CollectionPoint;
use App\Models\Donation;
use App\Models\Order;
use App\Models\Participation;
use App\Models\Product;
use App\Models\RecyclingProcess;
use App\Models\Reservation;
use App\Models\User;
use App\Models\Waste;
use App\Models\WasteCategory;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    private const CHART_COLORS = [
        '#0d6efd', '#198754', '#ffc107', '#0dcaf0', '#dc3545',
        '#6c757d', '#20c997', '#fd7e14', '#6f42c1', '#d63384',
    ];

    public function index()
    {
        // ============ KPIs GLOBAUX ============
        $kpis = $this->globalKpis();

        // ============ WASTES ============
        $wastes = Waste::query()->get(['created_at', 'weight', 'waste_category_id', 'status']);
        $categoryNames = WasteCategory::query()->pluck('name', 'id');

        $totalWeight = (float) $wastes->sum(fn ($w) => (float) $w->weight);
        $recycledWeight = (float) $wastes->where('status', 'recyclable')->sum(fn ($w) => (float) $w->weight);
        $reusableWeight = (float) $wastes->where('status', 'reusable')->sum(fn ($w) => (float) $w->weight);

        $campaigns = Campaign::query()->get();

        return view('back.home', [
            // ============ KPIs ============
            'kpis' => $kpis,

            // ============ Waste ============
            'totalWeight' => round($totalWeight, 2),
            'wasteCount' => $wastes->count(),
            'recycledWeight' => round($recycledWeight, 2),
            'recycledShare' => $totalWeight > 0 ? round($recycledWeight / $totalWeight * 100, 1) : 0.0,
            'reusableWeight' => round($reusableWeight, 2),
            'activeCampaigns' => (int) $campaigns->where('status', 'active')->count(),
            'participationCount' => Participation::count(),
            'pendingRequests' => Order::where('status', 'pending')->count()
                + Reservation::where('status', 'pending')->count(),

            // ============ Charts ============
            'trendChart' => $this->trendChart($wastes, $categoryNames),
            'distributionChart' => $this->distributionChart($wastes, $categoryNames),
            'revenueChart' => $this->revenueChart(),
            'moduleChart' => $this->moduleDistributionChart(),
            'campaignStatusChart' => $this->campaignStatusChart($campaigns),

            // ============ Lists ============
            'campaignProgress' => $this->campaignProgress($campaigns),
            'recentActivity' => $this->recentActivity(),
            'topWastes' => $this->topWastes($wastes, $categoryNames, $totalWeight),

            // ============ Module tables ============
            'topCollectionPoints' => $this->topCollectionPoints(),
            'topProducts' => $this->topProducts(),
            'recentOrders' => $this->recentOrders(),
            'recentDonations' => $this->recentDonations(),
            'recentReservations' => $this->recentReservations(),
            'recyclingProcessStats' => $this->recyclingProcessStats(),
        ]);
    }

    // ============================================================
    // KPIs GLOBAUX
    // ============================================================
    private function globalKpis(): array
    {
        return [
            'users' => User::count(),
            'waste_weight' => round((float) Waste::sum('weight'), 1),
            'orders_revenue' => round((float) Order::sum('total_amount'), 2),
            'products' => Product::count(),
            'donations' => Donation::count(),
            'reservations' => Reservation::count(),
            'campaigns' => Campaign::count(),
            'collection_points' => CollectionPoint::count(),
        ];
    }

    // ============================================================
    // TREND CHART
    // ============================================================
    private function trendChart($wastes, $categoryNames): array
    {
        $months = collect(range(5, 0))
            ->map(fn (int $offset) => Carbon::now()->startOfMonth()->subMonths($offset));

        $topCategoryIds = $wastes->groupBy('waste_category_id')
            ->map(fn ($rows, $categoryId) => [
                'id' => (int) $categoryId,
                'weight' => (float) $rows->sum(fn ($row) => (float) $row->weight),
            ])
            ->sortByDesc('weight')
            ->take(3)
            ->pluck('id')
            ->all();

        $weightByMonth = $wastes->groupBy(
            fn ($waste) => $waste->waste_category_id . '|' . $waste->created_at->format('Y-m')
        )->map(fn ($rows) => (float) $rows->sum(fn ($row) => (float) $row->weight));

        $datasets = [];
        foreach ($topCategoryIds as $index => $categoryId) {
            $datasets[] = [
                'label' => $categoryNames[$categoryId] ?? ('Category #' . $categoryId),
                'data' => $months->map(
                    fn ($month) => round($weightByMonth[$categoryId . '|' . $month->format('Y-m')] ?? 0, 2)
                )->values()->all(),
                'borderColor' => self::CHART_COLORS[$index % count(self::CHART_COLORS)],
                'backgroundColor' => $this->hexToRgba(self::CHART_COLORS[$index % count(self::CHART_COLORS)], 0.1),
            ];
        }

        return [
            'labels' => $months->map(fn ($month) => $month->format('M'))->values()->all(),
            'datasets' => $datasets,
        ];
    }

    // ============================================================
    // DISTRIBUTION CHART
    // ============================================================
    private function distributionChart($wastes, $categoryNames): array
    {
        $groups = $wastes->groupBy('waste_category_id')
            ->map(fn ($rows, $categoryId) => [
                'name' => $categoryNames[$categoryId] ?? ('Category #' . $categoryId),
                'weight' => round((float) $rows->sum(fn ($row) => (float) $row->weight), 2),
            ])
            ->sortByDesc('weight')
            ->values();

        return [
            'labels' => $groups->pluck('name')->all(),
            'data' => $groups->pluck('weight')->all(),
            'colors' => $groups->map(
                fn ($group, $index) => self::CHART_COLORS[$index % count(self::CHART_COLORS)]
            )->all(),
        ];
    }

    // ============================================================
    // REVENUE CHART
    // ============================================================
    private function revenueChart(): array
    {
        $months = collect(range(5, 0))
            ->map(fn (int $offset) => Carbon::now()->startOfMonth()->subMonths($offset));

        return [
            'labels' => $months->map(fn ($m) => $m->format('M'))->values()->all(),
            'orders' => $months->map(fn ($month) =>
                (float) Order::whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->sum('total_amount')
            )->values()->all(),
            'donations' => $months->map(fn ($month) =>
                Donation::whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)->count()
            )->values()->all(),
            'reservations' => $months->map(fn ($month) =>
                Reservation::whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)->count()
            )->values()->all(),
        ];
    }

    // ============================================================
    // MODULE DISTRIBUTION CHART
    // ============================================================
    private function moduleDistributionChart(): array
    {
        return [
            'labels' => ['Wastes', 'Donations', 'Orders', 'Reservations', 'Campaigns', 'Products'],
            'data' => [
                Waste::count(),
                Donation::count(),
                Order::count(),
                Reservation::count(),
                Campaign::count(),
                Product::count(),
            ],
            'colors' => array_slice(self::CHART_COLORS, 0, 6),
        ];
    }

    // ============================================================
    // CAMPAIGN STATUS CHART
    // ============================================================
    private function campaignStatusChart($campaigns): array
    {
        return [
            'labels' => ['Draft', 'Active', 'Closed'],
            'data' => [
                $campaigns->where('status', 'draft')->count(),
                $campaigns->where('status', 'active')->count(),
                $campaigns->where('status', 'closed')->count(),
            ],
            'colors' => ['#ffc107', '#198754', '#6c757d'],
        ];
    }

    // ============================================================
    // CAMPAIGN PROGRESS
    // ============================================================
    private function campaignProgress($campaigns): array
    {
        return $campaigns->where('status', 'active')
            ->sortBy(fn ($campaign) => (string) $campaign->start_date)
            ->take(4)
            ->map(function ($campaign) {
                $start = Carbon::parse($campaign->start_date)->getTimestamp();
                $end = Carbon::parse($campaign->end_date)->getTimestamp();
                $now = now()->getTimestamp();
                $duration = max(1, $end - $start);
                $progress = (int) round(min(100, max(0, ($now - $start) / $duration * 100)));

                return [
                    'title' => $campaign->title,
                    'location' => trim(($campaign->city ?? '') . ($campaign->region ? ', ' . $campaign->region : '')) ?: 'Location not set',
                    'progress' => $progress,
                    'participants' => (int) ($campaign->participants_count ?? 0),
                    'period' => Carbon::parse($campaign->start_date)->format('d M') . ' – ' . Carbon::parse($campaign->end_date)->format('d M Y'),
                ];
            })
            ->values()
            ->all();
    }

    // ============================================================
    // RECENT ACTIVITY
    // ============================================================
    private function recentActivity(): array
    {
        $items = collect();

        Waste::query()->latest()->take(4)->get(['id', 'type', 'weight', 'created_at'])
            ->each(fn ($waste) => $items->push([
                'icon' => 'trash', 'color' => 'primary',
                'title' => 'Waste logged: ' . $waste->type,
                'detail' => $waste->weight . ' kg collected',
                'timestamp' => $waste->created_at,
            ]));

        Campaign::query()->latest()->take(2)->get(['id', 'title', 'status', 'created_at'])
            ->each(fn ($c) => $items->push([
                'icon' => 'megaphone', 'color' => 'warning',
                'title' => 'Campaign ' . $c->status . ': ' . $c->title,
                'detail' => 'Campaign record updated',
                'timestamp' => $c->created_at,
            ]));

        Order::query()->latest()->take(2)->get(['id', 'status', 'total_amount', 'created_at'])
            ->each(fn ($o) => $items->push([
                'icon' => 'cart-check', 'color' => 'success',
                'title' => 'Order #' . $o->id . ' ' . $o->status,
                'detail' => 'Total ' . number_format($o->total_amount, 2) . ' DT',
                'timestamp' => $o->created_at,
            ]));

        Donation::query()->latest()->take(2)->get(['id', 'item_name', 'created_at'])
            ->each(fn ($d) => $items->push([
                'icon' => 'gift', 'color' => 'info',
                'title' => 'Donation: ' . $d->item_name,
                'detail' => 'Community donation',
                'timestamp' => $d->created_at,
            ]));

        Reservation::query()->latest()->take(2)->get(['id', 'status', 'created_at'])
            ->each(fn ($r) => $items->push([
                'icon' => 'calendar3', 'color' => 'secondary',
                'title' => 'Reservation #' . $r->id,
                'detail' => 'Status: ' . (is_object($r->status) ? $r->status->value : $r->status),
                'timestamp' => $r->created_at,
            ]));

        return $items->sortByDesc(fn ($item) => $item['timestamp']->getTimestamp())
            ->take(6)
            ->map(fn ($item) => [
                'icon' => $item['icon'], 'color' => $item['color'],
                'title' => $item['title'], 'detail' => $item['detail'],
                'time' => $item['timestamp']->diffForHumans(),
            ])
            ->values()
            ->all();
    }

    // ============================================================
    // TOP WASTES
    // ============================================================
    private function topWastes($wastes, $categoryNames, $totalWeight): array
    {
        return $wastes->groupBy('waste_category_id')
            ->map(fn ($rows, $categoryId) => [
                'name' => $categoryNames[$categoryId] ?? ('Category #' . $categoryId),
                'weight' => round((float) $rows->sum(fn ($row) => (float) $row->weight), 2),
                'count' => $rows->count(),
                'share' => $totalWeight > 0
                    ? (int) round((float) $rows->sum(fn ($row) => (float) $row->weight) / $totalWeight * 100)
                    : 0,
            ])
            ->sortByDesc('weight')
            ->take(4)
            ->values()
            ->all();
    }

    // ============================================================
    // TOP COLLECTION POINTS
    // ============================================================
    private function topCollectionPoints(): array
    {
        return CollectionPoint::query()
            ->withCount('wastes')
            ->orderByDesc('wastes_count')
            ->take(5)
            ->get(['id', 'name', 'city', 'status'])
            ->map(fn ($cp) => [
                'id' => $cp->id,
                'name' => $cp->name,
                'city' => $cp->city,
                'status' => $cp->status,
                'count' => $cp->wastes_count,
            ])
            ->all();
    }

    // ============================================================
    // TOP PRODUCTS
    // ============================================================
    private function topProducts(): array
    {
        return Product::query()
            ->orderByDesc('stock_quantity')
            ->take(5)
            ->get(['id', 'name', 'price', 'stock_quantity', 'is_available'])
            ->map(fn ($p) => [
                'id' => $p->id,
                'name' => $p->name,
                'price' => $p->price,
                'stock' => $p->stock_quantity,
                'available' => $p->is_available,
                'value' => $p->price * $p->stock_quantity,
            ])
            ->all();
    }

    // ============================================================
    // RECENT ORDERS
    // ============================================================
    private function recentOrders(): array
    {
        return Order::query()->with('user')->latest()->take(5)->get()
            ->map(fn ($o) => [
                'id' => $o->id,
                'user' => $o->user->name ?? 'Guest',
                'amount' => $o->total_amount,
                'status' => is_object($o->status) ? $o->status->value : $o->status,
                'date' => $o->created_at,
            ])->all();
    }

    // ============================================================
    // RECENT DONATIONS
    // ============================================================
    private function recentDonations(): array
    {
        return Donation::query()->with('user')->latest()->take(5)->get()
            ->map(fn ($d) => [
                'id' => $d->id,
                'user' => $d->user->name ?? 'Guest',
                'item' => $d->item_name,
                'condition' => $d->condition,
                'status' => is_object($d->status) ? $d->status->value : $d->status,
                'date' => $d->created_at,
            ])->all();
    }

    // ============================================================
    // RECENT RESERVATIONS
    // ============================================================
    private function recentReservations(): array
    {
        return Reservation::query()->with('user')->latest()->take(5)->get()
            ->map(fn ($r) => [
                'id' => $r->id,
                'user' => $r->user->name ?? 'Guest',
                'product_id' => $r->product_id,
                'quantity' => $r->quantity,
                'status' => is_object($r->status) ? $r->status->value : $r->status,
                'date' => $r->created_at,
            ])->all();
    }

    // ============================================================
    // RECYCLING PROCESS STATS
    // ============================================================
    private function recyclingProcessStats(): array
    {
        return [
            'total' => RecyclingProcess::count(),
            'pending' => RecyclingProcess::where('status', 'pending')->count(),
            'in_progress' => RecyclingProcess::where('status', 'in_progress')->count(),
            'completed' => RecyclingProcess::where('status', 'completed')->count(),
            'failed' => RecyclingProcess::where('status', 'failed')->count(),
            'output_total' => (float) RecyclingProcess::sum('output_quantity'),
        ];
    }

    // ============================================================
    // HELPERS
    // ============================================================
    private function hexToRgba(string $hex, float $alpha): string
    {
        $value = ltrim($hex, '#');
        $r = hexdec(substr($value, 0, 2));
        $g = hexdec(substr($value, 2, 2));
        $b = hexdec(substr($value, 4, 2));
        return sprintf('rgba(%d, %d, %d, %.2f)', $r, $g, $b, $alpha);
    }
}