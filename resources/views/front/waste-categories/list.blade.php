@extends('front.layout')

@section('content')
<div class="container py-5">
    <h1 class="mb-4 text-center text-success">Waste Categories</h1>

    <!-- Search and Filter Section -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="search-box">
                <div class="input-group">
                    <span class="input-group-text bg-success text-white">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" id="searchInput" class="form-control" placeholder="Search categories by name, description, or instructions...">
                    <button class="btn btn-outline-secondary" type="button" id="clearSearch">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="filter-section">
                <select id="sortSelect" class="form-select">
                    <option value="name_asc">Sort by: Name A-Z</option>
                    <option value="name_desc">Sort by: Name Z-A</option>
                    <option value="newest">Sort by: Newest First</option>
                    <option value="oldest">Sort by: Oldest First</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Results Info -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="results-count">
            <i class="fas fa-info-circle me-2"></i>
            <span id="resultsCount">{{ $categories->count() }}</span> category(ies) found
        </div>
        <div class="filter-tags" id="filterTags"></div>
    </div>

    @if($categories->isEmpty())
        <div class="empty-state">
            <div class="empty-state-icon">
                <i class="fas fa-folder-open"></i>
            </div>
            <h3 class="mb-3">No categories available</h3>
            <p class="text-muted mb-4">There are currently no waste categories available.</p>
        </div>
    @else
        <div class="row" id="categoriesContainer">
            @foreach($categories as $category)
                <div class="col-lg-4 col-md-6 mb-4 category-card" 
                     data-name="{{ strtolower($category->name) }}"
                     data-description="{{ strtolower($category->description) }}"
                     data-instructions="{{ strtolower($category->recycling_instructions) }}"
                     data-created="{{ $category->created_at->timestamp }}">
                    <div class="collection-card card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-recycle me-2"></i>{{ $category->name }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="card-text mb-3">{{ Str::limit($category->description, 100) }}</p>
                            
                            <div class="mb-3">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Instructions: {{ Str::limit($category->recycling_instructions, 80) }}
                                </small>
                            </div>

                            <div class="d-grid">
                                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#categoryModal{{ $category->id }}">
                                    <i class="fas fa-eye me-2"></i>
                                    View Details
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal for this category -->
                <div class="modal fade" id="categoryModal{{ $category->id }}" tabindex="-1" aria-labelledby="categoryModalLabel{{ $category->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header bg-success text-white">
                                <h5 class="modal-title" id="categoryModalLabel{{ $category->id }}">
                                    <i class="fas fa-recycle me-2"></i>{{ $category->name }}
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h6 class="text-success mb-3">
                                            <i class="fas fa-align-left me-2"></i>Description
                                        </h6>
                                        <p class="mb-4">{{ $category->description }}</p>

                                        <h6 class="text-success mb-3">
                                            <i class="fas fa-recycle me-2"></i>Recycling Instructions
                                        </h6>
                                        <p class="mb-4">{{ $category->recycling_instructions }}</p>

                                        @if($category->created_at)
                                            <div class="bg-light p-3 rounded">
                                                <small class="text-muted">
                                                    <i class="fas fa-calendar me-1"></i>
                                                    Created: {{ $category->created_at->format('m/d/Y') }}
                                                </small>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                    <i class="fas fa-times me-2"></i>Close
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- No Results Message -->
        <div id="noResults" class="empty-state" style="display: none;">
            <div class="empty-state-icon">
                <i class="fas fa-search"></i>
            </div>
            <h3 class="mb-3">No categories found</h3>
            <p class="text-muted mb-4">Try adjusting your search or filter criteria.</p>
            <button class="btn btn-outline-success" id="resetFilters">
                <i class="fas fa-refresh me-2"></i>Reset Filters
            </button>
        </div>
    @endif
</div>

<style>
    .collection-card {
        border: 1px solid #e0e0e0;
        border-radius: 10px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        height: 100%;
    }

    .collection-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .card-header {
        background-color: #198754;
        color: white;
        border-radius: 10px 10px 0 0 !important;
        padding: 15px 20px;
    }

    .card-title {
        margin: 0;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #6b7280;
    }

    .empty-state-icon {
        font-size: 4rem;
        color: #9ca3af;
        margin-bottom: 20px;
    }

    .results-count {
        background-color: #e8f5e8;
        padding: 10px 15px;
        border-radius: 5px;
        color: #065f46;
        font-weight: 500;
    }

    .modal-header {
        border-radius: 10px 10px 0 0;
    }

    .modal-content {
        border-radius: 10px;
        border: none;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }

    .search-box {
        margin-bottom: 0;
    }

    .filter-tags {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .filter-tag {
        background-color: #e3f2fd;
        color: #1976d2;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 0.8rem;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .filter-tag .close {
        cursor: pointer;
        margin-left: 4px;
    }

    .category-card {
        transition: all 0.3s ease;
    }

    .category-card.hidden {
        display: none;
    }

    .highlight {
        background-color: #fff3cd;
        padding: 2px 4px;
        border-radius: 3px;
    }
</style>

<!-- Script for handling modals and search/filter -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const clearSearch = document.getElementById('clearSearch');
    const sortSelect = document.getElementById('sortSelect');
    const categoriesContainer = document.getElementById('categoriesContainer');
    const categoryCards = document.querySelectorAll('.category-card');
    const resultsCount = document.getElementById('resultsCount');
    const noResults = document.getElementById('noResults');
    const filterTags = document.getElementById('filterTags');
    const resetFilters = document.getElementById('resetFilters');

    let currentSearch = '';
    let currentSort = 'name_asc';

    // Search functionality
    searchInput.addEventListener('input', function(e) {
        currentSearch = e.target.value.toLowerCase().trim();
        updateFilterTags();
        filterAndSortCategories();
    });

    // Clear search
    clearSearch.addEventListener('click', function() {
        searchInput.value = '';
        currentSearch = '';
        updateFilterTags();
        filterAndSortCategories();
    });

    // Sort functionality
    sortSelect.addEventListener('change', function(e) {
        currentSort = e.target.value;
        updateFilterTags();
        filterAndSortCategories();
    });

    // Reset all filters
    resetFilters?.addEventListener('click', function() {
        searchInput.value = '';
        sortSelect.value = 'name_asc';
        currentSearch = '';
        currentSort = 'name_asc';
        updateFilterTags();
        filterAndSortCategories();
    });

    function filterAndSortCategories() {
        let visibleCount = 0;
        let filteredCards = [];

        // Filter cards based on search
        categoryCards.forEach(card => {
            const name = card.dataset.name;
            const description = card.dataset.description;
            const instructions = card.dataset.instructions;

            const matchesSearch = !currentSearch || 
                name.includes(currentSearch) ||
                description.includes(currentSearch) ||
                instructions.includes(currentSearch);

            if (matchesSearch) {
                card.classList.remove('hidden');
                filteredCards.push(card);
                visibleCount++;
            } else {
                card.classList.add('hidden');
            }
        });

        // Sort filtered cards
        filteredCards.sort((a, b) => {
            switch(currentSort) {
                case 'name_asc':
                    return a.dataset.name.localeCompare(b.dataset.name);
                case 'name_desc':
                    return b.dataset.name.localeCompare(a.dataset.name);
                case 'newest':
                    return parseInt(b.dataset.created) - parseInt(a.dataset.created);
                case 'oldest':
                    return parseInt(a.dataset.created) - parseInt(b.dataset.created);
                default:
                    return 0;
            }
        });

        // Reorder cards in DOM
        filteredCards.forEach(card => {
            categoriesContainer.appendChild(card);
        });

        // Update results count
        resultsCount.textContent = visibleCount;

        // Show/hide no results message
        if (visibleCount === 0) {
            noResults.style.display = 'block';
            categoriesContainer.style.display = 'none';
        } else {
            noResults.style.display = 'none';
            categoriesContainer.style.display = 'flex';
        }

        // Highlight search terms
        if (currentSearch) {
            highlightSearchTerms(currentSearch);
        } else {
            removeHighlights();
        }
    }

    function highlightSearchTerms(term) {
        const cards = document.querySelectorAll('.category-card:not(.hidden)');
        cards.forEach(card => {
            const textElements = card.querySelectorAll('.card-title, .card-text, small');
            textElements.forEach(element => {
                const originalText = element.textContent;
                const regex = new RegExp(term, 'gi');
                const highlightedText = originalText.replace(regex, match => 
                    `<span class="highlight">${match}</span>`
                );
                element.innerHTML = highlightedText;
            });
        });
    }

    function removeHighlights() {
        const highlights = document.querySelectorAll('.highlight');
        highlights.forEach(highlight => {
            const parent = highlight.parentNode;
            parent.replaceChild(document.createTextNode(highlight.textContent), highlight);
            parent.normalize();
        });
    }

    function updateFilterTags() {
        filterTags.innerHTML = '';

        if (currentSearch) {
            const searchTag = document.createElement('span');
            searchTag.className = 'filter-tag';
            searchTag.innerHTML = `
                Search: "${currentSearch}"
                <span class="close" onclick="clearSearchTag('search')">×</span>
            `;
            filterTags.appendChild(searchTag);
        }

        if (currentSort && currentSort !== 'name_asc') {
            const sortText = sortSelect.options[sortSelect.selectedIndex].text;
            const sortTag = document.createElement('span');
            sortTag.className = 'filter-tag';
            sortTag.innerHTML = `
                ${sortText}
                <span class="close" onclick="clearSearchTag('sort')">×</span>
            `;
            filterTags.appendChild(sortTag);
        }
    }

    // Global function for clearing filter tags
    window.clearSearchTag = function(type) {
        if (type === 'search') {
            searchInput.value = '';
            currentSearch = '';
        } else if (type === 'sort') {
            sortSelect.value = 'name_asc';
            currentSort = 'name_asc';
        }
        updateFilterTags();
        filterAndSortCategories();
    };

    // Initialize
    filterAndSortCategories();
});
</script>
@endsection