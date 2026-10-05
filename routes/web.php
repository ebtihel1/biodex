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

// Frontoffice Routes (Public - Prefixed with /biodex)
Route::prefix('biodex')->name('front.')->group(function () {
    // Donations (Frontoffice)
    Route::get('donations', [DonationController::class, 'index'])->name('donations.index');
    Route::get('donations/create', [DonationController::class, 'create'])->name('donations.create');
    Route::post('donations', [DonationController::class, 'store'])->name('donations.store');
    Route::get('donations/{donation}', [DonationController::class, 'show'])->name('donations.show');
    Route::delete('donations/{donation}', [DonationController::class, 'destroy'])->name('donations.destroy');
    Route::get('donations/{donation}/edit', [DonationController::class, 'edit'])->name('donations.edit');
    Route::put('donations/{donation}', [DonationController::class, 'update'])->name('donations.update');
    Route::post('analyze-sentiment', [DonationController::class, 'analyzeSentiment'])->name('donations.analyze-sentiment');
    // Orders (Frontoffice)
    Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/create', [OrderController::class, 'create'])->name('orders.create');
    Route::post('orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('orders/{order}/edit', [OrderController::class, 'edit'])->name('orders.edit');
    Route::put('orders/{order}', [OrderController::class, 'update'])->name('orders.update');
    Route::delete('orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');
    // Reservations (Frontoffice)
    Route::get('reservations', [ReservationController::class, 'index'])->name('reservations.index');
    Route::get('reservations/create', [ReservationController::class, 'create'])->name('reservations.create');
    Route::post('reservations', [ReservationController::class, 'store'])->name('reservations.store');
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

    //Waste Routes
    Route::get('wastes', [WasteController::class, 'index'])->name('wastes.index');
    Route::get('wastes/create', [WasteController::class, 'create'])->name('wastes.create');
    Route::post('wastes', [WasteController::class, 'store'])->name('wastes.store');
    Route::get('wastes/{id}/edit', [WasteController::class, 'edit'])->name('wastes.edit');
    Route::put('wastes/{id}', [WasteController::class, 'update'])->name('wastes.update');
    Route::delete('wastes/{id}', [WasteController::class, 'destroy'])->name('wastes.destroy');
    Route::get('wastes/{id}', [WasteController::class, 'show'])->name('wastes.show');

    //Waste Category Routes
    Route::get('waste-categories', [WasteCategoryController::class, 'index'])
        ->name('waste_categories.index');
    Route::get('waste-categories/create', [WasteCategoryController::class, 'create'])
        ->name('waste_categories.create');
    Route::post('waste-categories', [WasteCategoryController::class, 'store'])
        ->name('waste_categories.store');
    Route::get('waste-categories/{id}/edit', [WasteCategoryController::class, 'edit'])
        ->name('waste_categories.edit');
    Route::put('waste-categories/{id}', [WasteCategoryController::class, 'update'])
        ->name('waste_categories.update');
    Route::delete('waste-categories/{id}', [WasteCategoryController::class, 'destroy'])
        ->name('waste_categories.destroy');
    Route::get('waste-categories/{id}', [WasteCategoryController::class, 'show'])
        ->name('waste_categories.show');

    // Recycling Process Routes (Back-office)
    Route::resource('recyclingprocesses', RecyclingProcessController::class);

    // zied Product Routes (Back-office)
    Route::resource('products', ProductController::class);
    Route::post('products/{id}/toggle-availability', [ProductController::class, 'toggleAvailability'])
        ->name('products.toggle-availability');

    Route::view('dashboard', 'back.home')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');
    
    //Dashboard Route
    Route::get('/back/home', function () {
    $categories = \App\Models\WasteCategory::all();
    $wastes = \App\Models\Waste::all();
    $totalWastes = $wastes->count();
    $wasteStats = $categories->map(function ($category) use ($wastes, $totalWastes) {
        $count = $wastes->where('waste_category_id', $category->id)->count();
        $percentage = $totalWastes > 0 ? ($count / $totalWastes) * 100 : 0;
        return [
            'name' => $category->name,
            'percentage' => round($percentage, 1),
        ];
    });
        return view('back.home', compact('categories', 'wastes', 'wasteStats'));
    })->middleware(['auth', 'verified'])->name('back.home');

    Route::get('/collection-ai/train/{id}', [CollectionAIController::class, 'train']);
    Route::get('/collection-ai/predict/{id}', [CollectionAIController::class, 'predict']);

    Route::get('/collectionpoints/predictions', [CollectionPointController::class, 'predictions'])
        ->name('collectionpoints.predictions');

    // Donation Routes
    Route::resource('donations', DonationController::class)->except(['edit', 'update']);

    // Order Routes
    Route::resource('orders', OrderController::class)->except(['edit', 'update']);

    // Reservation Routes
    Route::resource('reservations', ReservationController::class);

    Route::view('dashboard', 'dashboard')->name('dashboard');

    // Back-end Routes (Back - Prefixed with /back)
    Route::prefix('back')->name('back.')->group(function () {
        Route::get('home', function () {
            $totalDonations = \App\Models\Donation::count();
            $totalOrders = \App\Models\Order::count();
            $totalReservations = \App\Models\Reservation::count();
            return view('back.home', compact('totalDonations', 'totalOrders', 'totalReservations'));
        })->name('home');
        Route::prefix('home')->group(function () {
            // Donations (Back-end)
            Route::get('donations', [DonationController::class, 'index'])->name('donations.index');
            Route::get('donations/create', [DonationController::class, 'create'])->name('donations.create');
            Route::post('donations', [DonationController::class, 'store'])->name('donations.store');
            Route::get('donations/{donation}', [DonationController::class, 'show'])->name('donations.show');
            Route::delete('donations/{donation}', [DonationController::class, 'destroy'])->name('donations.destroy');
            Route::put('donations/{donation}', [DonationController::class, 'update'])->name('donations.update');
            Route::get('donations/{donation}/edit', [DonationController::class, 'edit'])->name('donations.edit');
            Route::post('donations/analyze-sentiment', [DonationController::class, 'analyzeSentiment'])->name('donations.analyze-sentiment');
            // Orders (Back-end)
            Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
            Route::get('orders/create', [OrderController::class, 'create'])->name('orders.create');
            Route::post('orders', [OrderController::class, 'store'])->name('orders.store');
            Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
            Route::get('orders/{order}/edit', [OrderController::class, 'edit'])->name('orders.edit');
            Route::put('orders/{order}', [OrderController::class, 'update'])->name('orders.update');
            Route::delete('orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');
            // Reservations (Back-end)
            Route::get('reservations', [ReservationController::class, 'index'])->name('reservations.index');
            Route::get('reservations/create', [ReservationController::class, 'create'])->name('reservations.create');
            Route::post('reservations', [ReservationController::class, 'store'])->name('reservations.store');
            Route::get('reservations/{reservation}', [ReservationController::class, 'show'])->name('reservations.show');
            Route::get('reservations/{reservation}/edit', [ReservationController::class, 'edit'])->name('reservations.edit');
            Route::put('reservations/{reservation}', [ReservationController::class, 'update'])->name('reservations.update');
            Route::delete('reservations/{reservation}', [ReservationController::class, 'destroy'])->name('reservations.destroy');
       
        });
    });
    Route::get('dashboard', function () {
        return redirect()->route('back.home');
    })->name('dashboard');


//frontoffice campaigns routes
Route::get('/campaignsFront', [CampaignController::class, 'frontIndex'])->name('campaigns.front');

// Frontoffice Waste Category Routes
Route::get('/categories', [FrontWasteCategoryController::class, 'index'])->name('front.waste-categories.index');
Route::get('/categories/{id}', [FrontWasteCategoryController::class, 'show'])->name('front.waste-categories.show');

// Frontoffice Waste Routes
Route::get('/wastess', [FrontWasteController::class, 'index'])->name('front.wastes.index');
Route::get('/wastess/{id}', [FrontWasteController::class, 'show'])->name('front.wastes.show');       


Route::post('/ai/predict', [AIController::class, 'predictWaste'])->name('ai.predict');
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

// Product Routes (Front-office) - Utiliser un chemin différent pour éviter les conflits
Route::get('/shop/products', [ProductFrontController::class, 'index'])->name('front.products.index');
Route::get('/shop/products/{id}', [ProductFrontController::class, 'show'])->name('front.products.show');

// Redirection supprimée pour permettre l'accès au back-office


Route::view('/contact', 'front.contact');

Route::get('/dashbored/collectionpoints', action: [CollectionPointController::class, 'index'])->name('back.home');
Route::resource('collectionpoints', CollectionPointController::class);

Route::get('/biodex/collectionpoints', [CollectionPointFrontController::class, 'index'])->name('front.collectionpoints.index');
Route::get('/biodex/collectionpoints/{id}', [CollectionPointFrontController::class, 'show'])->name('front.collectionpoints.show');

// Route pour la page de gestion des campagnes dans le back-office
Route::get('/back/campaigns', function () {
    return view('back.campaign.campaigns');
})->name('back.campaigns');

// Routes RESTful pour l'API des campagnes
Route::resource('campaigns', CampaignController::class);

//Profil
Route::get('/profile', function () {
    $user = Auth::user();
    return view('front.profil.profile', compact('user'));
})->middleware('auth')->name('profile.view');

//Users Routes
Route::prefix('users')->group(function () {
    Route::get('/', [UserController::class, 'index']);
    Route::post('/', [UserController::class, 'store']);
    Route::get('/{id}', [UserController::class, 'show']);
    Route::put('/{id}', [UserController::class, 'update']);
    Route::delete('/{id}', [UserController::class, 'destroy']);
});    
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

// Product Routes (Front-office) - Utiliser un chemin différent pour éviter les conflits
Route::get('/shop/products', [ProductFrontController::class, 'index'])->name('front.products.index');
Route::get('/shop/products/{id}', [ProductFrontController::class, 'show'])->name('front.products.show');




// Auth Routes
Route::get('/register', [AuthentifController::class, 'showRegisterForm'])->name('register.form');
Route::post('/register', [AuthentifController::class, 'register'])->name('register');

Route::get('/login', [AuthentifController::class, 'showLoginForm'])->name('login.form');
Route::post('/login', [AuthentifController::class, 'login'])->name('login');

// Google OAuth Routes
Route::get('/auth/google', [GoogleAuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');

Route::post('/logout', [AuthentifController::class, 'logout'])->name('logout');

Route::get('/dashboard', function() {
    return view('dashboard');
})->middleware('auth');


Route::view('/recycling', 'front.recycling');
Route::view('/about', 'front.about');
Route::view('/contact', 'front.contact');

Route::get('/dashbored/collectionpoints', action: [CollectionPointController::class, 'index'])->name('back.home');
Route::resource('collectionpoints', CollectionPointController::class);

Route::get('/biodex/collectionpoints', [CollectionPointFrontController::class, 'index'])->name('front.collectionpoints.index');
Route::get('/biodex/collectionpoints/{id}', [CollectionPointFrontController::class, 'show'])->name('front.collectionpoints.show');

// Route pour la page de gestion des campagnes dans le back-office
Route::get('/back/campaigns', function () {
    return view('back.campaign.campaigns');
})->name('back.campaigns');

// Routes RESTful pour l'API des campagnes
Route::resource('campaigns', CampaignController::class);
