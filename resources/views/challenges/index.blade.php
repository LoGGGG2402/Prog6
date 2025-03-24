@extends('layouts.app', ['title' => 'Challenges'])

@section('content')
<div class="row mb-3">
    <div class="col-md-12 d-flex justify-content-between align-items-center">
        <h2>Challenges</h2>
        <div class="d-flex">
            <!-- Teacher Filter Dropdown -->
            <div class="dropdown me-2">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="teacherFilterDropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    {{ $teacherId > 0 ? 'Filter: ' . $teachers->where('id', $teacherId)->first()->fullname : 'All Teachers' }}
                </button>
                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="teacherFilterDropdown">
                    <a class="dropdown-item {{ $teacherId == 0 ? 'active' : '' }}" href="{{ route('challenges.index') }}">All Teachers</a>
                    <div class="dropdown-divider"></div>
                    @foreach ($teachers as $teacher)
                        <a class="dropdown-item {{ $teacherId == $teacher->id ? 'active' : '' }}" 
                            href="{{ route('challenges.index', ['teacher_id' => $teacher->id]) }}">
                            {{ $teacher->fullname }}
                        </a>
                    @endforeach
                </div>
            </div>
            
            @if (Auth::user()->isTeacher())
            <a href="{{ route('challenges.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Create Challenge
            </a>
            @endif
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        @if (count($challenges) > 0)
            <div class="accordion" id="challengesAccordion">
                @foreach ($challenges as $index => $challenge)
                    <div class="accordion-item mb-3 border rounded">
                        <h2 class="accordion-header" id="heading{{ $challenge->id }}">
                            <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $challenge->id }}" aria-expanded="{{ $loop->first ? 'true' : 'false' }}" aria-controls="collapse{{ $challenge->id }}">
                                <div class="d-flex justify-content-between align-items-center w-100 me-2">
                                    <div>
                                        <i class="fas fa-puzzle-piece me-2"></i>
                                        Challenge #{{ $index + 1 }} by {{ $challenge->teacher->fullname }}
                                    </div>
                                    
                                    @if (Auth::user()->isStudent() && in_array($challenge->id, $answeredChallenges))
                                        <span class="badge bg-success">
                                            <i class="fas fa-check-circle me-1"></i> Solved
                                        </span>
                                    @endif
                                </div>
                            </button>
                        </h2>
                        <div id="collapse{{ $challenge->id }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" aria-labelledby="heading{{ $challenge->id }}" data-bs-parent="#challengesAccordion">
                            <div class="accordion-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="mb-3">
                                            <strong>Hint:</strong>
                                            <p>{{ $challenge->hint }}</p>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <strong>Created By:</strong>
                                            <p>{{ $challenge->teacher->fullname }}</p>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <strong>Created At:</strong>
                                            <p>{{ $challenge->created_at->format('M j, Y g:i A') }}</p>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        @if (Auth::user()->isStudent())
                                            @if (in_array($challenge->id, $answeredChallenges))
                                                <div class="alert alert-success">
                                                    <i class="fas fa-check-circle me-2"></i> You've solved this challenge!
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <a href="{{ route('challenges.download', $challenge) }}" class="btn btn-info w-100 mb-2">
                                                        <i class="fas fa-download me-1"></i> Download Challenge
                                                    </a>
                                                    
                                                    <button type="button" class="btn btn-primary w-100" 
                                                            onclick="viewChallengeContent('{{ route('challenges.content', $challenge) }}')">
                                                        <i class="fas fa-eye me-1"></i> View Content
                                                    </button>
                                                </div>
                                            @else
                                                <div class="card">
                                                    <div class="card-header">
                                                        <h5 class="mb-0">Solve Challenge</h5>
                                                    </div>
                                                    <div class="card-body">
                                                        <form action="{{ route('challenges.check', $challenge) }}" method="post">
                                                            @csrf
                                                            <div class="mb-3">
                                                                <label for="answer{{ $challenge->id }}" class="form-label">Your Answer:</label>
                                                                <input type="text" class="form-control" id="answer{{ $challenge->id }}" name="answer" required>
                                                            </div>
                                                            <button type="submit" class="btn btn-primary w-100">
                                                                <i class="fas fa-check me-1"></i> Submit Answer
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            @endif
                                        @else
                                            <!-- For teachers -->
                                            <div class="mb-3">
                                                <strong>Expected Answer:</strong>
                                                <p>{{ $challenge->result }}</p>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <a href="{{ route('challenges.download', $challenge) }}" class="btn btn-info w-100 mb-2">
                                                    <i class="fas fa-download me-1"></i> Download Challenge
                                                </a>
                                                
                                                <button type="button" class="btn btn-primary w-100" 
                                                        onclick="viewChallengeContent('{{ route('challenges.content', $challenge) }}')">
                                                    <i class="fas fa-eye me-1"></i> View Content
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i> No challenges available yet.
                @if (Auth::user()->isTeacher())
                    <a href="{{ route('challenges.create') }}" class="alert-link">Create your first challenge</a>.
                @endif
            </div>
        @endif
    </div>
</div>

<!-- Challenge Content Modal -->
<div class="modal fade" id="challengeContentModal" tabindex="-1" aria-labelledby="challengeContentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="challengeContentModalLabel">Challenge Content</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <pre id="challengeContentDisplay" class="bg-light p-3" style="max-height: 400px; overflow-y: auto;"></pre>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function viewChallengeContent(url) {
        fetch(url)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Access denied');
                }
                return response.json();
            })
            .then(data => {
                document.getElementById('challengeContentDisplay').textContent = data.content;
                var modal = new bootstrap.Modal(document.getElementById('challengeContentModal'));
                modal.show();
            })
            .catch(error => {
                alert('Error: ' + error.message);
            });
    }
</script>
@endpush
