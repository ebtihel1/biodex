<?php

namespace App\Http\Controllers\Campaign;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    public function index()
    {
        $campaigns = Campaign::with('user')->get();
        return response()->json($campaigns);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'in:draft,active,closed',
            'user_id' => 'nullable|exists:users,id',
        ]);

        $campaign = Campaign::create($validated);
        return response()->json($campaign, 201);
    }

    public function show($id)
    {
        $campaign = Campaign::with('user')->findOrFail($id);
        return response()->json($campaign);
    }

    public function update(Request $request, $id)
    {
        $campaign = Campaign::findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after_or_equal:start_date',
            'status' => 'in:draft,active,closed',
        ]);

        $campaign->update($validated);
        return response()->json($campaign);
    }

    public function destroy($id)
    {
        $campaign = Campaign::findOrFail($id);
        $campaign->delete();

        return response()->json(['message' => 'Campaign deleted successfully']);
    }
    public function frontIndex()
    {
    $campaigns = \App\Models\Campaign::where('status', 'active')->get();
    return view('front.campaign.campaigns', compact('campaigns'));
    }   

}
