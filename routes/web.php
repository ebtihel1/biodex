<?php
use App\Http\Controllers\DonationController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\OrderController;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backoffice\WasteCategoryController;
use App\Http\Controllers\Backoffice\WasteController;
use App\Models\WasteCategory;
use APP\Models\Waste;
use app\Models\CollectionPoint;
use App\Http\Controllers\Front\FrontWasteCategoryController;
use App\Http\Controllers\Front\FrontWasteController;
use App\Http\Controllers\AI\AIController;
use App\Http\Controllers\AI\WasteAIController;
use App\Http\Controllers\AI\RecyclingAIController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Backoffice\CollectionPointController;
use App\Http\Controllers\Front\CollectionPointFrontController;
use App\Http\Controllers\Backoffice\RecyclingProcessController;
use App\Http\Controllers\Backoffice\ProductController;
use App\Http\Controllers\Front\ProductFrontController;
use App\Http\Controllers\AI\CollectionAIController;
use App\Http\Controllers\Campaign\CampaignController;
use App\Http\Controllers\Campaign\AIControllerCampaign;
use App\Http\Controllers\Participants\ParticipationController;
use App\Http\Controllers\Auth\AuthentifController;
use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/biodex', function () {
    return view('front.home');
})->name('front.home');

// =============================================================
// Frontoffice Routes (Public - Prefixed with /biodex)
// =============================================================
Route::prefix('biodex')->name('front.')->group(function () {
    // Donations (Frontoffice)
    Route::get('donations', [DonationController::class, 'index'])->name('donations.index');
    Route::get('donations/create', [DonationController::class, 'create'])->name('donations.create');
    Route::post('donations', [DonationController::class, 'store'])->name('donations.store');
    Route::get('donations/export/csv', [DonationController::class, 'exportCsv'])->name('donations.export.csv');
    Route::get('donations/export/pdf', [DonationController::class, 'exportPdf'])->name('donations.export.pdf');
    Route::post('donations/import', [DonationController::class, 'importCsv'])->name('donations.import');

    Route::get('donations/{donation}', [DonationController::class, 'show'])->name('donations.show');
    Route::delete('donations/{donation}', [DonationController::class, 'destroy'])->name('donations.destroy');
    Route::get('donations/{donation}/edit', [DonationController::class, 'edit'])->name('donations.edit');
    Route::put('donations/{donation}', [DonationController::class, 'update'])->name('donations.update');
    Route::post('analyze-sentiment', [DonationController::class, 'analyzeSentiment'])->name('donations.analyze-sentiment');

    // Orders (Frontoffice)
    Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/create', [OrderController::class, 'create'])->name('orders.create');
    Route::post('orders', [OrderController::class, 'store'])->name('orders.store');

    // Orders Export / Import — MUST come before {order} routes
    Route::get('orders/export/csv', [OrderController::class, 'exportCsv'])->name('orders.export.csv');
    Route::get('orders/export/pdf', [OrderController::class, 'exportPdf'])->name('orders.export.pdf');
    Route::post('orders/import', [OrderController::class, 'importCsv'])->name('orders.import');

    Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('orders/{order}/edit', [OrderController::class, 'edit'])->name('orders.edit');
    Route::put('orders/{order}', [OrderController::class, 'update'])->name('orders.update');
    Route::delete('orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');

    // Reservations (Frontoffice)
    Route::get('reservations', [ReservationController::class, 'index'])->name('reservations.index');
    Route::get('reservations/create', [ReservationController::class, 'create'])->name('reservations.create');
    Route::post('reservations', [ReservationController::class, 'store'])->name('reservations.store');
      Route::get('reservations/export/csv', [ReservationController::class, 'exportCsv'])->name('reservations.export.csv');
    Route::get('reservations/export/pdf', [ReservationController::class, 'exportPdf'])->name('reservations.export.pdf');
    Route::post('reservations/import', [ReservationController::class, 'importCsv'])->name('reservations.import');
    Route::get('reservations/{reservation}', [ReservationController::class, 'show'])->name('reservations.show');
    Route::get('reservations/{reservation}/edit', [ReservationController::class, 'edit'])->name('reservations.edit');
    Route::put('reservations/{reservation}', [ReservationController::class, 'update'])->name('reservations.update');
    Route::delete('reservations/{reservation}', [ReservationController::class, 'destroy'])->name('reservations.destroy');
});

