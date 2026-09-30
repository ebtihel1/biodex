<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>Biodex - Campaigns</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<style>
body {
    font-family: 'Roboto', sans-serif;
    background-color: #f8fff8;
    margin: 0;
    overflow-x: hidden;
}

/* Hero Section */
.hero-campaign {
    height: 70vh;
    background: url("{{ asset('images/EarthFront.png') }}") no-repeat center center/cover;
    position: relative;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
}
.hero-campaign::before {
    content: "";
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(2,40,22,0.8), rgba(0,0,0,0.6));
    z-index: 1;
}
.hero-campaign .content {
    position: relative;
    z-index: 2;
    max-width: 800px;
}
.hero-campaign h1 {
    font-size: 4rem;
    font-weight: 800;
    color: #a7f3d0;
    text-shadow: 0 4px 8px rgba(0,0,0,0.4);
    margin-bottom: 20px;
}
.hero-campaign p {
    font-size: 1.5rem;
    font-weight: 300;
    margin-bottom: 30px;
}

/* Campaign Cards */
.campaign-card {
    border: none;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 6px 25px rgba(0,128,0,0.15);
    transition: transform 0.4s ease, box-shadow 0.4s ease, border 0.3s ease;
    background: white;
    position: relative;
}
.campaign-card:hover {
    transform: translateY(-12px);
    box-shadow: 0 12px 40px rgba(0,128,0,0.25);
    border: 1px solid #198754;
}
.campaign-card img {
    width: 100%;
    height: 250px;
    object-fit: cover;
    transition: transform 0.4s ease;
}
.campaign-card:hover img {
    transform: scale(1.05);
}
.card-body { padding: 20px; }
.card-body h5 { color: #198754; font-weight: 700; font-size: 1.4rem; margin-bottom: 10px; }
.card-body p { color: #6c757d; font-size: 1rem; margin-bottom: 15px; }
.campaign-dates { font-size: 0.9rem; color: #6c757d; margin-bottom: 15px; }
.campaign-status { position: absolute; top: 15px; right: 15px; padding: 5px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: bold; text-transform: uppercase; }
.status-active { background-color: #d4edda; color: #198754; }
.status-ended { background-color: #f8d7da; color: #721c24; }
.btn-success { border-radius: 50px; padding: 10px 25px; font-weight: 600; transition: background-color 0.3s ease, transform 0.3s ease; }
.btn-success:hover { background-color: #146c43; transform: scale(1.05); }

/* Filter Section */
.filter-section {
    background: #e6f4ea;
    padding: 20px;
    border-radius: 15px;
    margin-bottom: 40px;
    box-shadow: 0 4px 15px rgba(0,128,0,0.1);
}
.filter-section .btn { margin-right: 10px; border-radius: 30px; }


#campaignModal .modal-content {
  border-radius: 20px;
  overflow: hidden;
  background-color: #fff;
}

#campaignModal img {
  object-fit: cover;
  height: 100%;
  width: 100%;
}

#campaignModal .modal-header {
  background: linear-gradient(135deg, #198754, #145c3e);
  border-bottom: none;
}

#campaignModal .btn-success {
  border-radius: 50px;
  font-weight: 600;
  transition: all 0.3s;
}

#campaignModal .btn-success:hover {
  background-color: #157347;
  transform: scale(1.05);
}

#campaignModal .btn-outline-secondary:hover {
  background-color: #e9ecef;
}

/* Footer */
footer {
    background-color: #198754;
    color: white;
    text-align: center;
    padding: 20px 0;
    font-size: 1rem;
    margin-top: 60px;
    box-shadow: 0 -4px 10px rgba(0,0,0,0.1);
}
.image-error { background-color: #f8d7da; color: #721c24; text-align: center; height: 250px; display: flex; align-items: center; justify-content: center; font-size: 1rem; }

/* Navbar */
.navbar { background-color: transparent !important; transition: background-color 0.4s ease; position: fixed; width: 100%; top: 0; z-index: 1000; }
.navbar.scrolled { background-color: #198754 !important; box-shadow: 0 4px 15px rgba(0,0,0,0.2); }
.navbar-brand { font-weight: bold; color: #fff !important; }
.nav-link { color: #fff !important; transition: color 0.3s ease; }
.nav-link:hover { color: #a7f3d0 !important; text-decoration: underline; }

/* Modal */
#campaignModal .list-group-item { border: none; padding: 0.75rem 0; }
#campaignModal .btn-success { border-radius: 50px; padding: 10px 25px; font-weight: 600; }
#campaignModalImage { object-fit: cover; height: 100%; width: 100%; }
@media(max-width:768px){
    #campaignModal .row { flex-direction: column; }
    #campaignModalImage { height: 250px; width: 100%; }
}

/* Responsive */
@media (max-width: 768px){
    .hero-campaign h1 { font-size: 2.8rem; }
    .hero-campaign p { font-size: 1.2rem; }
    .campaign-card img { height: 200px; }
    .filter-section { text-align: center; }
}
</style>
</head>
<body>
@extends('front.navbar')

<!-- Hero -->
<section class="hero-campaign">
    <div class="content">
        <h1>Join Environmental Campaigns</h1>
        <p>Be part of community-led initiatives turning waste into hope for a sustainable future.</p>
        <a href="#campaign-list" class="btn btn-success btn-lg">Explore Now</a>
    </div>
</section>

<!-- Campaign List -->
<main class="container py-5">
    <!-- Filter Section -->
<section class="filter-section mb-5">
    <h4 class="text-center text-success fw-bold mb-4">Filter Campaigns by Status</h4>
    <div class="d-flex justify-content-center flex-wrap">
        <button class="btn btn-outline-success active" data-filter="all">All</button>
        <button class="btn btn-outline-success" data-filter="active">Active</button>
        <button class="btn btn-outline-success" data-filter="draft">Draft</button>
        <button class="btn btn-outline-success" data-filter="closed">Closed</button>
    </div>
</section>

    <h2 class="text-center fw-bold text-success mb-5">Our Sensitization Campaigns</h2>
    <div class="row g-4" id="campaign-list"></div>
</main>

<!-- Campaign Modal -->
<!-- Campaign Modal (NEW DESIGN) -->
<div class="modal fade" id="campaignModal" tabindex="-1" aria-labelledby="campaignModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">

      <!-- Header -->
      <div class="modal-header border-0 bg-success text-white py-3">
        <h5 class="modal-title fw-bold ms-2" id="campaignModalLabel">Campaign Title</h5>
        <button type="button" class="btn-close btn-close-white me-3" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <!-- Body -->
      <div class="modal-body p-0">
        <div class="row g-0">
          
          <!-- Left Image -->
          <div class="col-md-6 position-relative">
            <img src="" id="campaignModalImage" class="img-fluid w-100 h-100 object-fit-cover" alt="Campaign Image">
            <div class="position-absolute bottom-0 start-0 bg-dark bg-opacity-50 text-white p-3 w-100">
              <p class="mb-0"><i class="bi bi-calendar-event me-2"></i><span id="campaignModalDates"></span></p>
            </div>
          </div>

          <!-- Right Content -->
          <div class="col-md-6 p-4">
            <h4 class="fw-bold text-success mb-3" id="campaignModalTitle">Campaign Title</h4>
            <p class="text-muted mb-4" id="campaignModalDescription"></p>

            <div class="mb-3">
              <p><strong>Type :</strong> <span id="campaignModalType" class="text-secondary"></span></p>
              <p><strong>City :</strong> <span id="campaignModalCity" class="text-secondary"></span></p>
              <p><strong>Region :</strong> <span id="campaignModalRegion" class="text-secondary"></span></p>
              <p><strong>Participants :</strong> <span id="campaignModalParticipants" class="text-secondary"></span></p>
              <p><strong>Registration Deadline :</strong> <span id="campaignModalDeadline" class="text-secondary"></span></p>
            </div>

            <div class="mt-4 d-flex gap-3">
              <a href="#" class="btn btn-success" id="campaignModalBtn">
    <i class="bi bi-person-plus me-2"></i><span id="campaignModalBtnText">Participate</span>
</a>

              <button class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">
                <i class="bi bi-x-circle me-2"></i>Close
              </button>
            </div>
          </div>

        </div>
      </div>

    </div>
  </div>
</div>

<!-- Footer -->
<footer>
    © {{ date('Y') }} Biodex — Together for a Cleaner Future 🌱
</footer>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function () {
    // Quand on ouvre le modal -> vérifier si l'utilisateur a déjà rejoint
    $('#campaignModal').on('show.bs.modal', function (e) {
        const campaignId = $(e.relatedTarget).data('id');
        $('#campaignModalBtn').attr('data-id', campaignId);

        @auth
        $.get(`/campaigns/${campaignId}/check`, function (res) {
            if (res.joined) {
                $('#campaignModalBtn').removeClass('btn-success').addClass('btn-outline-success');
                $('#campaignModalBtn i').removeClass('bi-person-plus').addClass('bi-check-circle');
                $('#campaignModalBtnText').text('Joined ✓');
            } else {
                $('#campaignModalBtn').removeClass('btn-outline-success').addClass('btn-success');
                $('#campaignModalBtn i').removeClass('bi-check-circle').addClass('bi-person-plus');
                $('#campaignModalBtnText').text('Participate');
            }
        });
        @endauth
    });

    // Quand on clique sur le bouton Participate / Quit
    $('#campaignModalBtn').on('click', function (e) {
        e.preventDefault();
        const btn = $(this);
        const campaignId = btn.data('id');

        @guest
            window.location.href = "{{ route('login.form') }}";
            return;
        @endguest

       $.ajax({
    url: `/campaigns/${campaignId}/toggle`,
    type: 'POST',
    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
    success: function (res) {
        if (res.joined) {
            btn.removeClass('btn-success').addClass('btn-outline-success');
            btn.find('i').removeClass('bi-person-plus').addClass('bi-check-circle');
            $('#campaignModalBtnText').text('Joined ✓');
        } else {
            btn.removeClass('btn-outline-success').addClass('btn-success');
            btn.find('i').removeClass('bi-check-circle').addClass('bi-person-plus');
            $('#campaignModalBtnText').text('Participate');
        }
        $('#campaignModalParticipants').text(res.participants);
    },
    error: function(xhr) {
        console.log('Erreur complète :', xhr.responseText);
        alert(xhr.responseText);
    }
});

    });
});
</script>

<script>
document.addEventListener('scroll', function () {
    const navbar = document.querySelector('.navbar');
    navbar.classList.toggle('scrolled', window.scrollY > 50);
});

$(document).ready(function () {
    // Fetch campaigns from backend
    $.ajax({
        url: '{{ url("campaigns") }}',
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            const campaignList = $('#campaign-list');
            campaignList.empty();

            const campaignConfig = {
                'clean_city': { icon: 'bi-person-plus', buttonText: 'Participate', color: '#198754' },
                'tree_planting': { icon: 'bi-tree', buttonText: 'Join Now', color: '#28a745' },
                'recycling_awareness': { icon: 'bi-recycle', buttonText: 'Support', color: '#146c43' }
            };
            const defaultConfig = { icon: 'bi-hand-thumbs-up', buttonText: 'Get Involved', color: '#198754' };

            data.forEach(campaign => {
                const imageSrc = campaign.image || '{{ asset("images/default-campaign.jpg") }}';
                const config = campaignConfig[campaign.type] || defaultConfig;
                const statusClass = campaign.status === 'active' ? 'status-active' : 'status-ended';
                const statusText = campaign.status ? campaign.status.toUpperCase() : 'UNKNOWN';

                const cardHtml = `
                <div class="col-md-4 campaign-item" data-type="${campaign.type || 'other'}">
                    <div class="campaign-card">
                        <img src="${imageSrc}" alt="${campaign.title}" onerror="this.outerHTML='<div class=\\'image-error\\'>Image not found</div>'">
                        <span class="campaign-status ${statusClass}">${statusText}</span>
                        <div class="card-body">
                            <h5><i class="bi ${config.icon} me-2" style="color:${config.color}"></i>${campaign.title}</h5>
                            <p class="text-muted">${campaign.description || 'No description available'}</p>
                            <div class="campaign-dates"><i class="bi bi-calendar-event me-2"></i>${campaign.start_date || 'N/A'} - ${campaign.end_date || 'N/A'}</div>
                            <a href="#" class="btn btn-outline-success btn-sm see-more-btn" 
                                data-bs-toggle="modal" data-bs-target="#campaignModal"
                                data-title="${campaign.title}"
                                data-description="${campaign.description}"
                                data-image="${imageSrc}"
                                data-type="${campaign.type}"
                                data-city="${campaign.city || 'N/A'}"
                                data-region="${campaign.region || 'N/A'}"
                                data-participants="${campaign.participants_count || 0}"
                                data-deadline="${campaign.deadline_registration || 'N/A'}"
                                data-dates="${campaign.start_date || 'N/A'} - ${campaign.end_date || 'N/A'}"
                                data-id="${campaign.id}"> See More </a>
                        </div>
                    </div>
                </div>`;
                campaignList.append(cardHtml);
            });
        },
        error: function() {
            $('#campaign-list').html('<p class="text-danger text-center">Failed to load campaigns. Please try again later.</p>');
        }
    });

    // Filter functionality
    $('.filter-section .btn').on('click', function() {
        $('.filter-section .btn').removeClass('active');
        $(this).addClass('active');
        const filter = $(this).data('filter');
        if (filter === 'all') {
            $('.campaign-item').fadeIn(300);
        } else {
            $('.campaign-item').fadeOut(300);
            $(`.campaign-item[data-type="${filter}"]`).fadeIn(300);
        }
    });

    // Fill modal dynamically
    $(document).on('click', '.see-more-btn', function() {
        const btn = $(this);
        $('#campaignModalLabel').text(btn.data('title'));
        $('#campaignModalDescription').text(btn.data('description'));
        $('#campaignModalImage').attr('src', btn.data('image'));
        $('#campaignModalType').text(btn.data('type'));
        $('#campaignModalCity').text(btn.data('city'));
        $('#campaignModalRegion').text(btn.data('region'));
        $('#campaignModalParticipants').text(btn.data('participants'));
        $('#campaignModalDeadline').text(btn.data('deadline'));
        $('#campaignModalDates').text(btn.data('dates'));
        $('#campaignModalBtn').attr('href', `/campaigns/${btn.data('id')}`);
    });
});
</script><script>
$(document).ready(function () {
    // Fetch campaigns from backend
    $.ajax({
        url: '{{ url("campaigns") }}',
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            const campaignList = $('#campaign-list');
            campaignList.empty();

            data.forEach(campaign => {
                const imageSrc = campaign.image || '{{ asset("images/default-campaign.jpg") }}';
                const status = campaign.status ? campaign.status.toLowerCase() : 'unknown';
                const statusClass = 
                    status === 'active' ? 'status-active' :
                    status === 'closed' ? 'status-ended' :
                    status === 'draft' ? 'bg-warning text-dark' :
                    'bg-secondary text-white';

                const statusText = campaign.status ? campaign.status.toUpperCase() : 'UNKNOWN';

                const cardHtml = `
                <div class="col-md-4 campaign-item" data-status="${status}">
                    <div class="campaign-card">
                        <img src="${imageSrc}" alt="${campaign.title}" 
                            onerror="this.outerHTML='<div class=\\'image-error\\'>Image not found</div>'">
                        <span class="campaign-status ${statusClass}">${statusText}</span>
                        <div class="card-body">
                            <h5 class="fw-bold text-success">${campaign.title}</h5>
                            <p class="text-muted">${campaign.description || 'No description available'}</p>
                            <div class="campaign-dates">
                                <i class="bi bi-calendar-event me-2"></i>
                                ${campaign.start_date || 'N/A'} - ${campaign.end_date || 'N/A'}
                            </div>
                            <a href="#" class="btn btn-outline-success btn-sm see-more-btn" 
                                data-bs-toggle="modal" data-bs-target="#campaignModal"
                                data-title="${campaign.title}"
                                data-description="${campaign.description}"
                                data-image="${imageSrc}"
                                data-type="${campaign.type || 'N/A'}"
                                data-city="${campaign.city || 'N/A'}"
                                data-region="${campaign.region || 'N/A'}"
                                data-participants="${campaign.participants_count || 0}"
                                data-deadline="${campaign.deadline_registration || 'N/A'}"
                                data-dates="${campaign.start_date || 'N/A'} - ${campaign.end_date || 'N/A'}"
                                data-id="${campaign.id}"> See More </a>
                        </div>
                    </div>
                </div>`;
                campaignList.append(cardHtml);
            });
        },
        error: function() {
            $('#campaign-list').html('<p class="text-danger text-center">Failed to load campaigns. Please try again later.</p>');
        }
    });

    // === FILTER FUNCTIONALITY BY STATUS ===
    $('.filter-section .btn').on('click', function() {
        $('.filter-section .btn').removeClass('active');
        $(this).addClass('active');
        const filter = $(this).data('filter');

        if (filter === 'all') {
            $('.campaign-item').fadeIn(300);
        } else {
            $('.campaign-item').fadeOut(300);
            $(`.campaign-item[data-status="${filter}"]`).fadeIn(300);
        }
    });

    // === FILL MODAL DYNAMICALLY ===
    $(document).on('click', '.see-more-btn', function() {
        const btn = $(this);
        $('#campaignModalLabel').text(btn.data('title'));
        $('#campaignModalDescription').text(btn.data('description'));
        $('#campaignModalImage').attr('src', btn.data('image'));
        $('#campaignModalType').text(btn.data('type'));
        $('#campaignModalCity').text(btn.data('city'));
        $('#campaignModalRegion').text(btn.data('region'));
        $('#campaignModalParticipants').text(btn.data('participants'));
        $('#campaignModalDeadline').text(btn.data('deadline'));
        $('#campaignModalDates').text(btn.data('dates'));
        $('#campaignModalBtn').attr('href', `/campaigns/${btn.data('id')}`);
    });
});
</script>
</body>
</html>
