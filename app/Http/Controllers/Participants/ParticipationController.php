<?php

namespace App\Http\Controllers\Participants;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Campaign;
use Illuminate\Support\Facades\Auth;

class ParticipationController extends Controller
{
    public function toggle($campaignId)
    {
        $user = Auth::user();
        $campaign = Campaign::findOrFail($campaignId);

        // Vérifier si l'utilisateur participe déjà
        $existing = $campaign->participations()->where('user_id', $user->id)->first();

        if ($existing) {
            // ❌ Supprimer la participation
            $existing->delete();
            $campaign->decrement('participants_count');

            return response()->json([
                'joined' => false,
                'participants' => $campaign->participants_count
            ]);
        } else {
            // ✅ Créer une participation
            $campaign->participations()->create([
    'user_id' => $user->id,
    'status' => 'approved'  
]);

            $campaign->increment('participants_count');

            return response()->json([
                'joined' => true,
                'participants' => $campaign->participants_count
            ]);
        }
    }
}