Route::middleware(['auth'])->group(function () {

    // Password Routes
    Route::get('settings/password', [UserController::class, 'editPassword'])->name('settings.password');
    Route::put('settings/password', [UserController::class, 'updatePassword'])->name('settings.password.update');

    // Profile View
    Route::get('/profile', function () {
        $user = Auth::user();
        return view('front.profil.profile', compact('user'));
    })->name('profile.view');

    Route::get('/back/profile', function () {
        $user = Auth::user();
        return view('back.profile', compact('user'));
    })->name('back.profile');
});

// ============================
// Waste Routes
// ============================
Route::get('wastes', [WasteController::class, 'index'])->name('wastes.index');
Route::get('wastes/create', [WasteController::class, 'create'])->name('wastes.create');
Route::post('wastes', [WasteController::class, 'store'])->name('wastes.store');

// Waste Export / Import — MUST be declared BEFORE the {id} routes
Route::get('wastes/export/csv', [WasteController::class, 'exportCsv'])->name('wastes.export.csv');
Route::get('wastes/export/pdf', [WasteController::class, 'exportPdf'])->name('wastes.export.pdf');
Route::post('wastes/import', [WasteController::class, 'importCsv'])->name('wastes.import');

Route::get('wastes/{id}/edit', [WasteController::class, 'edit'])->name('wastes.edit');
Route::put('wastes/{id}', [WasteController::class, 'update'])->name('wastes.update');
Route::delete('wastes/{id}', [WasteController::class, 'destroy'])->name('wastes.destroy');
Route::get('wastes/{id}', [WasteController::class, 'show'])->name('wastes.show');

// ============================
// Waste Category Routes
// ============================
Route::get('waste-categories', [WasteCategoryController::class, 'index'])
    ->name('waste_categories.index');
Route::get('waste-categories/create', [WasteCategoryController::class, 'create'])
    ->name('waste_categories.create');
Route::post('waste-categories', [WasteCategoryController::class, 'store'])
    ->name('waste_categories.store');

// Waste Category Export / Import — MUST be declared BEFORE the {id} routes
Route::get('waste-categories/export/csv', [WasteCategoryController::class, 'exportCsv'])
    ->name('waste_categories.export.csv');
Route::get('waste-categories/export/pdf', [WasteCategoryController::class, 'exportPdf'])
    ->name('waste_categories.export.pdf');
Route::post('waste-categories/import', [WasteCategoryController::class, 'importCsv'])
    ->name('waste_categories.import');

Route::get('waste-categories/{id}/edit', [WasteCategoryController::class, 'edit'])
    ->name('waste_categories.edit');
Route::put('waste-categories/{id}', [WasteCategoryController::class, 'update'])
    ->name('waste_categories.update');
Route::delete('waste-categories/{id}', [WasteCategoryController::class, 'destroy'])
    ->name('waste_categories.destroy');
Route::get('waste-categories/{id}', [WasteCategoryController::class, 'show'])
    ->name('waste_categories.show');

// ============================
// Recycling Process Routes (Back-office)
// ============================
Route::get('recyclingprocesses/export/csv', [RecyclingProcessController::class, 'exportCsv'])
    ->name('recyclingprocesses.export.csv');
Route::get('recyclingprocesses/export/pdf', [RecyclingProcessController::class, 'exportPdf'])
    ->name('recyclingprocesses.export.pdf');
Route::post('recyclingprocesses/import', [RecyclingProcessController::class, 'importCsv'])
    ->name('recyclingprocesses.import');
