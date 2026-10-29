{{-- resources/views/front/orders/create.blade.php --}}
@extends('front.layout')

@section('content')
<div class="container py-4 py-lg-5 order-form-page">
    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('front.cart.index') }}">Cart</a></li>
            <li class="breadcrumb-item active" aria-current="page">Checkout</li>
        </ol>
    </nav>

    {{-- Stepper --}}
    <div class="checkout-steps mb-4">
        <div class="checkout-step is-done">
            <span class="checkout-step__dot"><i class="fas fa-check"></i></span>
            <span class="checkout-step__label">Cart</span>
        </div>
        <div class="checkout-step__line is-done"></div>
        <div class="checkout-step is-active">
            <span class="checkout-step__dot">2</span>
            <span class="checkout-step__label">Delivery</span>
        </div>
        <div class="checkout-step__line"></div>
        <div class="checkout-step">
            <span class="checkout-step__dot">3</span>
            <span class="checkout-step__label">Confirmation</span>
        </div>
    </div>

    {{-- Hero --}}
    <div class="checkout-hero mb-4">
        <div>
            <span class="checkout-hero__eyebrow"><i class="fas fa-lock"></i> Secure checkout</span>
            <h1 class="checkout-hero__title">Complete your order</h1>
            <p class="checkout-hero__subtitle">Review your items and enter your delivery details.</p>
        </div>
    </div>

    {{-- Alerts --}}
    @if ($errors->any())
        <div class="alert alert-danger soft-alert-danger" role="alert">
            <i class="fas fa-triangle-exclamation me-2"></i>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if (session('success'))
        <div class="alert alert-success soft-alert" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        </div>
    @endif

    <form action="{{ route('front.orders.store') }}" method="POST" class="needs-validation" novalidate>
        @csrf
        <input type="hidden" name="user_id" value="{{ auth()->id() }}">

        <div class="row g-4">
            {{-- ============ LEFT COLUMN: FORM ============ --}}
            <div class="col-12 col-lg-7">

                {{-- Shipping --}}
                <div class="panel mb-4">
                    <div class="panel-header">
                        <h5 class="panel-title"><span class="panel-icon"><i class="fas fa-location-dot"></i></span> Shipping address</h5>
                    </div>
                    <div class="panel-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="shipping_address" class="form-label">Full address</label>
                                <input type="text" name="shipping_address" id="shipping_address"
                                       value="{{ old('shipping_address', auth()->user()->address ?? '') }}"
                                       class="form-control @error('shipping_address') is-invalid @enderror"
                                       placeholder="e.g. 12 Main Street" required>
                                @error('shipping_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12 col-sm-6">
                                <label for="city" class="form-label">City <span class="text-muted fw-normal">(optional)</span></label>
                                <input type="text" name="city" id="city"
                                       value="{{ old('city', auth()->user()->city ?? '') }}"
                                       class="form-control" placeholder="Tunis">
                            </div>
                            <div class="col-12 col-sm-6">
                                <label for="postal_code" class="form-label">ZIP code <span class="text-muted fw-normal">(optional)</span></label>
                                <input type="text" name="postal_code" id="postal_code"
                                       value="{{ old('postal_code', auth()->user()->postal_code ?? '') }}"
                                       class="form-control" placeholder="1000">
                            </div>
                            <div class="col-12">
                                <label for="phone" class="form-label">Phone <span class="text-muted fw-normal">(optional)</span></label>
                                <input type="tel" name="phone" id="phone"
                                       value="{{ old('phone', auth()->user()->phone ?? '') }}"
                                       class="form-control" placeholder="+216 12 345 678">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Payment --}}
                <div class="panel mb-4">
                    <div class="panel-header">
                        <h5 class="panel-title"><span class="panel-icon"><i class="fas fa-credit-card"></i></span> Payment method</h5>
                    </div>
                    <div class="panel-body">
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="cash" checked required>
                            <span class="payment-option__icon"><i class="fas fa-money-bill-wave"></i></span>
                            <span class="payment-option__text">
                                <strong>Cash on delivery</strong>
                                <small>Pay in cash when your package arrives</small>
                            </span>
                            <span class="payment-option__check"><i class="fas fa-check-circle"></i></span>
                        </label>
                        @error('payment_method')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                </div>

                {{-- Recommendations --}}
                @if(isset($recommendations) && count($recommendations) > 0)
                    <div class="panel mb-4">
                        <div class="panel-header">
                            <h5 class="panel-title"><span class="panel-icon"><i class="fas fa-wand-magic-sparkles"></i></span> Recommended for you</h5>
                        </div>
                        <div class="panel-body">
                            <div class="row g-3">
                                @foreach($recommendations as $rec)
                                    <div class="col-12 col-md-6">
                                        <div class="recommendation-card">
                                            <div class="recommendation-card__badge"><i class="fas fa-star"></i> AI</div>
                                            <h6 class="recommendation-card__title">{{ $rec['name'] }}</h6>
                                            <p class="recommendation-card__price">{{ number_format($rec['price'], 2) }} DT</p>
                                            <p class="recommendation-card__stock"><i class="fas fa-box"></i> {{ $rec['stock'] }} in stock</p>
                                            <a href="{{ route('front.products.show', $rec['id']) }}" class="recommendation-card__btn">
                                                View product <i class="fas fa-arrow-right ms-1"></i>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- ============ RIGHT COLUMN: STICKY SUMMARY ============ --}}
            <div class="col-12 col-lg-5">
                <div class="panel summary-panel">
                    <div class="panel-header">
                        <h5 class="panel-title"><span class="panel-icon"><i class="fas fa-receipt"></i></span> Order summary</h5>
                        <span class="summary-count">{{ $cart->total_quantity }} item{{ $cart->total_quantity > 1 ? 's' : '' }}</span>
                    </div>
                    <div class="panel-body">

                        <ul class="summary-items">
                            @foreach($cart->items as $item)
                                <li class="summary-item">
                                    <div class="summary-item__thumb">
                                        @if(!empty($item->product->image))
                                            <img src="{{ asset('storage/'.$item->product->image) }}" alt="{{ $item->product->name }}">
                                        @else
                                            <i class="fas fa-box"></i>
                                        @endif
                                    </div>
                                    <div class="summary-item__info">
                                        <strong>{{ $item->product->name }}</strong>
                                        <small>{{ $item->quantity }} × {{ number_format($item->unit_price, 2) }} DT</small>
                                    </div>
                                    <div class="summary-item__price">{{ number_format($item->subtotal, 2) }} DT</div>
                                </li>
                            @endforeach
                        </ul>

                        <div class="summary-totals">
                            <div class="summary-line">
                                <span>Subtotal</span>
                                <span>{{ number_format($cart->total, 2) }} DT</span>
                            </div>
                            <div class="summary-line">
                                <span>Shipping</span>
                                <span class="text-success fw-semibold">Free</span>
                            </div>
                            <div class="summary-line summary-line--total">
                                <span>Total to pay</span>
                                <strong>{{ number_format($cart->total, 2) }} DT</strong>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-brand btn-block mt-3">
                            <i class="fas fa-lock me-1"></i>
                            Place order · {{ number_format($cart->total, 2) }} DT
                        </button>
                        <a href="{{ route('front.cart.index') }}" class="btn btn-brand-outline btn-block mt-2">
                            <i class="fas fa-arrow-left me-1"></i>Back to cart
                        </a>

                        <div class="trust-badges">
                            <div class="trust-badge"><i class="fas fa-shield-alt"></i><span>Secure<br>payment</span></div>
                            <div class="trust-badge"><i class="fas fa-truck"></i><span>Fast<br>delivery</span></div>
                            <div class="trust-badge"><i class="fas fa-undo"></i><span>14-day<br>returns</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    (function () {
        'use strict'
        document.querySelectorAll('.needs-validation').forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }
                form.classList.add('was-validated')
            }, false)
        })
    })()
