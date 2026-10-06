<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Donation;
use App\Models\Order;
use App\Models\Participation;
use App\Models\Reservation;
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
        $wastes = Waste::query()->get(['created_at', 'weight', 'waste_category_id', 'status']);
        $categoryNames = WasteCategory::query()->pluck('name', 'id');

        $weightOf = fn ($rows) => (float) $rows->sum(fn ($row) => (float) $row->weight);

        $totalWeight = $weightOf($wastes);
        $recycledWeight = $weightOf($wastes->where('status', 'recyclable'));
        $reusableWeight = $weightOf($wastes->where('status', 'reusable'));

        $participationCount = Participation::query()->count();
        $pendingRequests = Order::query()->where('status', 'pending')->count()
            + Reservation::query()->where('status', 'pending')->count();

        $campaigns = Campaign::query()->get();

        return view('back.home', [
            'totalWeight' => round($totalWeight, 2),
            'wasteCount' => $wastes->count(),
            'recycledWeight' => round($recycledWeight, 2),
            'recycledShare' => $totalWeight > 0 ? round($recycledWeight / $totalWeight * 100, 1) : 0.0,
            'reusableWeight' => round($reusableWeight, 2),
            'activeCampaigns' => (int) $campaigns->where('status', 'active')->count(),
            'participationCount' => $participationCount,
            'pendingRequests' => $pendingRequests,
            'trendChart' => $this->trendChart($wastes, $categoryNames),
            'distributionChart' => $this->distributionChart($wastes, $categoryNames),
            'campaignProgress' => $this->campaignProgress($campaigns),
            'recentActivity' => $this->recentActivity(),
            'topWastes' => $this->topWastes($wastes, $categoryNames, $totalWeight),
        ]);
    }

    private function trendChart($wastes, $categoryNames)
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

    private function distributionChart($wastes, $categoryNames)
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

    private function campaignProgress($campaigns)
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

    private function recentActivity()
    {
        $items = collect();

        Waste::query()->latest()->take(5)->get(['id', 'type', 'description', 'weight', 'created_at'])
            ->each(fn ($waste) => $items->push([
                'title' => 'Waste logged: ' . $waste->type,
                'detail' => $waste->weight . ' kg collected',
                'timestamp' => $waste->created_at,
            ]));

        Campaign::query()->latest()->take(3)->get(['id', 'title', 'status', 'created_at'])
            ->each(fn ($campaign) => $items->push([
                'title' => 'Campaign ' . $campaign->status . ': ' . $campaign->title,
                'detail' => 'Campaign record updated',
                'timestamp' => $campaign->created_at,
            ]));

        Order::query()->latest()->take(3)->get(['id', 'status', 'total_amount', 'created_at'])
            ->each(fn ($order) => $items->push([
                'title' => 'Order #' . $order->id . ' ' . $order->status,
                'detail' => 'Total ' . $order->total_amount,
                'timestamp' => $order->created_at,
            ]));

        Donation::query()->latest()->take(3)->get(['id', 'item_name', 'status', 'created_at'])
            ->each(fn ($donation) => $items->push([
                'title' => 'Donation ' . ($donation->status?->value ?? '') . ': ' . $donation->item_name,
                'detail' => 'Community donation',
                'timestamp' => $donation->created_at,
            ]));

        return $items->sortByDesc(fn ($item) => $item['timestamp']->getTimestamp())
            ->take(4)
            ->map(fn ($item) => [
                'title' => $item['title'],
                'detail' => $item['detail'],
                'time' => $item['timestamp']->diffForHumans(),
            ])
            ->values()
            ->all();
    }

    private function topWastes($wastes, $categoryNames, $totalWeight)
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

    private function hexToRgba(string $hex, float $alpha): string
    {
        $value = ltrim($hex, '#');
        $r = hexdec(substr($value, 0, 2));
        $g = hexdec(substr($value, 2, 2));
        $b = hexdec(substr($value, 4, 2));

        return sprintf('rgba(%d, %d, %d, %.2f)', $r, $g, $b, $alpha);
    }
}