Route::resource('recyclingprocesses', RecyclingProcessController::class);

// ============================
// Product Routes (Back-office)
// ============================
Route::get('products/export/csv', [ProductController::class, 'exportCsv'])
    ->name('products.export.csv');
Route::get('products/export/pdf', [ProductController::class, 'exportPdf'])
    ->name('products.export.pdf');
Route::post('products/import', [ProductController::class, 'importCsv'])
    ->name('products.import');
Route::resource('products', ProductController::class);
Route::post('products/{id}/toggle-availability', [ProductController::class, 'toggleAvailability'])
    ->name('products.toggle-availability');

// Dashboard Route
Route::get('/back/home', [\App\Http\Controllers\Backoffice\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('back.home');

Route::get('dashboard', [\App\Http\Controllers\Backoffice\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('/collection-ai/train/{id}', [CollectionAIController::class, 'train']);
Route::get('/collection-ai/predict/{id}', [CollectionAIController::class, 'predict']);
Route::get('/collection-ai/forecasts', [CollectionAIController::class, 'forecasts'])->name('collection-ai.forecasts');

Route::get('/collectionpoints/predictions', [CollectionPointController::class, 'predictions'])
    ->name('collectionpoints.predictions');

// Donation Routes
Route::resource('donations', DonationController::class)->except(['edit', 'update']);

// Order Routes
Route::resource('orders', OrderController::class)->except(['edit', 'update']);

// Reservation Routes
Route::resource('reservations', ReservationController::class);

// =============================================================
// Back-end Routes (Back - Prefixed with /back)
// =============================================================
Route::prefix('back')->name('back.')->group(function () {
    Route::prefix('home')->group(function () {

        // Donations (Back-end)
        Route::get('donations', [DonationController::class, 'index'])->name('donations.index');
        Route::get('donations/create', [DonationController::class, 'create'])->name('donations.create');
        Route::post('donations', [DonationController::class, 'store'])->name('donations.store');
        Route::get('donations/export/csv', [DonationController::class, 'exportCsv'])->name('donations.export.csv');
        Route::get('donations/export/pdf', [DonationController::class, 'exportPdf'])->name('donations.export.pdf');
        Route::post('donations/import', [DonationController::class, 'importCsv'])->name('donations.import');
        Route::get('donations/{donation}', [DonationController::class, 'show'])->name('donations.show');
        Route::delete('donations/{donation}', [DonationController::class, 'destroy'])->name('donations.destroy');
        Route::put('donations/{donation}', [DonationController::class, 'update'])->name('donations.update');
        Route::get('donations/{donation}/edit', [DonationController::class, 'edit'])->name('donations.edit');
        Route::post('donations/analyze-sentiment', [DonationController::class, 'analyzeSentiment'])->name('donations.analyze-sentiment');

        // Orders (Back-end)
        Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/create', [OrderController::class, 'create'])->name('orders.create');
        Route::post('orders', [OrderController::class, 'store'])->name('orders.store');

        // Orders Export / Import (Back-office) — MUST come before {order} routes
        Route::get('orders/export/csv', [OrderController::class, 'exportCsv'])->name('orders.export.csv');
        Route::get('orders/export/pdf', [OrderController::class, 'exportPdf'])->name('orders.export.pdf');
        Route::post('orders/import', [OrderController::class, 'importCsv'])->name('orders.import');

        Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::get('orders/{order}/edit', [OrderController::class, 'edit'])->name('orders.edit');
        Route::put('orders/{order}', [OrderController::class, 'update'])->name('orders.update');
        Route::delete('orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');

        // Reservations (Back-end)
        Route::get('reservations', [ReservationController::class, 'index'])->name('reservations.index');
        Route::get('reservations/create', [ReservationController::class, 'create'])->name('reservations.create');
        Route::post('reservations', [ReservationController::class, 'store'])->name('reservations.store');
               Route::get('reservations/export/csv', [ReservationController::class, 'exportCsv'])->name('reservations.export.csv');
        Route::get('reservations/export/pdf', [ReservationController::class, 'exportPdf'])->name('reservations.export.pdf');
        Route::post('reservations/import', [ReservationController::class, 'importCsv'])->name('reservations.import');
        Route::get('reservations/{reservation}', [ReservationController::class, 'show'])->name('reservations.show');
        Route::get('reservations/{reservation}/edit', [ReservationController::class, 'edit'])->name('reservations.edit');
        Route::put('reservations/{reservation}', [ReservationController::class, 'update'])->name('reservations.update');
        Route::delete('reservations/{reservation}', [ReservationController::class, 'destroy'])->name('reservations.destroy');
    });
});

// =============================================================
// Campaigns
// =============================================================
Route::get('/campaignsFront', [CampaignController::class, 'frontIndex'])->name('campaigns.front');
Route::get('/ai/campaign/ask', [AIControllerCampaign::class, 'askAI'])->name('ai.campaign.ask');

// =============================================================
// Frontoffice Waste Category Routes
// =============================================================
Route::get('/categories', [FrontWasteCategoryController::class, 'index'])->name('front.waste-categories.index');
Route::get('/categories/{id}', [FrontWasteCategoryController::class, 'show'])->name('front.waste-categories.show');

// =============================================================
// Frontoffice Waste Routes
// =============================================================
Route::get('/wastess', [FrontWasteController::class, 'index'])->name('front.wastes.index');
Route::get('/wastess/create', [FrontWasteController::class, 'create'])->name('front.wastes.create');
Route::post('/wastess', [FrontWasteController::class, 'store'])->name('front.wastes.store');
Route::get('/wastess/{id}', [FrontWasteController::class, 'show'])->name('front.wastes.show');

// =============================================================
// AI Routes
// =============================================================
Route::post('/ai/predict', [AIController::class, 'predictWaste'])->name('ai.predict');
Route::get('/ai/classify', [App\Http\Controllers\AI\ImageClassificationController::class, 'showForm'])->name('ai.classify.form');
Route::post('/waste/classify', [App\Http\Controllers\AI\ImageClassificationController::class, 'classify'])->name('waste.classify');
Route::get('/predictwaste', function () {
    return view('predictwaste');
})->name('predictwaste');
Route::get('/ai-advice', [WasteAIController::class, 'showForm'])->name('ai.advice.form');
Route::post('/ai-advice', [WasteAIController::class, 'recycling'])->name('ai.advice.recycling');

// Démo IA pour le module Recyclage
Route::get('/ai/recycling/demo', function () {
    return view('ai.recycling-ai-demo');
})->name('ai.recycling.demo');

// Routes IA pour le module Recyclage
Route::prefix('ai/recycling')->group(function () {
    Route::post('/classify-waste', [RecyclingAIController::class, 'classifyWaste'])->name('ai.recycling.classify');
    Route::post('/predict-quality', [RecyclingAIController::class, 'predictQuality'])->name('ai.recycling.predict-quality');
    Route::post('/estimate-price', [RecyclingAIController::class, 'estimatePrice'])->name('ai.recycling.estimate-price');
    Route::post('/generate-description', [RecyclingAIController::class, 'generateDescription'])->name('ai.recycling.generate-description');
    Route::post('/optimize-process', [RecyclingAIController::class, 'optimizeProcess'])->name('ai.recycling.optimize-process');
    Route::get('/health', [RecyclingAIController::class, 'healthCheck'])->name('ai.recycling.health');
});

// =============================================================
// Product Routes (Front-office)
// =============================================================
Route::get('/shop/products', [ProductFrontController::class, 'index'])->name('front.products.index');
Route::get('/shop/products/{id}', [ProductFrontController::class, 'show'])->name('front.products.show');

// =============================================================
// Static pages
// =============================================================
Route::view('/contact', 'front.contact');

Route::get('/dashbored/collectionpoints', [CollectionPointController::class, 'index'])
    ->name('back.collectionpoints.overview');
Route::get('collectionpoints/export/csv', [CollectionPointController::class, 'exportCsv'])
    ->name('collectionpoints.export.csv');
Route::get('collectionpoints/export/pdf', [CollectionPointController::class, 'exportPdf'])
    ->name('collectionpoints.export.pdf');
Route::post('collectionpoints/import', [CollectionPointController::class, 'importCsv'])
    ->name('collectionpoints.import');
Route::resource('collectionpoints', CollectionPointController::class);

// =============================================================
// Users Routes
// =============================================================
Route::prefix('users')->group(function () {
    Route::get('/', [UserController::class, 'index']);
    Route::post('/', [UserController::class, 'store']);
    Route::get('/{id}', [UserController::class, 'show']);
    Route::put('/{id}', [UserController::class, 'update']);
    Route::delete('/{id}', [UserController::class, 'destroy']);
});

// =============================================================
// Auth Routes
// =============================================================
Route::get('/register', [AuthentifController::class, 'showRegisterForm'])->name('register.form');
Route::post('/register', [AuthentifController::class, 'register'])->name('register');

Route::get('/login', [AuthentifController::class, 'showLoginForm'])->name('login.form');
Route::post('/login', [AuthentifController::class, 'login'])->name('login');

// Google OAuth Routes
Route::get('/auth/google', [GoogleAuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');

Route::post('/logout', [AuthentifController::class, 'logout'])->name('logout');

// =============================================================
// Static pages (suite)
// =============================================================
Route::view('/recycling', 'front.recycling');

Route::get('/about', function () {
    $count = function (string $table): int {
        try {
            return \Illuminate\Support\Facades\DB::table($table)->count();
        } catch (\Throwable $e) {
            return 0;
        }
    };

    return view('front.about', [
        'stats' => [
            ['icon' => 'people-fill', 'label' => 'Registered members', 'value' => $count('users')],
            ['icon' => 'geo-alt-fill', 'label' => 'Collection points', 'value' => $count('collection_points')],
            ['icon' => 'megaphone-fill', 'label' => 'Campaigns launched', 'value' => $count('campaigns')],
            ['icon' => 'bag-check-fill', 'label' => 'Eco-products listed', 'value' => $count('products')],
            ['icon' => 'recycle', 'label' => 'Waste streams tracked', 'value' => $count('wastes')],
            ['icon' => 'hand-thumbs-up-fill', 'label' => 'Community participations', 'value' => $count('participations')],
        ],
    ]);
});

Route::view('/contact', 'front.contact');

Route::get('/dashbored/collectionpoints', [CollectionPointController::class, 'index'])
    ->name('back.collectionpoints.overview');
Route::resource('collectionpoints', CollectionPointController::class);

Route::get('/biodex/collectionpoints', [CollectionPointFrontController::class, 'index'])->name('front.collectionpoints.index');
Route::get('/biodex/collectionpoints/map', [CollectionPointFrontController::class, 'map'])->name('front.collectionpoints.map');
Route::get('/biodex/collectionpoints/{id}', [CollectionPointFrontController::class, 'show'])->name('front.collectionpoints.show');

// =============================================================
// Back-office campaigns
// =============================================================
Route::get('/back/campaigns', function () {
    return view('back.campaign.campaigns');
})->name('back.campaigns');

// Routes RESTful pour l'API des campagnes
Route::get('/campaigns/export/csv', [CampaignController::class, 'exportCsv'])->name('campaigns.export.csv');
Route::get('/campaigns/export/pdf', [CampaignController::class, 'exportPdf'])->name('campaigns.export.pdf');
Route::post('/campaigns/import', [CampaignController::class, 'importCsv'])->name('campaigns.import');
Route::resource('campaigns', CampaignController::class);