</script>

<style>
    .order-form-page {
        --green-700: #047857; --green-600: #059669; --green-500: #10b981;
        --mint: #ecf8f2; --ink: #14231c; --muted: #5f7168; --line: #e3ebe6;
        --radius: 16px; color: var(--ink);
    }
    .order-form-page .breadcrumb { --bs-breadcrumb-divider-color: #aab5af; }
    .order-form-page .breadcrumb a { color: var(--green-700); text-decoration: none; }
    .order-form-page .breadcrumb a:hover { text-decoration: underline; }

    /* ---------- Stepper ---------- */
    .checkout-steps { display: flex; align-items: center; justify-content: center; gap: .5rem; flex-wrap: wrap; }
    .checkout-step { display: flex; align-items: center; gap: .5rem; opacity: .55; }
    .checkout-step__dot {
        width: 32px; height: 32px; border-radius: 50%; display: grid; place-items: center;
        background: #fff; border: 2px solid var(--line); font-weight: 700; font-size: .85rem; color: var(--muted);
    }
    .checkout-step__label { font-size: .85rem; font-weight: 600; }
    .checkout-step.is-active { opacity: 1; }
    .checkout-step.is-active .checkout-step__dot { background: var(--green-700); border-color: var(--green-700); color: #fff; }
    .checkout-step.is-done { opacity: 1; }
    .checkout-step.is-done .checkout-step__dot { background: var(--mint); border-color: var(--green-500); color: var(--green-700); }
    .checkout-step__line { flex: 1; max-width: 60px; height: 2px; background: var(--line); }
    .checkout-step__line.is-done { background: var(--green-500); }
    @media (max-width: 575.98px) { .checkout-step__label { display: none; } }

    /* ---------- Hero ---------- */
    .checkout-hero {
        padding: 1.5rem 1.75rem; border-radius: var(--radius);
        background:
            radial-gradient(120% 160% at 100% 0%, rgba(255,255,255,.28) 0%, rgba(255,255,255,0) 45%),
            linear-gradient(120deg, #047857 0%, #059669 55%, #10b981 100%);
        color: #fff; box-shadow: 0 16px 34px rgba(4,120,87,.22);
    }
    .checkout-hero__eyebrow {
        display: inline-flex; align-items: center; gap: .4rem;
        font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .08em;
        padding: .3rem .7rem; border-radius: 999px; background: rgba(255,255,255,.18); margin-bottom: .6rem;
    }
    .checkout-hero__title { font-size: clamp(1.5rem, 3vw, 2rem); font-weight: 800; margin: 0 0 .25rem; }
    .checkout-hero__subtitle { margin: 0; opacity: .9; font-size: .95rem; }

    /* ---------- Panels ---------- */
    .panel {
        background: #fff; border: 1px solid var(--line); border-radius: var(--radius);
        box-shadow: 0 10px 30px rgba(20,35,28,.06); overflow: hidden;
    }
    .panel-header {
        padding: 1rem 1.5rem; border-bottom: 1px solid var(--line); background: #fafdfb;
        display: flex; align-items: center; justify-content: space-between; gap: .75rem;
    }
    .panel-title { margin: 0; font-size: 1rem; font-weight: 700; display: flex; align-items: center; gap: .6rem; }
    .panel-icon {
        width: 32px; height: 32px; border-radius: 9px; background: var(--mint);
        color: var(--green-700); display: grid; place-items: center; font-size: .85rem;
    }
    .panel-body { padding: 1.5rem; }
    .summary-count {
        font-size: .75rem; font-weight: 600; color: var(--green-700);
        background: var(--mint); padding: .3rem .7rem; border-radius: 999px;
    }

    /* ---------- Form ---------- */
    .order-form-page .form-label { font-weight: 600; font-size: .85rem; color: var(--ink); margin-bottom: .35rem; }
    .order-form-page .form-control, .order-form-page .form-select {
        border-color: var(--line); border-radius: 10px; padding: .65rem .9rem;
    }
    .order-form-page .form-control:focus, .order-form-page .form-select:focus {
        border-color: var(--green-500); box-shadow: 0 0 0 .2rem rgba(16,185,129,.18);
    }

    /* ---------- Payment ---------- */
    .payment-option {
        display: flex; align-items: center; gap: .9rem; padding: 1rem;
        border: 2px solid var(--line); border-radius: 12px; cursor: pointer;
        transition: all .2s ease; position: relative;
    }
    .payment-option input { position: absolute; opacity: 0; pointer-events: none; }
    .payment-option:hover { border-color: var(--green-500); }
    .payment-option:has(input:checked) { border-color: var(--green-600); background: var(--mint); }
    .payment-option__icon {
        width: 44px; height: 44px; border-radius: 11px; display: grid; place-items: center;
        background: #fff; border: 1px solid var(--line); color: var(--green-700); font-size: 1.1rem; flex-shrink: 0;
    }
    .payment-option:has(input:checked) .payment-option__icon { background: var(--green-700); color: #fff; border-color: var(--green-700); }
    .payment-option__text { display: flex; flex-direction: column; flex: 1; }
    .payment-option__text strong { font-size: .92rem; }
    .payment-option__text small { color: var(--muted); font-size: .78rem; }
    .payment-option__check { color: var(--green-600); font-size: 1.2rem; opacity: 0; transition: opacity .2s; }
    .payment-option:has(input:checked) .payment-option__check { opacity: 1; }

    /* ---------- Summary ---------- */
    .summary-panel { position: sticky; top: 1.5rem; }
    .summary-items { list-style: none; padding: 0; margin: 0; max-height: 320px; overflow-y: auto; }
    .summary-item {
        display: flex; align-items: center; gap: .75rem; padding: .75rem 0;
        border-bottom: 1px dashed var(--line);
    }
    .summary-item:last-child { border-bottom: 0; }
    .summary-item__thumb {
        width: 48px; height: 48px; border-radius: 10px; background: var(--mint);
        display: grid; place-items: center; color: var(--green-700); flex-shrink: 0; overflow: hidden;
    }
    .summary-item__thumb img { width: 100%; height: 100%; object-fit: cover; }
    .summary-item__info { flex: 1; display: flex; flex-direction: column; min-width: 0; }
    .summary-item__info strong { font-size: .88rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .summary-item__info small { color: var(--muted); font-size: .78rem; }
    .summary-item__price { font-weight: 700; font-size: .9rem; color: var(--green-700); white-space: nowrap; }

    .summary-totals { margin-top: 1rem; padding-top: 1rem; border-top: 2px solid var(--line); }
    .summary-line { display: flex; justify-content: space-between; padding: .35rem 0; font-size: .9rem; color: var(--muted); }
    .summary-line--total {
        margin-top: .5rem; padding-top: .75rem; border-top: 1px dashed var(--line);
        font-size: 1.05rem; color: var(--ink); align-items: center;
    }
    .summary-line--total strong { color: var(--green-700); font-size: 1.4rem; }

    /* ---------- Buttons ---------- */
    .btn-brand {
        background: var(--green-700); border: 1px solid var(--green-700); color: #fff;
        font-weight: 600; padding: .8rem 1.25rem; border-radius: 10px; transition: all .2s ease;
    }
    .btn-brand:hover, .btn-brand:focus-visible { background: var(--green-600); border-color: var(--green-600); color: #fff; box-shadow: 0 6px 16px rgba(4,120,87,.25); }
    .btn-brand-outline {
        background: #fff; border: 1px solid var(--line); color: var(--ink);
        font-weight: 600; border-radius: 10px; padding: .7rem 1.25rem; transition: all .2s ease;
    }
    .btn-brand-outline:hover { background: #f6faf8; border-color: var(--green-500); color: var(--green-700); }
    .btn-block { width: 100%; }

    .trust-badges {
        display: grid; grid-template-columns: repeat(3, 1fr); gap: .5rem;
        margin-top: 1.25rem; padding-top: 1rem; border-top: 1px solid var(--line);
    }
    .trust-badge {
        display: flex; flex-direction: column; align-items: center; gap: .35rem;
        font-size: .72rem; color: var(--muted); text-align: center; line-height: 1.2;
    }
    .trust-badge i { color: var(--green-500); font-size: 1.1rem; }

    /* ---------- Alerts ---------- */
    .soft-alert { border: 0; border-radius: 12px; background: var(--mint); color: var(--green-700); }
    .soft-alert-danger { border: 0; border-radius: 12px; background: #fee2e2; color: #991b1b; }

    /* ---------- Recommendations ---------- */
    .recommendation-card {
        background: #fff; border: 1px solid var(--green-500); border-radius: 12px;
        padding: 1rem; position: relative; transition: all .3s ease;
    }
    .recommendation-card:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(16,185,129,.2); }
    .recommendation-card__badge {
        position: absolute; top: -10px; right: 10px;
        background: linear-gradient(135deg, var(--green-500), var(--green-600));
        color: #fff; padding: .2rem .65rem; border-radius: 20px;
        font-size: .7rem; font-weight: 700; display: flex; align-items: center; gap: .25rem;
    }
    .recommendation-card__title { font-weight: 700; margin: 0 0 .4rem; font-size: .95rem; }
    .recommendation-card__price { color: var(--green-700); font-weight: 800; font-size: 1.15rem; margin: 0 0 .2rem; }
    .recommendation-card__stock { color: var(--muted); font-size: .8rem; margin: 0 0 .6rem; }
    .recommendation-card__btn {
        display: inline-block; padding: .4rem .9rem; background: var(--green-700);
        color: #fff; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: .82rem;
    }
    .recommendation-card__btn:hover { background: var(--green-600); color: #fff; }

    @media (max-width: 575.98px) {
        .panel-body { padding: 1.15rem; }
        .summary-panel { position: static; }
        .checkout-hero { padding: 1.25rem; }
    }
</style>
@endsection